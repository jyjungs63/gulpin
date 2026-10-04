<?php
/**
 * 교재 가격표 / 학습 열람 권한 — 원본: 가격표.xlsx → price_catalog.json (tools/price_xlsx_to_json.py)
 *
 * SGetPriceCatalog   : 로그인한 지사 그룹의 가격표 (본사는 전체)
 * SGetLearningAccess : 로그인 사용자가 볼 수 있는 영상코드와 열람 기한
 *                      소속 지사(학생 → 담당 선생 → 지사)가 주문한 교재의 영상코드를 주문일로부터 6개월간 연다.
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
    $rows = $id === '' ? array() : gpMgrQuery('SELECT id, role, mid, tid FROM gulpin_user WHERE id = ? AND status = 1 AND confirm = 1', array($id));
    if (!$rows) {
        gpMgrRespond(array('error' => '로그인이 만료되었습니다. 다시 로그인해 주세요.'), 401);
    }
    $rows[0]['role'] = (int) $rows[0]['role'];
    return $rows[0];
}

function SGetPriceCatalog($data)
{
    $actor = gpMgrActor();   // 본사 + 지사(1~5)만 주문
    $catalog = gpPriceCatalog();

    $groups = $catalog['groups'];
    if (!gpMgrIsAdmin($actor)) {
        $key = GP_PRICE_GROUP_OF[gpRoleGroup($actor['role'])] ?? '';
        $groups = isset($groups[$key]) ? array($key => $groups[$key]) : array();
    }
    gpMgrRespond(array('success' => array('groups' => $groups, 'generated' => $catalog['generated'] ?? '')));
}

function SGetLearningAccess($data)
{
    $user = gpSessionUser();
    if ($user['role'] === GP_ROLE_ADMIN) {
        gpMgrRespond(array('success' => array('all' => true, 'codes' => new stdClass())));
    }

    // 주문 계정: 본인 + 상위 단계 (학생 → 담당 선생(tid) → 지사(mid))
    $ids = array($user['id']);
    $parent = in_array($user['role'], GP_STUDENT_ROLES, true) ? $user['tid'] : $user['mid'];
    for ($depth = 0; $parent && $depth < 2; $depth++) {
        $ids[] = $parent;
        $rows = gpMgrQuery('SELECT mid FROM gulpin_user WHERE id = ?', array($parent));
        $parent = $rows[0]['mid'] ?? null;
    }

    // 교재 코드 → 영상코드 (세트는 구성품의 영상코드까지 포함)
    $videosOf = array();
    foreach (gpPriceCatalog()['groups'] as $group) {
        foreach ($group['items'] as $item) {
            $videosOf[$item['code']] = $item['videos'];
        }
    }

    $months = (int) (gpPriceCatalog()['videoAccessMonths'] ?? 6);
    $now = new DateTimeImmutable();
    $codes = array();
    $orders = gpMgrQuery(
        'SELECT por_list, rdate, confirm FROM gulpin_porlist WHERE id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')',
        $ids
    );
    foreach ($orders as $order) {
        if (in_array((int) $order['confirm'], GP_ORDER_REFUND_STATUS, true)) {
            continue;
        }
        $expires = (new DateTimeImmutable($order['rdate']))->modify("+{$months} months");
        if ($expires < $now) {
            continue;
        }
        foreach (json_decode((string) $order['por_list'], true) ?: array() as $line) {
            foreach ($videosOf[$line['uid'] ?? ''] ?? array() as $video) {
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
