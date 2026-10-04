<?php
/**
 * 지사/선생 관리(purchase/branchmgr.php) 서버 권한 정책 — role 정의는 roles.php
 *
 * 본사(9)는 지사(1~5)를, 지사는 자기 그룹 선생을 등록·관리한다.
 * 생성한 부모 id 를 mid 에 저장한다. 학생 관리는 선생 화면(kgardenmgr/institutemgr)에서 한다.
 *
 * 로그인 사용자 정보는 브라우저 값이 아니라 세션 + DB 에서만 가져온다.
 * SMethods.php(MariaDB) / SMethodsSQLite.php 공용.
 */

require_once __DIR__ . '/roles.php';

// 로그인 role => branchmgr 에서 조회/등록할 수 있는 하위 role
const GP_MGR_CHILD_ROLES = array(
    9 => GP_ROLE_CHILDREN[9],
    1 => GP_ROLE_CHILDREN[1],
    2 => GP_ROLE_CHILDREN[2],
    3 => GP_ROLE_CHILDREN[3],
    4 => GP_ROLE_CHILDREN[4],
    5 => GP_ROLE_CHILDREN[5],
);

function gpMgrRespond(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function gpMgrQuery(string $sql, array $params = array())
{
    global $conn;

    try {
        if ($conn instanceof mysqli) {
            $stmt = $conn->prepare($sql);
            if ($params) {
                $values = array_map('strval', array_values($params));
                $stmt->bind_param(str_repeat('s', count($values)), ...$values);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result === false ? $stmt->affected_rows : $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $rows;
        }

        $stmt = $conn->pdo()->prepare(sqlite_convert_sql($sql));
        $stmt->execute(array_values($params));
        return $stmt->columnCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->rowCount();
    } catch (Throwable $e) {
        error_log('[mgr_policy] ' . $e->getMessage());
        gpMgrRespond(array('error' => 'DB 처리 중 오류가 발생했습니다.'), 500);
    }
}

/** 세션 로그인 사용자를 DB 에서 다시 확인해 반환 (권한 변경/승인 해제가 즉시 반영됨) */
function gpMgrActor(): array
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

    $rows = gpMgrQuery('SELECT id, role, owner FROM gulpin_user WHERE id = ? AND status = 1 AND confirm = 1', array($id));
    if (!$rows) {
        gpMgrRespond(array('error' => '로그인이 만료되었습니다. 다시 로그인해 주세요.'), 401);
    }

    $actor = array('id' => $rows[0]['id'], 'role' => (int) $rows[0]['role'], 'owner' => (string) $rows[0]['owner']);
    if (!isset(GP_MGR_CHILD_ROLES[$actor['role']])) {
        gpMgrRespond(array('error' => '지사 관리 권한이 없습니다.'), 403);
    }
    return $actor;
}

function gpMgrIsAdmin(array $actor): bool
{
    return $actor['role'] === GP_ROLE_ADMIN;
}

/** 수정/삭제 대상 계정을 찾고 관리 권한을 확인 — 본사는 admin 외 전체, 그 외는 본인이 만든 하위 계정만 */
function gpMgrManagedTarget(array $actor, string $id): array
{
    $rows = gpMgrQuery('SELECT id, role, mid, password FROM gulpin_user WHERE id = ?', array($id));
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
function gpMgrValidate(array $item, bool $requireAll, ?string $currentPassword = null): array
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
        if (!array_key_exists($key, $item)) {
            if ($requireAll) {
                gpMgrRespond(array('error' => "{$label}을(를) 입력해 주세요."), 400);
            }
            continue;
        }
        $value = trim((string) $item[$key]);
        if ($value === '' && ($requireAll || $key === 'name' || $key === 'password')) {
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
        if (!preg_match('/^(0?A?B?T?|전체|1단계|2단계)$/u', $class)) {
            gpMgrRespond(array('error' => '과정 값이 올바르지 않습니다.'), 400);
        }
        $clean['class'] = $class;
    }
    return $clean;
}

function SShowMgr($data)
{
    $actor = gpMgrActor();
    $grade = (string) ($data['role'] ?? 'va');   // 'va' = 전체, 그 외 role 번호
    $roles = GP_MGR_CHILD_ROLES[$actor['role']];

    if ($grade !== 'va') {
        $roles = in_array((int) $grade, $roles, true) ? array((int) $grade) : array();
    }
    if (!$roles) {
        gpMgrRespond(array('success' => array()));
    }

    $sql = 'SELECT id, name, owner, password, mobile, addr, zipcode, confirm, rdate, role, etc
            FROM gulpin_user WHERE role IN (' . implode(',', array_fill(0, count($roles), '?')) . ')';
    $params = $roles;
    if (!gpMgrIsAdmin($actor)) {
        $sql .= ' AND mid = ? AND status = 1';
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

    $role = (int) ($item['role'] ?? -1);
    if (!in_array($role, GP_MGR_CHILD_ROLES[$actor['role']], true)) {
        gpMgrRespond(array('error' => '등록 권한이 없는 구분입니다.'), 403);
    }

    $id = trim((string) ($item['id'] ?? ''));
    if (!preg_match('/^[A-Za-z0-9가-힣._@-]{2,30}$/u', $id)) {
        gpMgrRespond(array('error' => '아이디는 한글/영문/숫자 2~30자로 입력해 주세요.'), 400);
    }
    $clean = gpMgrValidate($item, true);
    $class = gpMgrValidate(array('class' => $item['class'] ?? ''), false)['class'];
    $confirm = gpMgrIsAdmin($actor) && isset($item['confirm']) && (int) $item['confirm'] === 0 ? 0 : 1;

    if (gpMgrQuery('SELECT id FROM gulpin_user WHERE id = ?', array($id))) {
        gpMgrRespond(array('error' => "{$id}은(는) 이미 등록되어 있습니다. 다른 ID를 사용하세요."), 409);
    }

    gpMgrQuery(
        'INSERT INTO gulpin_user (id, name, owner, password, mobile, addr, zipcode, role, mid, confirm, etc)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        array($id, $clean['name'], $clean['owner'], $clean['password'], $clean['mobile'], $clean['addr'],
            $clean['zipcode'], $role, $actor['id'], $confirm, $class)
    );
    gpMgrRespond(array('success' => true, 'mid' => $actor['id'], 'confirm' => $confirm));
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

        // 승인 상태와 과정은 본사만 변경
        if (gpMgrIsAdmin($actor)) {
            if (isset($row['confirm'])) {
                $fields['confirm'] = (int) $row['confirm'] === 1 ? 1 : 0;
            }
        } else {
            unset($fields['class']);
        }
        if (isset($fields['class'])) {
            $fields['etc'] = $fields['class'];
            unset($fields['class']);
        }
        if ($fields) {
            $updates[$id] = $fields;
        }
    }

    foreach ($updates as $id => $fields) {
        $set = implode(', ', array_map(fn($column) => "{$column} = ?", array_keys($fields)));
        gpMgrQuery("UPDATE gulpin_user SET {$set} WHERE id = ?", array_merge(array_values($fields), array($id)));
    }
    gpMgrRespond(array('success' => true, 'count' => count($updates)));
}

function SDeleteMgr($data)
{
    $actor = gpMgrActor();
    $id = (string) ($data['id'] ?? '');
    gpMgrManagedTarget($actor, $id);

    // 하위 계정(원/선생님/학생)이 남아 있으면 삭제하지 않는다 — 고아 계정 방지
    $children = gpMgrQuery('SELECT COUNT(*) AS cnt FROM gulpin_user WHERE mid = ? OR tid = ?', array($id, $id));
    $count = (int) ($children[0]['cnt'] ?? 0);
    if ($count > 0) {
        gpMgrRespond(array('error' => "{$id} 아래에 하위 계정이 {$count}개 있어 삭제할 수 없습니다. 하위 계정을 먼저 정리해 주세요."), 409);
    }

    gpMgrQuery('DELETE FROM gulpin_user WHERE id = ?', array($id));
    gpMgrRespond(array('success' => true));
}

/** 학생관리(purchase/branchstudents.php, 조회 전용) — 본사: 전체 학생, 지사: 본인이 등록한 선생(mid)의 학생 */
function SShowBranchStudents($data)
{
    $actor = gpMgrActor();
    $sql = 'SELECT s.id, s.name, s.role, s.tid, t.name AS teacher, t.owner, s.classnm, s.step, s.status, s.rdate
            FROM gulpin_user s LEFT JOIN gulpin_user t ON t.id = s.tid
            WHERE s.role IN (' . implode(',', GP_STUDENT_ROLES) . ')';
    $params = array();
    if (!gpMgrIsAdmin($actor)) {
        $sql .= ' AND t.mid = ?';
        $params[] = $actor['id'];
    }
    $sql .= ' ORDER BY t.owner, s.tid, s.name';
    gpMgrRespond(array('success' => gpMgrQuery($sql, $params)));
}
