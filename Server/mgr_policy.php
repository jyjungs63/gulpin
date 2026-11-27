<?php
/**
 * 지사/선생 관리(purchase/branchmgr.php) 서버 권한 정책 — role 정의는 roles.php
 *
 * 본사(9)는 지사(1~5)를, 지사는 자기 그룹 선생을 등록·관리한다. 본사는 모든 선생도 조회·관리한다.
 * 생성한 부모 id 를 mid 에 저장한다. 본사가 선생을 등록할 때는 고른 소속 지사가 mid 가 된다. 학생 관리는 선생 화면(kgardenmgr/institutemgr)에서 한다.
 *
 * 로그인 사용자 정보는 브라우저 값이 아니라 세션 + DB 에서만 가져온다.
 * SMethods.php(MariaDB) / SMethodsSQLite.php 공용.
 */

require_once __DIR__ . '/roles.php';
require_once __DIR__ . '/password_policy.php';

// 로그인 role => branchmgr 에서 조회/등록할 수 있는 하위 role
const GP_MGR_CHILD_ROLES = array(
    9 => GP_ROLE_CHILDREN[9],
    1 => GP_ROLE_CHILDREN[1],
    2 => GP_ROLE_CHILDREN[2],
    3 => GP_ROLE_CHILDREN[3],
    4 => GP_ROLE_CHILDREN[4],
    5 => GP_ROLE_CHILDREN[5],
);

function gpIsHttpsRequest(): bool
{
    return in_array(strtolower((string) ($_SERVER['HTTPS'] ?? '')), ['on', '1'], true)
        || (getenv('GP_TRUST_TLS_PROXY') === '1' && ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

function gpMgrRespond(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function gpMgrQuery(string $sql, array $params = array(), bool $throwOnError = false)
{
    global $conn;

    try {
        if ($conn instanceof mysqli) {
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {   // mysqli 오류 보고가 꺼진 서버(PHP 7.2 기본값)에서도 실제 SQL 오류를 남긴다
                throw new RuntimeException('prepare: ' . $conn->error);
            }
            if ($params) {
                $values = array_map(function ($value) { return $value === null ? null : (string) $value; }, array_values($params));
                $stmt->bind_param(str_repeat('s', count($values)), ...$values);
            }
            if (!$stmt->execute()) {
                throw new RuntimeException('execute: ' . $stmt->error);
            }
            $result = $stmt->get_result();
            $rows = $result === false ? $stmt->affected_rows : $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        }

        $stmt = $conn->pdo()->prepare(sqlite_convert_sql($sql));
        $stmt->execute(array_values($params));
        return $stmt->columnCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->rowCount();
    } catch (Throwable $e) {
        if ($throwOnError) {
            throw $e;
        }
        error_log('[mgr_policy] ' . $e->getMessage());
        gpMgrRespond(array('error' => 'DB 처리 중 오류가 발생했습니다.'), 500);
    }
}

/** 세션 로그인 사용자를 DB 에서 다시 확인해 반환 (권한 변경/승인 해제가 즉시 반영됨) */
function gpMgrActor(array $alsoAllow = array()): array
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin !== '' && parse_url($origin, PHP_URL_HOST) !== parse_url('//' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST)) {
        gpMgrRespond(array('error' => '허용되지 않은 요청입니다.'), 403);
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $id = (string) ($_SESSION['user'] ?? '');
    if ($id === '') {
        gpMgrRespond(array('error' => '로그인이 만료되었습니다. 다시 로그인해 주세요.'), 401);
    }

    $rows = gpMgrQuery('SELECT * FROM gulpin_user WHERE id = ? AND status = 1 AND confirm = 1', array($id));
    if (!$rows) {
        gpMgrRespond(array('error' => '로그인이 만료되었습니다. 다시 로그인해 주세요.'), 401);
    }
    gpRequirePasswordChanged($rows[0]);

    $actor = array('id' => $rows[0]['id'], 'role' => (int) $rows[0]['role'], 'owner' => (string) $rows[0]['owner'], 'etc' => (string) $rows[0]['etc']);
    if (!isset(GP_MGR_CHILD_ROLES[$actor['role']]) && !in_array($actor['role'], $alsoAllow, true)) {
        gpMgrRespond(array('error' => '지사 관리 권한이 없습니다.'), 403);
    }
    return $actor;
}

// 주문(order.php) 가능 계정 — 본사·지사 + 홈스쿨선생(30)
const GP_ORDER_EXTRA_ROLES = array(30);

/*
 * 방문수업 과정 (gulpin_user.etc) — 지사(3)는 '전체', 교사(30)는 방문수업 / 급파한 중 하나
 *   지사장: 방문수업·급파한 교재 주문, 학습 콘텐츠 전체
 *   방문수업 교사: 방문수업 교재만, 학습 콘텐츠 이용 / 급파한 교사: 급파한 교재만, 학습 콘텐츠 이용 불가
 */
const GP_HOMESCHOOL_TEACHER_COURSES = array('방문수업', '급파한');

/** 방문수업 교사 과정 — 예전 값('전체', '홈스쿨' 등)은 방문수업으로 본다 */
function gpHomeschoolTeacherCourse(string $etc): string
{
    return $etc === '급파한' ? '급파한' : '방문수업';
}

/** 방문수업 지사·교사 등록/수정 과정 값 — 지사는 '전체' 고정, 교사는 방문수업/급파한만 */
function gpHomeschoolClass(int $role, string $class): string
{
    if ($role === 3) {
        return '전체';
    }
    if ($role === 30 && !in_array($class, GP_HOMESCHOOL_TEACHER_COURSES, true)) {
        gpMgrRespond(array('error' => '방문수업 교사의 과정은 방문수업 또는 급파한을 선택해 주세요.'), 400);
    }
    return $class;
}

/*
 * 영업(시연) 계정 — 유치원생(100) 중 과정(etc)이 '영업'인 계정. 본사만 branchmgr.php 에서 등록
 *   관리 메뉴 없음, 잼잼별·말잼별·글잼별 1호만 열람 (Server/catalog.php SGetLearningAccess)
 */
const GP_SALES_ROLE = 100;
const GP_SALES_CLASS = '영업';

function gpIsSalesAccount(int $role, string $etc): bool
{
    return $role === GP_SALES_ROLE && $etc === GP_SALES_CLASS;
}

function gpOrderActor(): array
{
    return gpMgrActor(GP_ORDER_EXTRA_ROLES);
}

function gpMgrIsAdmin(array $actor): bool
{
    return $actor['role'] === GP_ROLE_ADMIN;
}

/** gpMgrQuery 의 SELECT 결과를 mysqli_query 처럼 문자열 값(NULL 유지)으로 — 기존 응답 JSON 형식 유지용 */
function gpMgrQueryText(string $sql, array $params = array()): array
{
    return array_map(function ($row) {
        return array_map(function ($value) {
            return $value === null ? null : (string) $value;
        }, $row);
    }, gpMgrQuery($sql, $params));
}

/** 본인, 본인이 만든 하위 계정(mid), 또는 본사만 해당 계정의 학생/학습 정보를 본다 */
function gpCanView(array $user, string $id): bool
{
    if ($user['role'] === GP_ROLE_ADMIN || $id === $user['id']) {
        return true;
    }
    return (bool) gpMgrQuery('SELECT 1 FROM gulpin_user WHERE id = ? AND mid = ?', array($id, $user['id']));
}

/** 수정/삭제 대상 계정을 찾고 관리 권한을 확인 — 본사는 admin 외 전체, 그 외는 본인이 만든 하위 계정만 */
function gpMgrManagedTarget(array $actor, string $id): array
{
    $rows = gpMgrQuery('SELECT id, role, mid, password, etc FROM gulpin_user WHERE id = ?', array($id));
    if (!$rows) {
        gpMgrRespond(array('error' => "{$id} 계정을 찾을 수 없습니다."), 404);
    }

    $target = $rows[0];
    $targetRole = (int) $target['role'];
    $allowed = gpMgrIsAdmin($actor)
        ? $targetRole !== GP_ROLE_ADMIN
        : in_array($targetRole, GP_MGR_CHILD_ROLES[$actor['role']], true) && $target['mid'] === $actor['id'];

    if (!$allowed || $target['id'] === $actor['id']) {
        gpMgrRespond(array('error' => "{$id} 계정을 관리할 권한이 없습니다."), 403);
    }
    return $target;
}

/** 입력값 검증 — 컬럼 길이(gulpin_user) 와 형식. 기존 암호는 바뀐 경우에만 형식 검사 */
function gpMgrValidate(array $item, bool $requireAll, ?string $currentPassword = null, array $optional = array()): array
{
    $rules = array(
        'name'     => array(20, '유치원명/학원명'),
        'owner'    => array(30, '지사회사/가맹지점명'),
        'password' => array(30, '암호'),
        'mobile'   => array(20, '전화번호'),
        'addr'     => array(64, '주소'),
        'zipcode'  => array(12, '우편번호'),
    );

    $clean = array();
    foreach ($rules as $key => [$max, $label]) {
        $required = $requireAll && !in_array($key, $optional, true);
        if (!array_key_exists($key, $item)) {
            if ($required) {
                gpMgrRespond(array('error' => "{$label}을(를) 입력해 주세요."), 400);
            }
            continue;
        }
        $value = trim((string) $item[$key]);
        if ($value === '' && $key === 'password' && !$requireAll) {
            continue;   // 수정 시 암호를 비우면 기존 암호 유지 (재설정은 SResetPassword)
        }
        if ($value === '' && ($required || $key === 'name' || $key === 'password')) {
            gpMgrRespond(array('error' => "{$label}을(를) 입력해 주세요."), 400);
        }
        if (mb_strlen($value) > $max) {
            gpMgrRespond(array('error' => "{$label}은(는) {$max}자 이내로 입력해 주세요."), 400);
        }
        $clean[$key] = $value;
    }

    if (isset($clean['password']) && $clean['password'] === $currentPassword) {
        unset($clean['password']);
    } elseif (isset($clean['password']) && !preg_match('/^[A-Za-z0-9]{4,30}$/', $clean['password'])) {
        gpMgrRespond(array('error' => '암호는 영문/숫자 4~30자로 입력해 주세요.'), 400);
    }
    if (array_key_exists('class', $item)) {
        $class = trim((string) $item['class']);
        if (!preg_match('/^(0?A?B?T?|전체|1단계|2단계|급파한|방문수업|홈스쿨|한자파닉스|한어사|어휘잼|어휘의신)$/u', $class)) {
            gpMgrRespond(array('error' => '과정 값이 올바르지 않습니다.'), 400);
        }
        $clean['class'] = $class;
    }
    return $clean;
}

function SShowMgr($data)
{
    $actor = gpMgrActor();
    $grade = (string) ($data['role'] ?? 'va');   // 'va' = 전체, 'sales' = 영업 계정(본사만), 그 외 role 번호
    // 본사는 지사와 모든 선생을 조회
    $roles = gpMgrIsAdmin($actor) ? array_merge(GP_BRANCH_ROLES, GP_TEACHER_ROLES) : GP_MGR_CHILD_ROLES[$actor['role']];
    $withSales = gpMgrIsAdmin($actor) && ($grade === 'va' || $grade === 'sales');

    if ($grade !== 'va') {
        $roles = in_array((int) $grade, $roles, true) ? array((int) $grade) : array();
    }
    if (!$roles && !$withSales) {
        gpMgrRespond(array('success' => array()));
    }

    // 비활성(status = 0) 계정도 목록에 포함 — 화면에서 다시 활성으로 바꿀 수 있게
    $where = array();
    $params = $roles;
    if ($roles) {
        $where[] = 'role IN (' . implode(',', array_fill(0, count($roles), '?')) . ')';
    }
    if ($withSales) {
        $where[] = '(role = ? AND etc = ?)';
        array_push($params, GP_SALES_ROLE, GP_SALES_CLASS);
    }
    $sql = 'SELECT id, name, owner, mobile, addr, zipcode, confirm, rdate, role, mid, etc, status
            FROM gulpin_user WHERE (' . implode(' OR ', $where) . ')';
    if (!gpMgrIsAdmin($actor)) {
        $sql .= ' AND mid = ?';
        $params[] = $actor['id'];
    }

    $rows = array();
    foreach (gpMgrQuery($sql, $params) as $row) {
        $row['class'] = $row['etc'];
        unset($row['etc']);
        $rows[] = $row;
    }
    gpMgrRespond(array('success' => $rows));
}

function SRegistermgr($data)
{
    $actor = gpMgrActor();
    $item = is_array($data['items'] ?? null) ? $data['items'] : array();

    // 영업 계정: 본사만, 이름·아이디·암호만 받는다 (유치원생 role, 과정 '영업')
    $sales = ($item['role'] ?? '') === 'sales';
    if ($sales && !gpMgrIsAdmin($actor)) {
        gpMgrRespond(array('error' => '영업 계정은 본사에서만 등록할 수 있습니다.'), 403);
    }
    $role = $sales ? GP_SALES_ROLE : (int) ($item['role'] ?? -1);
    $mid = $actor['id'];
    if (!$sales && gpMgrIsAdmin($actor) && in_array($role, GP_TEACHER_ROLES, true)) {
        // 본사가 선생을 등록 — 고른 소속 지사(선생 role 의 바로 위 지사)를 부모로 저장
        $mid = trim((string) ($item['mid'] ?? ''));
        if (!gpMgrQuery('SELECT 1 FROM gulpin_user WHERE id = ? AND role = ? AND status = 1', array($mid, intdiv($role, 10)))) {
            gpMgrRespond(array('error' => '소속 지사를 선택해 주세요.'), 400);
        }
    } elseif (!$sales && !in_array($role, GP_MGR_CHILD_ROLES[$actor['role']], true)) {
        gpMgrRespond(array('error' => '등록 권한이 없는 구분입니다.'), 403);
    }

    $id = trim((string) ($item['id'] ?? ''));
    if (!preg_match('/^[A-Za-z0-9]{2,30}$/', $id)) {
        gpMgrRespond(array('error' => '아이디는 영문/숫자 2~30자로 입력해 주세요.'), 400);
    }
    // 홈스쿨 선생님(30)은 주소 없이 등록 (branchmgr 주소검색/우편번호/주소 비활성화)
    $optional = $sales ? array('owner', 'mobile', 'addr', 'zipcode') : ($role === 30 ? array('addr', 'zipcode') : array());
    $clean = gpMgrValidate($item, true, null, $optional);
    $clean += array('owner' => '', 'mobile' => '', 'addr' => '', 'zipcode' => '');
    $class = $sales ? GP_SALES_CLASS : gpHomeschoolClass($role, gpMgrValidate(array('class' => $item['class'] ?? ''), false)['class']);
    $confirm = gpMgrIsAdmin($actor) && isset($item['confirm']) && (int) $item['confirm'] === 0 ? 0 : 1;

    if (gpMgrQuery('SELECT id FROM gulpin_user WHERE id = ?', array($id))) {
        gpMgrRespond(array('error' => "{$id}은(는) 이미 등록되어 있습니다. 다른 ID를 사용하세요."), 409);
    }

    gpMgrQuery(
        'INSERT INTO gulpin_user (id, name, owner, password, mobile, addr, zipcode, role, mid, confirm, etc)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        array($id, $clean['name'], $clean['owner'], gpPasswordStore($role, $clean['password']), $clean['mobile'], $clean['addr'],
            $clean['zipcode'], $role, $mid, $confirm, $class)
    );
    gpPasswordCapture(array('id' => $id, 'role' => $role), $clean['password']);
    gpMgrRespond(array('success' => true, 'mid' => $mid, 'confirm' => $confirm));
}

function SShowConfirmUpdate($data)
{
    $actor = gpMgrActor();
    $items = is_array($data['items'] ?? null) ? $data['items'] : array();
    if (!$items) {
        gpMgrRespond(array('error' => '수정할 계정을 선택해 주세요.'), 400);
    }

    // 전부 검증한 뒤 수정 — 일부만 반영되는 것을 막는다
    $updates = array();
    foreach ($items as $row) {
        $id = (string) ($row['id'] ?? '');
        $target = gpMgrManagedTarget($actor, $id);
        $fields = gpMgrValidate($row, false, (string) $target['password']);
        if (isset($fields['password'])) {
            if (gpUserColumn('password_admin_view') !== null) {
                $fields['password_admin_view'] = gpPasswordAdminValue($id, (int) $target['role'], $fields['password']);
            }
            $fields['password'] = gpPasswordStore((int) $target['role'], $fields['password']);
        }

        // 승인 상태와 과정은 본사만 변경
        if (gpMgrIsAdmin($actor)) {
            if (isset($row['confirm'])) {
                $fields['confirm'] = (int) $row['confirm'] === 1 ? 1 : 0;
            }
        }
        // 영업 계정의 과정('영업')은 바꾸지 않는다 — 바뀌면 일반 유치원생이 된다
        if (!gpMgrIsAdmin($actor) || gpIsSalesAccount((int) $target['role'], (string) $target['etc'])) {
            unset($fields['class']);
        }
        if (isset($fields['class'])) {
            $fields['etc'] = gpHomeschoolClass((int) $target['role'], $fields['class']);
            unset($fields['class']);
        }
        if ($fields) {
            $updates[$id] = $fields;
        }
    }

    foreach ($updates as $id => $fields) {
        $set = implode(', ', array_map(function ($column) { return "{$column} = ?"; }, array_keys($fields)));
        gpMgrQuery("UPDATE gulpin_user SET {$set} WHERE id = ?", array_merge(array_values($fields), array($id)));
        // 학생은 선생님 아이디(tid)로 연결 — 선생님 이름·전화번호 등을 바꿔도 그대로 이어진다. 지점명만 학생에도 맞춘다
        if (isset($fields['owner'])) {
            gpMgrQuery('UPDATE gulpin_user SET owner = ? WHERE tid = ? AND role IN (' . implode(',', GP_STUDENT_ROLES) . ')',
                array($fields['owner'], $id));
        }
    }
    gpMgrRespond(array('success' => true, 'count' => count($updates)));
}

function SDeleteMgr($data)
{
    $actor = gpMgrActor();
    // 삭제는 본사만 — 지사는 비활성(SSetMgrStatus)
    if (!gpMgrIsAdmin($actor)) {
        gpMgrRespond(array('error' => '삭제는 본사에서만 할 수 있습니다. 비활성으로 처리해 주세요.'), 403);
    }
    $id = (string) ($data['id'] ?? '');
    gpMgrManagedTarget($actor, $id);

    // 하위 계정(지사 → 선생님(mid) → 학생(mid/tid)) — 본사는 함께 삭제할 수 있다. 본사 계정은 제외
    $children = array();
    $frontier = array($id);
    while ($frontier) {
        $marks = implode(',', array_fill(0, count($frontier), '?'));
        $rows = gpMgrQuery("SELECT id FROM gulpin_user WHERE (mid IN ({$marks}) OR tid IN ({$marks})) AND role <> ?",
            array_merge($frontier, $frontier, array(GP_ROLE_ADMIN)));
        $frontier = array();
        foreach ($rows as $row) {
            if ($row['id'] !== $id && !isset($children[$row['id']])) {
                $children[$row['id']] = true;
                $frontier[] = $row['id'];
            }
        }
    }
    $count = count($children);
    // 하위 계정이 있으면 먼저 개수를 알려 주고, 화면에서 확인(cascade)한 뒤에만 함께 삭제
    //   폼 전송이라 false 가 "false" 문자열로 오므로 '1' 일 때만 함께 삭제
    if ($count > 0 && (string) ($data['cascade'] ?? '') !== '1') {
        gpMgrRespond(array('error' => "{$id} 아래에 하위 계정이 {$count}개 있습니다.", 'children' => $count), 409);
    }

    $ids = array_merge(array($id), array_keys($children));
    foreach (array_chunk($ids, 500) as $chunk) {   // SQLite 바인딩 변수 한도(999) 안쪽
        gpMgrQuery('DELETE FROM gulpin_user WHERE id IN (' . implode(',', array_fill(0, count($chunk), '?')) . ')', $chunk);
    }
    gpMgrRespond(array('success' => true, 'deleted' => count($ids)));
}

/** 활성/비활성 — 비활성(status = 0)은 계정은 남기고 로그인·사용만 막는다 */
function SSetMgrStatus($data)
{
    $actor = gpMgrActor();
    $target = gpMgrManagedTarget($actor, (string) ($data['id'] ?? ''));
    $status = (int) ($data['status'] ?? -1);
    if ($status !== 0 && $status !== 1) {
        gpMgrRespond(array('error' => '상태 값이 올바르지 않습니다.'), 400);
    }
    gpMgrQuery('UPDATE gulpin_user SET status = ? WHERE id = ?', array($status, $target['id']));
    gpMgrRespond(array('success' => true, 'status' => $status));
}

/** 학생관리 — 본사: 전체 학생, 지사: 본인이 등록한 선생(mid)의 학생 */
function SShowBranchStudents($data)
{
    $actor = gpMgrActor();
    $birthday = gpUserColumn('birthday') !== null ? 's.birthday' : 'NULL AS birthday';
    $sql = 'SELECT s.id, s.name, s.role, s.tid, t.name AS teacher, t.owner, s.classnm, s.step, s.status, s.rdate, s.mobile, ' . $birthday . '
            FROM gulpin_user s LEFT JOIN gulpin_user t ON t.id = s.tid
            WHERE s.role IN (' . implode(',', GP_STUDENT_ROLES) . ')';
    $params = array();
    if (!gpMgrIsAdmin($actor)) {
        $sql .= ' AND t.mid = ?';
        $params[] = $actor['id'];
    }
    $sql .= ' ORDER BY t.owner, s.tid, s.name';
    $rows = gpMgrQuery($sql, $params);
    $teacherSql = 'SELECT id, name, owner, role FROM gulpin_user WHERE role IN (' . implode(',', GP_TEACHER_ROLES) . ') AND status = 1 AND confirm = 1';
    $teacherParams = array();
    if (!gpMgrIsAdmin($actor)) {
        $teacherSql .= ' AND mid = ?';
        $teacherParams[] = $actor['id'];
    }
    $teacherSql .= ' ORDER BY owner, name, id';
    gpMgrRespond(array('success' => $rows, 'teachers' => gpMgrQuery($teacherSql, $teacherParams)));
}

/** 학생 정보 수정 — 기존 학생과 새 담당 선생 모두 관리 범위 안인지 확인한다. */
function SSaveBranchStudent($data)
{
    $actor = gpMgrActor();
    $id = trim((string) ($data['id'] ?? ''));
    $sql = 'SELECT s.id, s.role, s.tid FROM gulpin_user s LEFT JOIN gulpin_user t ON t.id = s.tid
            WHERE s.id = ? AND s.role IN (' . implode(',', GP_STUDENT_ROLES) . ')';
    $params = array($id);
    if (!gpMgrIsAdmin($actor)) {
        $sql .= ' AND t.mid = ?';
        $params[] = $actor['id'];
    }
    $students = gpMgrQuery($sql, $params);
    if (!$students) {
        gpMgrRespond(array('error' => '학생을 관리할 권한이 없습니다.'), 403);
    }
    $student = $students[0];
    $fields = array();
    foreach (array('name' => 20, 'classnm' => 45, 'mobile' => 20, 'step' => 20) as $field => $max) {
        $fields[$field] = trim((string) ($data[$field] ?? ''));
        if (mb_strlen($fields[$field]) > $max) {
            gpMgrRespond(array('error' => '입력값이 허용 길이를 초과했습니다.'), 400);
        }
    }
    if ($fields['name'] === '') {
        gpMgrRespond(array('error' => '학생 이름을 입력해 주세요.'), 400);
    }
    $status = (string) ($data['status'] ?? '');
    if (!in_array($status, array('0', '1'), true)) {
        gpMgrRespond(array('error' => '재원/퇴원 상태를 선택해 주세요.'), 400);
    }
    $birthday = trim((string) ($data['birthday'] ?? ''));
    if ($birthday !== '' && !(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $birthday, $m)
        && (int) $m[1] >= 1900 && checkdate((int) $m[2], (int) $m[3], (int) $m[1]) && $birthday <= date('Y-m-d'))) {
        gpMgrRespond(array('error' => '생년월일을 올바르게 입력해 주세요.'), 400);
    }
    $tid = trim((string) ($data['tid'] ?? ''));
    if ($tid !== (string) $student['tid']) {
        $teacherSql = 'SELECT id FROM gulpin_user WHERE id = ? AND role = ? AND status = 1 AND confirm = 1';
        $teacherParams = array($tid, (int) ((int) $student['role'] / 10));
        if (!gpMgrIsAdmin($actor)) {
            $teacherSql .= ' AND mid = ?';
            $teacherParams[] = $actor['id'];
        }
        if ($tid === '' || !gpMgrQuery($teacherSql, $teacherParams)) {
            gpMgrRespond(array('error' => '같은 구분의 관리 가능한 담당 교사를 선택해 주세요.'), 403);
        }
    }
    $birthdayReady = gpUserColumn('birthday') !== null;
    if (!$birthdayReady && $birthday !== '') {
        gpMgrRespond(array('error' => '생년월일 컬럼이 없습니다. DB 컬럼 추가 권한을 확인해 주세요.'), 503);
    }
    $sets = 'name = ?, classnm = ?, mobile = ?, step = ?, status = ?, tid = ?';
    $values = array($fields['name'], $fields['classnm'], $fields['mobile'], $fields['step'], $status, $tid);
    if ($tid !== (string) $student['tid']) {
        $sets .= ', mid = ?';
        $values[] = $tid;
    }
    if ($birthdayReady) {
        $sets .= ", birthday = NULLIF(?, '')";
        $values[] = $birthday;
    }
    $values[] = $id;
    gpMgrQuery('UPDATE gulpin_user SET ' . $sets . ' WHERE id = ?', $values);
    gpMgrRespond(array('success' => true));
}

/** 학생 삭제 (purchase/branchstudents.php) — 범위는 SShowBranchStudents 와 같음: 본사는 전체, 지사는 본인이 등록한 선생의 학생 */
function SDeleteBranchStudent($data)
{
    $actor = gpMgrActor();
    $id = (string) ($data['id'] ?? '');
    $students = implode(',', GP_STUDENT_ROLES);

    $sql = "SELECT s.id FROM gulpin_user s LEFT JOIN gulpin_user t ON t.id = s.tid WHERE s.id = ? AND s.role IN ({$students})";
    $params = array($id);
    if (!gpMgrIsAdmin($actor)) {
        $sql .= ' AND t.mid = ?';
        $params[] = $actor['id'];
    }
    if (!gpMgrQuery($sql, $params)) {
        gpMgrRespond(array('error' => '삭제할 수 있는 학생이 아닙니다.'), 404);
    }

    gpMgrQuery("DELETE FROM gulpin_user WHERE id = ? AND role IN ({$students})", array($id));
    gpMgrRespond(array('success' => true));
}
