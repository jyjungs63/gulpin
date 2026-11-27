<?php
/**
 * 교재 가격표 / 학습 열람 권한 — 원본: 가격표.xlsx → price_catalog.json (tools/price_xlsx_to_json.py)
 *
 * SGetPriceCatalog   : 로그인한 지사 그룹의 가격표 (본사는 전체)
 * SGetLearningAccess : 로그인 사용자가 볼 수 있는 영상코드와 열람 기한
 *                      소속 지사(학생 → 담당 선생 → 지사)가 주문한 교재의 영상코드를 주문일로부터 6개월간 연다.
 *                      홈스쿨: 지사장은 전체, 선생님·학생은 완료된 교재 신청(gulpin_home_order_stat)의 교재만 완료일로부터 6개월.
 *                      급파한 과정 선생님은 이용 불가.
 *
 * SMethods.php / SMethodsSQLite.php 공용 (mgr_policy.php 다음에 require)
 */

require_once __DIR__ . '/mgr_policy.php';

// role 그룹(roles.php) => 가격표 그룹. 화상수업은 엑셀에 별도 가격표가 없어 홈스쿨 가격표를 쓴다.
const GP_PRICE_GROUP_OF = array(1 => 'kinder', 2 => 'academy', 3 => 'homeschool', 4 => 'homeschool', 5 => 'afterschool');

// 열람 권한에서 제외할 주문 상태 (99 환불처리, 999 환불완료)
const GP_ORDER_REFUND_STATUS = array(99, 999);

function gpPriceCatalog(): array
{
    static $catalog = null;
    if ($catalog === null) {
        $catalog = json_decode((string) file_get_contents(__DIR__ . '/price_catalog.json'), true) ?: array('groups' => array());
    }
    return $catalog;
}

/** 로그인 사용자 (모든 role) — 세션 + DB 로 확인 */
function gpSessionUser(): array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $id = (string) ($_SESSION['user'] ?? '');
    $rows = $id === '' ? array() : gpMgrQuery('SELECT * FROM gulpin_user WHERE id = ? AND status = 1 AND confirm = 1', array($id));
    if (!$rows) {
        gpMgrRespond(array('error' => '로그인이 만료되었습니다. 다시 로그인해 주세요.'), 401);
    }
    gpRequirePasswordChanged($rows[0]);
    return array('id' => $rows[0]['id'], 'role' => (int) $rows[0]['role'], 'mid' => $rows[0]['mid'], 'tid' => $rows[0]['tid'], 'etc' => (string) $rows[0]['etc']);
}

/** 주문할 수 있는 가격표 그룹 — 본사는 전체, 지사는 자기 그룹만 */
function gpPriceGroupsFor(array $actor): array
{
    $groups = gpPriceCatalog()['groups'];
    if (!gpMgrIsAdmin($actor)) {
        $key = GP_PRICE_GROUP_OF[gpRoleGroup($actor['role'])] ?? '';
        $groups = isset($groups[$key]) ? array($key => $groups[$key]) : array();
        // 방문수업 지사장은 방문수업·급파한 모두, 교사는 자기 과정 교재군만 (화상수업은 같은 가격표를 그대로 사용)
        if ($actor['role'] === 30 && isset($groups[$key])) {
            $course = gpHomeschoolTeacherCourse($actor['etc'] ?? '');
            $groups[$key]['items'] = array_values(array_filter($groups[$key]['items'], function ($item) use ($course) {
                return ($item['family'] ?? '') === $course;
            }));
        }
    }
    return $groups;
}

function SGetPriceCatalog($data)
{
    $actor = gpOrderActor();   // 본사 + 지사(1~5) + 홈스쿨선생(30)만 주문
    $catalog = gpPriceCatalog();

    $groups = gpPriceGroupsFor($actor);
    gpMgrRespond(array('success' => array('groups' => $groups, 'generated' => $catalog['generated'] ?? '')));
}

// 택배비: 교재 합계가 GP_FREE_SHIPPING_FROM 원 미만이면 GP_SHIPPING_FEE 원 (purchase/order.php, bookorder.php 와 같은 규칙)
const GP_SHIPPING_FEE = 4500;
const GP_FREE_SHIPPING_FROM = 100000;

/**
 * 주문서(por_list JSON)를 가격표로 다시 계산해 결제금액을 돌려준다.
 * 교재 줄(uid = 교재 코드)의 단가·수량·금액과 마지막 "총금액" 줄이 서버 계산과 다르면 400 으로 거절.
 * uid 가 빈 줄(택배비, 총금액)은 교재 금액에서 제외.
 */
function gpOrderAmount(array $actor, string $porList): int
{
    $priceOf = array();
    foreach (gpPriceGroupsFor($actor) as $group) {
        foreach ($group['items'] as $item) {
            $priceOf[$item['code']] = (int) $item['price'];
        }
    }
    $won = function ($value) {   // "120,000" / 120000 → 120000, 그 외(음수·소수·빈값) → -1
        $digits = str_replace(',', '', (string) $value);
        return ctype_digit($digits) ? (int) $digits : -1;
    };

    $lines = json_decode($porList, true);
    if (!is_array($lines) || !$lines) {
        gpMgrRespond(array('error' => '주문 내역이 올바르지 않습니다.'), 400);
    }

    $subtotal = 0;
    $books = 0;
    foreach ($lines as $line) {
        $code = (string) ($line['uid'] ?? '');
        if ($code === '') {
            continue;
        }
        $count = $won($line['count'] ?? '');
        if (!isset($priceOf[$code]) || $count < 1
            || $won($line['price'] ?? '') !== $priceOf[$code]
            || $won($line['total'] ?? '') !== $priceOf[$code] * $count) {
            gpMgrRespond(array('error' => "주문 금액이 가격표와 다릅니다. ({$code}) 새로고침 후 다시 주문해 주세요."), 400);
        }
        $subtotal += $priceOf[$code] * $count;
        $books++;
    }
    if ($books === 0) {
        gpMgrRespond(array('error' => '상품 선택을 1권 이상 해야 주문이 가능 합니다.'), 400);
    }

    $amount = $subtotal + ($subtotal < GP_FREE_SHIPPING_FROM ? GP_SHIPPING_FEE : 0);
    $last = end($lines);
    if (($last['grade'] ?? '') !== '총금액' || $won($last['total'] ?? '') !== $amount) {
        gpMgrRespond(array('error' => '총금액이 가격표와 다릅니다. 새로고침 후 다시 주문해 주세요.'), 400);
    }
    return $amount;
}

function SGetLearningAccess($data)
{
    $user = gpSessionUser();
    if ($user['role'] === GP_ROLE_ADMIN) {
        gpMgrRespond(array('success' => array('all' => true, 'codes' => new stdClass())));
    }

    // 홈스쿨지사장(3)은 전체 열람
    if ($user['role'] === 3) {
        gpMgrRespond(array('success' => array('all' => true, 'codes' => new stdClass())));
    }

    // 영업(시연) 계정(mgr_policy.php gpIsSalesAccount): 주문과 상관없이 잼잼별·말잼별·글잼별 1호만 기한 없이
    //   demo: 평소 제한 없는 잼잼별(data-video-free)도 이 계정에는 JJ 코드로 제한
    if (gpIsSalesAccount($user['role'], $user['etc'])) {
        gpMgrRespond(array('success' => array('all' => false, 'demo' => true, 'codes' => array('GJ1' => '9999-12-31', 'JJ1' => '9999-12-31', 'MJ1' => '9999-12-31'))));
    }

    // 급파한 과정 방문수업 교사는 학습 콘텐츠 이용 불가
    if ($user['role'] === 30 && gpHomeschoolTeacherCourse($user['etc']) === '급파한') {
        gpMgrRespond(array('success' => array('all' => false, 'codes' => new stdClass(), 'blocked' => true)));
    }

    // 교재 코드 → 영상코드 (세트는 구성품의 영상코드까지 포함)
    $videosOf = array();
    foreach (gpPriceCatalog()['groups'] as $group) {
        foreach ($group['items'] as $item) {
            $videosOf[$item['code']] = $item['videos'];
        }
    }

    // 열람 근거: [주문일, 교재 코드 목록]
    $sources = array();
    if (in_array($user['role'], array(30, 300), true)) {
        // 홈스쿨 선생님·학생: 지사가 완료 처리한 교재 신청만 — 학생은 본인 것, 선생님은 본인이 신청한 것. 완료일 기준
        $rows = gpMgrQuery(
            'SELECT material_code, order_date, complete_date FROM gulpin_home_order_stat WHERE status = 1 AND '
            . ($user['role'] === 30 ? 'teacher_id' : 'student_id') . ' = ?',
            array($user['id'])
        );
        foreach ($rows as $row) {
            $sources[] = array($row['complete_date'] ?: $row['order_date'], array($row['material_code']));
        }
    } else {
        // 주문 계정: 본인 + 상위 단계 (학생 → 담당 선생(tid) → 지사(mid))
        $ids = array($user['id']);
        $parent = in_array($user['role'], GP_STUDENT_ROLES, true) ? $user['tid'] : $user['mid'];
        for ($depth = 0; $parent && $depth < 2; $depth++) {
            $ids[] = $parent;
            $rows = gpMgrQuery('SELECT mid FROM gulpin_user WHERE id = ?', array($parent));
            $parent = $rows[0]['mid'] ?? null;
        }
        $orders = gpMgrQuery(
            'SELECT por_list, rdate, confirm FROM gulpin_porlist WHERE id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')',
            $ids
        );
        foreach ($orders as $order) {
            if (in_array((int) $order['confirm'], GP_ORDER_REFUND_STATUS, true)) {
                continue;
            }
            $sources[] = array($order['rdate'], array_map(function ($line) {
                return (string) ($line['uid'] ?? '');
            }, json_decode((string) $order['por_list'], true) ?: array()));
        }
    }

    $months = (int) (gpPriceCatalog()['videoAccessMonths'] ?? 6);
    $now = new DateTimeImmutable();
    $codes = array();
    foreach ($sources as list($date, $materials)) {
        $expires = (new DateTimeImmutable($date))->modify("+{$months} months");
        if ($expires < $now) {
            continue;
        }
        foreach ($materials as $material) {
            foreach ($videosOf[$material] ?? array() as $video) {
                $until = $expires->format('Y-m-d');
                if (!isset($codes[$video]) || $codes[$video] < $until) {
                    $codes[$video] = $until;
                }
            }
        }
    }
    ksort($codes);
    gpMgrRespond(array('success' => array('all' => false, 'codes' => (object) $codes)));
}
