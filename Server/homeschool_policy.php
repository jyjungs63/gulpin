<?php
/** 방문수업 선생님 전용 학생부. step=과정, material_code=선택한 교재 코드. */
require_once __DIR__ . '/student_policy.php';

// 학생의 과정 = 담당 교사의 과정(방문수업/급파한, mgr_policy.php gpHomeschoolTeacherCourse) — 단계(교재)는 그 교재군에서 고른다
function gpHomeschoolActor(): array
{
    $actor = gpTeacherActor();
    if ($actor['role'] !== 30) {
        gpMgrRespond(array('error' => '방문수업 교사 계정으로 이용해 주세요.'), 403);
    }
    $actor['course'] = gpHomeschoolTeacherCourse((string) gpMgrQuery('SELECT etc FROM gulpin_user WHERE id = ?', array($actor['id']))[0]['etc']);
    return $actor;
}

function gpHomeschoolMaterials(array $actor): array
{
    // 구성품이 있는 교재도 포함한다. 단품만 추리는 필터를 적용하지 않는다. 교사 과정의 교재군만
    return array_values(array_filter(gpPriceCatalog()['groups']['homeschool']['items'] ?? array(), function ($item) use ($actor) {
        return ($item['family'] ?? '') === $actor['course'];
    }));
}

function gpHomeschoolSchema(): void
{
    foreach (array('birth_year', 'material_code', 'birthday', 'p_mobile', 'sdate', 'edate') as $column) {
        if (gpUserColumn($column) === null) {
            gpMgrRespond(array('error' => '방문수업 학생부 DB 업데이트가 필요합니다. homeschool-students.sql을 실행해 주세요.'), 503);
        }
    }
}

function SHomeschoolStudents($data)
{
    $actor = gpHomeschoolActor();
    gpHomeschoolSchema();
    $window = gpHomeOrderWindow();
    $advanced = gpHomeschoolAutoAdvance($actor, $window);
    $rows = gpMgrQuery(
        'SELECT id, name, password AS passwd, birth_year, birthday, classnm, p_mobile, mobile,
                step AS course, material_code, sdate, edate, status
         FROM gulpin_user WHERE tid = ? AND role = 300 ORDER BY name, id', array($actor['id'])
    );
    foreach ($rows as &$row) {
        if (!$row['birth_year'] && !empty($row['birthday'])) {
            $row['birth_year'] = substr($row['birthday'], 0, 4);
        }
        $row['p_mobile'] = $row['p_mobile'] ?: ($row['mobile'] ?? '');
        $row['course'] = $actor['course'];   // 학생 과정은 교사 과정을 따른다 (이전 저장값 무시)
    }
    unset($row);
    foreach ($rows as &$row) {
        if ((string) $row['status'] !== '0') {
            $row['edate'] = null;   // 수업중 학생은 수업 종료일 없음 (이전 데이터에 남은 값도 숨김)
        }
    }
    unset($row);
    // 현재 교재의 이번 달 주문 상태: '' 미주문, pending 대기(status NULL), done 완료(status 1)
    $orders = gpMgrQuery(
        'SELECT student_id, material_code, status FROM gulpin_home_order_stat
         WHERE order_month = ? AND student_id IN (SELECT id FROM gulpin_user WHERE tid = ? AND role = 300)',
        array(gpHomeOrderNow()->format('Y-m'), $actor['id'])
    );
    $statusOf = array();
    foreach ($orders as $order) {
        $statusOf[$order['student_id'] . "\n" . $order['material_code']] = $order['status'];
    }
    foreach ($rows as &$row) {
        $key = $row['id'] . "\n" . $row['material_code'];
        $row['order_status'] = !array_key_exists($key, $statusOf) ? '' : ($statusOf[$key] === null ? 'pending' : 'done');
    }
    unset($row);
    gpMgrRespond(array('success' => $rows, 'materials' => gpHomeschoolMaterials($actor), 'course' => $actor['course'],
        'order_window' => $window, 'auto_advanced' => $advanced));
}

function gpHomeschoolValidate(array $actor, array $item): array
{
    foreach (array('course' => '과정', 'name' => '학생명', 'id' => '아이디', 'passwd' => '비번',
        'birth_year' => '출생년도', 'classnm' => '학교명', 'p_mobile' => '부모님 연락처',
        'material_code' => '단계(교재명)', 'sdate' => '수업 시작일') as $key => $label) {   // 수업 종료일은 선택 (비우면 NULL)
        if (trim((string) ($item[$key] ?? '')) === '') {
            gpMgrRespond(array('error' => $label . ' 항목은 필수 입력입니다.'), 400);
        }
    }
    $f = array();
    foreach (array('id' => 30, 'name' => 20, 'passwd' => 30, 'classnm' => 45,
        'p_mobile' => 20, 'course' => 20, 'material_code' => 64) as $key => $max) {
        $f[$key] = trim((string) ($item[$key] ?? ''));
        if (mb_strlen($f[$key]) > $max) {
            gpMgrRespond(array('error' => '입력값이 허용 길이를 초과했습니다.'), 400);
        }
    }
    if (!preg_match('/^[A-Za-z0-9]{2,30}$/', $f['id'])) {
        gpMgrRespond(array('error' => '아이디는 영문/숫자 2~30자로 입력해 주세요.'), 400);
    }
    if ($f['name'] === '' || !preg_match(GP_PW_PATTERN, $f['passwd'])) {
        gpMgrRespond(array('error' => '학생명과 비밀번호(영문/숫자 4~30자)를 입력해 주세요.'), 400);
    }
    if ($f['course'] !== $actor['course']) {
        gpMgrRespond(array('error' => "과정은 {$actor['course']}만 선택할 수 있습니다."), 400);
    }
    if (!isset(array_column(gpHomeschoolMaterials($actor), null, 'code')[$f['material_code']])) {
        gpMgrRespond(array('error' => "{$actor['course']} 교재를 선택해 주세요."), 400);
    }
    $year = trim((string) ($item['birth_year'] ?? ''));
    if ($year !== '' && (!preg_match('/^\d{4}$/', $year) || (int) $year < 1900 || (int) $year > (int) date('Y'))) {
        gpMgrRespond(array('error' => '출생년도를 올바르게 입력해 주세요.'), 400);
    }
    $f['birth_year'] = $year === '' ? null : (int) $year;
    foreach (array('sdate', 'edate') as $key) {
        $value = trim((string) ($item[$key] ?? ''));
        if ($value !== '' && !(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m)
            && (int) $m[1] >= 1900 && checkdate((int) $m[2], (int) $m[3], (int) $m[1]))) {
            gpMgrRespond(array('error' => '수업 시작일/종료일을 올바르게 입력해 주세요.'), 400);
        }
        $f[$key] = $value === '' ? null : $value;
    }
    if ($f['sdate'] && $f['edate'] && $f['edate'] < $f['sdate']) {
        gpMgrRespond(array('error' => '수업 종료일이 시작일보다 빠릅니다.'), 400);
    }
    return $f;
}

function SSaveHomeschoolStudent($data)
{
    $actor = gpHomeschoolActor();
    gpHomeschoolSchema();
    $f = gpHomeschoolValidate($actor, $data);
    // 신규 등록/수정을 구분해 중복 ID가 기존 학생 정보를 덮어쓰지 않게 한다.
    $originalId = trim((string) ($data['original_id'] ?? ''));
    if ($originalId !== '' && $originalId !== $f['id']) {
        gpMgrRespond(array('error' => '저장된 학생 아이디는 변경할 수 없습니다.'), 400);
    }
    $existing = gpMgrQuery('SELECT id, tid, role FROM gulpin_user WHERE id = ?', array($f['id']));
    if ($originalId === '' && $existing) {
        gpMgrRespond(array('error' => '이미 사용 중인 아이디입니다.'), 409);
    }
    if ($originalId !== '' && (!$existing || $existing[0]['tid'] !== $actor['id'] || (int) $existing[0]['role'] !== 300)) {
        gpMgrRespond(array('error' => '해당 학생을 수정할 권한이 없습니다.'), 403);
    }
    $values = array($f['name'], $f['passwd'], $f['birth_year'], $f['classnm'], $f['p_mobile'],
        $f['p_mobile'], $f['course'], $f['material_code'], $f['sdate'], $f['edate']);
    if ($originalId !== '') {
        // 학생명·단계(교재)·수업 시작일은 수정 불가 — 단계는 목록에서만 변경 (SSaveHomeschoolMaterial)
        gpMgrQuery('UPDATE gulpin_user SET password = ?, birth_year = ?, classnm = ?,
                    p_mobile = ?, mobile = ?, edate = CASE WHEN status = 0 THEN ? ELSE NULL END
                    WHERE id = ? AND tid = ? AND role = 300',
            array($f['passwd'], $f['birth_year'], $f['classnm'], $f['p_mobile'], $f['p_mobile'], $f['edate'], $f['id'], $actor['id']));
    } else {
        $values[9] = null;   // 신규 학생은 수업중 — 수업 종료일 없음
        gpMgrQuery('INSERT INTO gulpin_user (name, password, birth_year, classnm, p_mobile, mobile, step,
                    material_code, sdate, edate, id, tid, mid, owner, role, confirm, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 300, 1, 1)',
            array_merge($values, array($f['id'], $actor['id'], $actor['id'], $actor['owner'])));
    }
    gpMgrRespond(array('success' => true));
}

/** 담당 학생인지 확인 — 아니면 403 */
function gpHomeschoolOwnStudent(array $actor, string $id): array
{
    $rows = gpMgrQuery('SELECT id, material_code, sdate, status FROM gulpin_user WHERE id = ? AND tid = ? AND role = 300', array($id, $actor['id']));
    if (!$rows) {
        gpMgrRespond(array('error' => '해당 학생을 관리할 권한이 없습니다.'), 403);
    }
    return $rows[0];
}

/** 학생 목록에서 단계(교재)만 바로 변경 */
function SSaveHomeschoolMaterial($data)
{
    $actor = gpHomeschoolActor();
    gpHomeschoolSchema();
    $student = gpHomeschoolOwnStudent($actor, trim((string) ($data['id'] ?? '')));
    $book = array_column(gpHomeschoolMaterials($actor), null, 'code')[trim((string) ($data['material_code'] ?? ''))] ?? null;
    if (!$book) {
        gpMgrRespond(array('error' => "{$actor['course']} 교재를 선택해 주세요."), 400);
    }
    gpMgrQuery('UPDATE gulpin_user SET step = ?, material_code = ? WHERE id = ? AND tid = ? AND role = 300',
        array($actor['course'], $book['code'], $student['id'], $actor['id']));
    gpMgrRespond(array('success' => true));
}

/** 학생 목록에서 수업 종료일만 바로 변경 — 비우면 NULL. 수업종료 학생만 */
function SSaveHomeschoolEdate($data)
{
    $actor = gpHomeschoolActor();
    gpHomeschoolSchema();
    $student = gpHomeschoolOwnStudent($actor, trim((string) ($data['id'] ?? '')));
    if ((string) $student['status'] !== '0') {
        gpMgrRespond(array('error' => '수업중 학생은 수업 종료일을 입력할 수 없습니다.'), 400);
    }
    $edate = trim((string) ($data['edate'] ?? ''));
    if ($edate !== '' && !(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $edate, $m)
        && (int) $m[1] >= 1900 && checkdate((int) $m[2], (int) $m[3], (int) $m[1]))) {
        gpMgrRespond(array('error' => '수업 종료일을 올바르게 입력해 주세요.'), 400);
    }
    if ($edate !== '' && !empty($student['sdate']) && $edate < $student['sdate']) {
        gpMgrRespond(array('error' => '수업 종료일이 시작일보다 빠릅니다.'), 400);
    }
    gpMgrQuery('UPDATE gulpin_user SET edate = ? WHERE id = ? AND tid = ? AND role = 300',
        array($edate === '' ? null : $edate, $student['id'], $actor['id']));
    gpMgrRespond(array('success' => true));
}

/** 수업중(status 1) / 수업종료(status 0) — 수업종료 학생은 로그인할 수 없다. 수업종료 시 오늘을 수업 종료일로, 수업중이면 종료일 비움 */
function SSaveHomeschoolStatus($data)
{
    $actor = gpHomeschoolActor();
    $student = gpHomeschoolOwnStudent($actor, trim((string) ($data['id'] ?? '')));
    $status = (string) ($data['status'] ?? '');
    if (!in_array($status, array('0', '1'), true)) {
        gpMgrRespond(array('error' => '수업 상태를 선택해 주세요.'), 400);
    }
    if ($status === '0') {
        gpMgrQuery('UPDATE gulpin_user SET status = 0, edate = ? WHERE id = ? AND tid = ? AND role = 300',
            array(gpHomeOrderNow()->format('Y-m-d'), $student['id'], $actor['id']));
    } else {
        gpMgrQuery('UPDATE gulpin_user SET status = 1, edate = NULL WHERE id = ? AND tid = ? AND role = 300', array($student['id'], $actor['id']));
    }
    gpMgrRespond(array('success' => true));
}

/*
 * 방문수업 교재 주문 (gulpin_home_order_stat)
 *   선생님(30) 학생관리 '교재 주문' → 학생 현재 교재로 신청 (학생·교재당 월 1건, UNIQUE student_id + order_month + material_code)
 *   지사장(3) order.php → 신청 조회 / 주문 불러오기 → 구매내역서 저장 시 완료, 또는 선택 완료 처리
 *   status: NULL 대기, 1 완료
 */
function gpHomeOrderNow(): DateTime
{
    return new DateTime('now', new DateTimeZone('Asia/Seoul'));
}

/*
 * 교재 신청 기간 — 매월 시작일~종료일에만 신청 가능 (기본 10~15일).
 *   본사가 주문현황(homeorderstatus.php)에서 바꾼다 — gulpin_setting (_private/dbinit.php 가 생성)
 */
const GP_HOME_ORDER_DAYS = array('home_order_start_day' => 10, 'home_order_end_day' => 15);

function gpSettingTableReady(): bool
{
    global $conn;
    $sql = $conn instanceof mysqli
        ? "SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gulpin_setting'"
        : "SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = 'gulpin_setting'";
    return (bool) gpMgrQuery($sql);
}

function gpHomeOrderWindow(): array
{
    $days = GP_HOME_ORDER_DAYS;
    if (gpSettingTableReady()) {
        $keys = array_keys($days);
        foreach (gpMgrQuery('SELECT skey, svalue FROM gulpin_setting WHERE skey IN (?, ?)', $keys) as $row) {
            $days[$row['skey']] = (int) $row['svalue'];
        }
    }
    $start = $days['home_order_start_day'];
    $end = $days['home_order_end_day'];
    $day = (int) gpHomeOrderNow()->format('j');
    return array('start' => $start, 'end' => $end, 'open' => $day >= $start && $day <= $end);
}

/** 신청 기간 조회 — 지사(3)·선생님(30)·본사 */
function SHomeOrderWindow($data)
{
    gpMgrActor(array(30));
    gpMgrRespond(array('success' => gpHomeOrderWindow()));
}

/** 신청 기간 변경 — 본사만 */
function SSaveHomeOrderWindow($data)
{
    $actor = gpMgrActor();
    if (!gpMgrIsAdmin($actor)) {
        gpMgrRespond(array('error' => '교재 신청 기간은 본사에서만 바꿀 수 있습니다.'), 403);
    }
    $start = (int) ($data['start'] ?? 0);
    $end = (int) ($data['end'] ?? 0);
    if ($start < 1 || $end > 31 || $start > $end) {
        gpMgrRespond(array('error' => '시작일과 종료일을 1~31일 사이로, 시작일이 종료일보다 늦지 않게 입력해 주세요.'), 400);
    }
    if (!gpSettingTableReady()) {
        gpMgrRespond(array('error' => '설정 테이블이 없습니다. doc/homeschool-order-window.sql을 실행해 주세요.'), 503);
    }
    foreach (array('home_order_start_day' => $start, 'home_order_end_day' => $end) as $key => $value) {
        gpMgrQuery('DELETE FROM gulpin_setting WHERE skey = ?', array($key));
        gpMgrQuery('INSERT INTO gulpin_setting (skey, svalue) VALUES (?, ?)', array($key, (string) $value));
    }
    gpMgrRespond(array('success' => gpHomeOrderWindow()));
}

/** 같은 교재군의 다음 단계 교재 코드 (가격표 순서) — 마지막 단계면 null */
function gpHomeschoolNextMaterial(array $actor, string $code): ?string
{
    $items = gpHomeschoolMaterials($actor);
    foreach ($items as $i => $item) {
        if ($item['code'] === $code) {
            $next = $items[$i + 1] ?? null;
            return $next && $next['family'] === $item['family'] ? $next['code'] : null;
        }
    }
    return null;
}

/**
 * 신청 기간에 처음 학생관리를 열면 학생별 단계를 지난달 신청 교재의 다음 단계로 바꾼다 (학생당 월 1회).
 *   지난달 신청 뒤 단계를 이미 바꿨거나, 이번 달에 이미 신청한 학생은 그대로 둔다.
 *   material_auto_month 에 처리한 달을 남겨 선생님이 고친 단계를 다시 덮어쓰지 않는다.
 */
function gpHomeschoolAutoAdvance(array $actor, array $window): int
{
    if (!$window['open'] || gpUserColumn('material_auto_month') === null) {
        return 0;
    }
    $now = gpHomeOrderNow();
    $month = $now->format('Y-m');
    $prev = (clone $now)->modify('first day of last month')->format('Y-m');
    $students = gpMgrQuery('SELECT id, material_code FROM gulpin_user
                            WHERE tid = ? AND role = 300 AND status = 1 AND (material_auto_month IS NULL OR material_auto_month <> ?)',
        array($actor['id'], $month));
    if (!$students) {
        return 0;
    }
    $lastPrev = array();
    $orderedNow = array();
    foreach (gpMgrQuery('SELECT student_id, material_code, order_month FROM gulpin_home_order_stat
                         WHERE order_month IN (?, ?) AND student_id IN (SELECT id FROM gulpin_user WHERE tid = ? AND role = 300)
                         ORDER BY order_date, id', array($prev, $month, $actor['id'])) as $order) {
        if ($order['order_month'] === $month) {
            $orderedNow[$order['student_id']] = true;
        } else {
            $lastPrev[$order['student_id']] = $order['material_code'];
        }
    }
    $advanced = 0;
    foreach ($students as $student) {
        $code = (string) $student['material_code'];
        $next = !isset($orderedNow[$student['id']]) && ($lastPrev[$student['id']] ?? null) === $code
            ? gpHomeschoolNextMaterial($actor, $code) : null;
        if ($next !== null) {
            gpMgrQuery('UPDATE gulpin_user SET material_code = ?, material_auto_month = ? WHERE id = ? AND tid = ? AND role = 300',
                array($next, $month, $student['id'], $actor['id']));
            $advanced++;
        } else {
            gpMgrQuery('UPDATE gulpin_user SET material_auto_month = ? WHERE id = ? AND tid = ? AND role = 300',
                array($month, $student['id'], $actor['id']));
        }
    }
    return $advanced;
}

function SHomeschoolOrder($data)
{
    $actor = gpHomeschoolActor();
    gpHomeschoolSchema();
    $window = gpHomeOrderWindow();
    if (!$window['open']) {
        gpMgrRespond(array('error' => "교재 신청은 매월 {$window['start']}일~{$window['end']}일에만 할 수 있습니다."), 403);
    }
    $student = gpHomeschoolOwnStudent($actor, trim((string) ($data['id'] ?? '')));
    $book = array_column(gpHomeschoolMaterials($actor), null, 'code')[(string) $student['material_code']] ?? null;
    if (!$book) {
        gpMgrRespond(array('error' => '단계(교재명)를 먼저 선택해 주세요.'), 400);
    }
    $branch = gpMgrQuery('SELECT b.id FROM gulpin_user t JOIN gulpin_user b ON b.id = t.mid AND b.role = 3 WHERE t.id = ?', array($actor['id']));
    if (!$branch) {
        gpMgrRespond(array('error' => '소속 방문수업 지사가 없어 주문할 수 없습니다.'), 400);
    }
    $now = gpHomeOrderNow();
    if (gpMgrQuery('SELECT 1 FROM gulpin_home_order_stat WHERE student_id = ? AND order_month = ? AND material_code = ?',
        array($student['id'], $now->format('Y-m'), $book['code']))) {
        gpMgrRespond(array('error' => '이번 달에 이미 이 교재를 주문했습니다.'), 409);
    }
    gpMgrQuery('INSERT INTO gulpin_home_order_stat (branch_id, teacher_id, student_id, material_title, material_code, order_date, order_month)
                VALUES (?, ?, ?, ?, ?, ?, ?)',
        array($branch[0]['id'], $actor['id'], $student['id'], $book['title'], $book['code'], $now->format('Y-m-d H:i:s'), $now->format('Y-m')));
    gpMgrRespond(array('success' => true));
}

function gpHomeOrderBranchActor(): array
{
    $actor = gpMgrActor();
    if ($actor['role'] !== 3) {
        gpMgrRespond(array('error' => '방문수업 지사 계정으로 이용해 주세요.'), 403);
    }
    return $actor;
}

/** 선택 월(YYYY-MM, 기본 이번 달)의 선생님 교재 주문 — 지사장(3)은 소속 지사만, 본사(9)는 전체 조회 */
function SHomeOrderList($data)
{
    $actor = gpMgrActor();
    if ($actor['role'] !== 3 && !gpMgrIsAdmin($actor)) {
        gpMgrRespond(array('error' => '방문수업 지사 계정으로 이용해 주세요.'), 403);
    }
    $month = (string) ($data['month'] ?? '');
    if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
        $month = gpHomeOrderNow()->format('Y-m');
    }
    $params = array($month);
    $branchFilter = '';
    if (!gpMgrIsAdmin($actor)) {
        $branchFilter = ' AND o.branch_id = ?';
        $params[] = $actor['id'];
    }
    $rows = gpMgrQueryText(
        'SELECT o.id, o.order_date, o.complete_date, o.status, o.material_title, o.material_code,
                o.branch_id, b.owner AS branch_owner, b.name AS branch_name,
                o.teacher_id, t.name AS teacher_name, o.student_id, s.name AS student_name
         FROM gulpin_home_order_stat o
         LEFT JOIN gulpin_user b ON b.id = o.branch_id
         LEFT JOIN gulpin_user t ON t.id = o.teacher_id
         LEFT JOIN gulpin_user s ON s.id = o.student_id
         WHERE o.order_month = ?' . $branchFilter . '
         ORDER BY o.order_date, o.id', $params
    );
    gpMgrRespond(array('success' => $rows, 'month' => $month));
}

/** 주문현황(homeorderstatus.php): 전체 기간 교재 주문 — 본사(9) 전체, 방문수업 지사장(3) 소속 지사, 선생님(30) 본인 신청만 */
function SHomeOrderHistory($data)
{
    $actor = gpMgrActor(array(30));
    if (!gpMgrIsAdmin($actor) && $actor['role'] !== 3 && $actor['role'] !== 30) {
        gpMgrRespond(array('error' => '방문수업 지사 또는 교사 계정으로 이용해 주세요.'), 403);
    }
    $where = '';
    $params = array();
    if (!gpMgrIsAdmin($actor)) {
        $where = ' WHERE ' . ($actor['role'] === 3 ? 'o.branch_id' : 'o.teacher_id') . ' = ?';
        $params[] = $actor['id'];
    }
    $rows = gpMgrQueryText(
        'SELECT o.id, o.order_date, o.complete_date, o.status, o.order_month, o.material_title, o.material_code,
                o.branch_id, b.owner AS branch_owner, b.name AS branch_name,
                o.teacher_id, t.name AS teacher_name, o.student_id, s.name AS student_name
         FROM gulpin_home_order_stat o
         LEFT JOIN gulpin_user b ON b.id = o.branch_id
         LEFT JOIN gulpin_user t ON t.id = o.teacher_id
         LEFT JOIN gulpin_user s ON s.id = o.student_id' . $where . '
         ORDER BY o.order_date, o.id', $params
    );
    gpMgrRespond(array('success' => $rows));
}

/** 대기 주문을 완료(status 1, 완료일 지금)로 — 지사장 본인 지사 것만. $codes 가 있으면 그 교재 코드만 */
function gpHomeOrderComplete(array $actor, array $ids, ?array $codes = null): int
{
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids), function ($id) { return $id > 0; })));
    if (!$ids || $codes === array()) {
        return 0;
    }
    $sql = 'UPDATE gulpin_home_order_stat SET status = 1, complete_date = ?
            WHERE branch_id = ? AND status IS NULL AND id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')';
    $params = array_merge(array(gpHomeOrderNow()->format('Y-m-d H:i:s'), $actor['id']), $ids);
    if ($codes !== null) {
        $sql .= ' AND material_code IN (' . implode(',', array_fill(0, count($codes), '?')) . ')';
        $params = array_merge($params, $codes);
    }
    return (int) gpMgrQuery($sql, $params);
}

function SHomeOrderComplete($data)
{
    $actor = gpHomeOrderBranchActor();
    $count = gpHomeOrderComplete($actor, (array) ($data['ids'] ?? array()));
    gpMgrRespond(array('success' => $count));
}

/** order.php 구매내역서 저장(SUploadBoardPDF) 후: 불러온 주문 중 주문서에 들어간 교재만 완료 처리 */
function gpHomeOrderCompleteFromPurchase(array $actor, string $porList, string $idsJson): void
{
    $ids = json_decode($idsJson, true);
    if ($actor['role'] !== 3 || !is_array($ids) || !$ids) {
        return;
    }
    $codes = array();
    foreach ((array) json_decode($porList, true) as $line) {
        if (is_array($line) && (string) ($line['uid'] ?? '') !== '' && (int) str_replace(',', '', (string) ($line['count'] ?? '')) > 0) {
            $codes[] = (string) $line['uid'];
        }
    }
    gpHomeOrderComplete($actor, $ids, array_values(array_unique($codes)));
}
