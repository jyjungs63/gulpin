<?php
/**
 * 비밀번호 저장/확인/재설정 — SMethods.php / SMethodsSQLite.php 공용 (mgr_policy.php 에서 require)
 *
 * 본사·지사·선생님: 해시(password_hash) 저장. 학생: 선생님 화면에서 보고 출력해야 하므로 평문 유지.
 * 기존 평문 암호는 로그인에 성공하는 순간 해시로 바꾼다 (사용자가 다시 정할 필요 없음).
 * PG 도입 전 관리자 확인은 별도 암호화 사본만 사용한다. 로그인용 해시는 그대로 유지한다.
 *
 * 분실 시 재설정
 *   - 상위 계정(본사 → 지사, 지사 → 선생님, 본사 → 전체): SResetPassword — 임시 비밀번호 발급
 *   - 임시 비밀번호로 로그인하면(pw_temp = 1) 새 비밀번호로 바꾸기 전에는 다른 기능을 쓸 수 없다 (SChangePassword)
 */

// 암호 형식 — 기존 등록 화면(branchmgr.php)·학생관리와 같은 규칙
const GP_PW_PATTERN = '/^[A-Za-z0-9]{4,30}$/';
const GP_PW_RULE_TEXT = '비밀번호는 영문/숫자 4~30자로 입력해 주세요.';

if (!defined('GULPIN_PASSWORD_POLICY')) {
    define('GULPIN_PASSWORD_POLICY', true);
}

/** PG 적용 전의 관리자 확인 기능. 설정 파일이 없으면 비활성화한다. */
function gpPasswordViewEnabled(): bool
{
    $override = getenv('GP_PASSWORD_VIEW_ENABLED');
    $path = __DIR__ . '/../_private/password_view.php';
    $config = is_file($path) ? require $path : array();
    return ($config['enabled'] ?? false) === true && ($override === false || $override === '1');
}

function gpPasswordViewKey(): ?string
{
    $path = __DIR__ . '/../_private/password_view_key.php';
    $encoded = getenv('GP_PASSWORD_VIEW_KEY') ?: (is_file($path) ? require $path : '');
    $key = base64_decode((string) $encoded, true);
    return is_string($key) && strlen($key) === 32 ? $key : null;
}

/** 관리자에게만 보여 주는 저장 환경 진단. 키 내용은 반환하지 않는다. */
function gpPasswordViewProblem(): ?string
{
    if (!gpPasswordViewEnabled()) {
        return '비밀번호 확인 기능이 꺼져 있습니다. _private/password_view.php의 enabled와 GP_PASSWORD_VIEW_ENABLED 설정을 확인해 주세요.';
    }
    if (gpPasswordViewKey() === null) {
        return '암호화 키를 읽을 수 없습니다. _private/password_view_key.php 업로드와 읽기 권한, GP_PASSWORD_VIEW_KEY 설정을 확인해 주세요. 키는 기존 파일을 그대로 사용해야 합니다.';
    }
    if (!function_exists('openssl_encrypt') || !function_exists('openssl_decrypt')
        || !in_array('aes-256-gcm', openssl_get_cipher_methods(), true)) {
        return '서버가 AES-256-GCM 암호화를 지원하지 않습니다. 호스팅의 PHP OpenSSL 지원을 확인해 주세요.';
    }
    return null;
}

/** 로그인용 해시는 유지하고 별도 사본만 AES-256-GCM으로 암호화한다. */
function gpPasswordAdminValue(string $id, int $role, string $plain): ?string
{
    if (!gpPasswordViewEnabled() || $role === GP_ROLE_ADMIN || !gpPasswordHashed($role)) {
        return null;
    }
    $problem = gpPasswordViewProblem();
    if ($problem !== null) {
        error_log('[password_view] ' . $problem);
        return null;
    }
    $key = gpPasswordViewKey();
    $iv = random_bytes(12);
    $tag = '';
    $encrypted = openssl_encrypt($plain, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, $id);
    if ($encrypted === false) {
        error_log('[password_view] AES-256-GCM encryption failed');
        return null;
    }
    return base64_encode($iv . $tag . $encrypted);
}

function gpPasswordAdminDecode(string $id, string $stored): ?string
{
    $key = gpPasswordViewKey();
    $raw = base64_decode($stored, true);
    if ($key === null || $raw === false || strlen($raw) < 28 || !function_exists('openssl_decrypt')) {
        return null;
    }
    $plain = openssl_decrypt(substr($raw, 28), 'aes-256-gcm', $key, OPENSSL_RAW_DATA,
        substr($raw, 0, 12), substr($raw, 12, 16), $id);
    return $plain === false ? null : $plain;
}

function gpPasswordCapture(array $user, string $plain): string
{
    $label = '[password_view] account=' . json_encode((string) $user['id']) . ' ';
    if ((int) $user['role'] === GP_ROLE_ADMIN || !gpPasswordHashed((int) $user['role'])) {
        return 'excluded_role';
    }
    if (!gpPasswordViewEnabled()) {
        error_log($label . 'capture=disabled');
        return 'disabled';
    }
    if (gpUserColumn('password_admin_view') === null) {
        error_log($label . 'capture=column_missing');
        return 'column_missing';
    }
    $encrypted = gpPasswordAdminValue((string) $user['id'], (int) $user['role'], $plain);
    // 설정/키/암호화 실패를 빈 값으로 UPDATE하면 이전 사본까지 지워진다.
    // 로그인은 유지하고, 암호화 성공 시에만 저장한다. PG 종료 시 삭제는 명시적 SQL로 한다.
    if ($encrypted !== null) {
        gpMgrQuery('UPDATE gulpin_user SET password_admin_view = ? WHERE id = ?', array($encrypted, $user['id']));
        $rows = gpMgrQuery('SELECT password_admin_view FROM gulpin_user WHERE id = ?', array($user['id']));
        if (!$rows || !hash_equals($encrypted, (string) ($rows[0]['password_admin_view'] ?? ''))) {
            error_log($label . 'capture=write_mismatch');
            return 'write_mismatch';
        }
        error_log($label . 'capture=saved');
        return 'saved';
    }
    error_log($label . 'capture=encryption_failed');
    return 'encryption_failed';
}

/** 해시로 저장하는 계정인지 — 학생만 평문 */
function gpPasswordHashed(int $role): bool
{
    return !in_array($role, GP_STUDENT_ROLES, true);
}

/** gulpin_user 에 컬럼이 있는지 (dbinit 마이그레이션 전/권한 부족 대비) */
function gpUserColumn(string $column): ?array
{
    static $columns = null;
    global $conn;
    if ($columns === null) {
        $columns = array();
        if ($conn instanceof mysqli) {
            $rows = gpMgrQuery("SELECT COLUMN_NAME AS name, CHARACTER_MAXIMUM_LENGTH AS len FROM information_schema.COLUMNS
                                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gulpin_user'");
        } else {
            $rows = gpMgrQuery('PRAGMA table_info(gulpin_user)');
        }
        foreach ($rows as $row) {
            $columns[$row['name']] = $row;
        }
    }
    return $columns[$column] ?? null;
}

/** 해시(60자 이상)를 담을 수 있는지 — 아직 varchar(30) 이면 평문으로 둬서 로그인이 깨지지 않게 */
function gpPasswordHashReady(): bool
{
    $column = gpUserColumn('password');
    if ($column === null) {
        return false;
    }
    return !isset($column['len']) || (int) $column['len'] >= 60;   // SQLite TEXT 는 길이 제한 없음
}

/** 저장할 값 — 해시 대상 계정이면 해시 */
function gpPasswordStore(int $role, string $plain): string
{
    return gpPasswordHashed($role) && gpPasswordHashReady() ? password_hash($plain, PASSWORD_DEFAULT) : $plain;
}

function gpPasswordIsHash(string $stored): bool
{
    return password_get_info($stored)['algoName'] !== 'unknown';
}

/** 로그인 확인 — 평문으로 맞으면(기존 계정) 해시로 바꿔 저장 */
function gpPasswordCheck(array $user, string $plain): bool
{
    $stored = (string) $user['password'];
    if ((int) ($user['pw_temp'] ?? 0) === 1) {
        $plain = trim($plain);   // 임시 비밀번호(영문/숫자)는 복사할 때 붙는 앞뒤 공백을 무시
    }
    if (gpPasswordIsHash($stored)) {
        if (!password_verify($plain, $stored)) {
            return false;
        }
        return true;
    }
    if ($stored === '' || !hash_equals($stored, $plain)) {
        return false;
    }
    $store = gpPasswordStore((int) $user['role'], $plain);
    if ($store !== $stored) {
        gpMgrQuery('UPDATE gulpin_user SET password = ? WHERE id = ?', array($store, $user['id']));
    }
    return true;
}

/** 비밀번호 저장 + 임시 여부(pw_temp) */
function gpPasswordSave(string $id, int $role, string $plain, bool $temp): void
{
    $set = 'password = ?';
    $params = array(gpPasswordStore($role, $plain));
    if (gpUserColumn('pw_temp') !== null) {
        $set .= ', pw_temp = ?';
        $params[] = $temp ? 1 : 0;
    }
    if (gpUserColumn('password_admin_view') !== null) {
        $set .= ', password_admin_view = ?';
        $params[] = gpPasswordAdminValue($id, $role, $plain);
    }
    $params[] = $id;
    gpMgrQuery('UPDATE gulpin_user SET ' . $set . ' WHERE id = ?', $params);
}

/** 로그인한 본사 관리자 세션으로 한 계정만 조회. 일반 목록 응답에는 포함하지 않는다. */
function SAdminViewPassword($data)
{
    header('Cache-Control: no-store, private');
    $actor = gpMgrActor();
    if (!gpMgrIsAdmin($actor) || !gpPasswordViewEnabled()) {
        gpMgrRespond(array('error' => '비밀번호 확인 권한이 없거나 확인 기능이 종료되었습니다.'), 403);
    }
    $problem = gpPasswordViewProblem();
    if ($problem !== null) {
        gpMgrRespond(array('error' => $problem), 503);
    }
    $target = gpMgrManagedTarget($actor, (string) ($data['id'] ?? ''));
    if (gpUserColumn('password_admin_view') === null) {
        gpMgrRespond(array('error' => '관리자 확인용 DB 컬럼이 없습니다. 배포 SQL을 실행해 주세요.'), 503);
    }
    $rows = gpMgrQuery('SELECT password_admin_view FROM gulpin_user WHERE id = ?', array($target['id']));
    $plain = gpPasswordAdminDecode($target['id'], (string) ($rows[0]['password_admin_view'] ?? ''));
    if ($plain === null) {
        if (!empty($rows[0]['password_admin_view'])) {
            gpMgrRespond(array('error' => '저장된 사본을 현재 암호화 키로 해독하지 못했습니다. 저장 당시의 password_view_key.php 파일을 확인하거나 해당 사용자로 다시 로그인해 주세요.'), 409);
        }
        gpMgrRespond(array('error' => '확인 가능한 비밀번호가 없습니다. 기능 적용 후 해당 사용자의 로그인 또는 비밀번호 변경이 필요합니다.'), 409);
    }
    // 오래된 사본이 노출되지 않도록 현재 로그인용 해시와도 일치하는지 확인한다.
    $password = (string) $target['password'];
    if (!(gpPasswordIsHash($password) ? password_verify($plain, $password) : hash_equals($password, $plain))) {
        gpMgrRespond(array('error' => '비밀번호가 변경되었습니다. 해당 사용자의 재로그인이 필요합니다.'), 409);
    }
    error_log('[password_view] admin=' . $actor['id'] . ' target=' . $target['id']);
    gpMgrRespond(array('success' => true, 'password' => $plain));
}

/**
 * 관리 목록(branchmgr.php)에 비밀번호를 바로 표시 — 본사는 admin 외 전체, 지사는 본인이 등록한 하위 계정만.
 * 확인용 사본이 없거나 현재 비밀번호와 다르면 응답에서 빠진다 (화면은 재설정 버튼 표시).
 */
function SViewManagedPasswords($data)
{
    header('Cache-Control: no-store, private');
    $actor = gpMgrActor();
    if (gpPasswordViewProblem() !== null || gpUserColumn('password_admin_view') === null) {
        gpMgrRespond(array('success' => array()));
    }
    $ids = array();
    foreach ((is_array($data['ids'] ?? null) ? $data['ids'] : array()) as $id) {
        if (is_string($id) && $id !== '' && $id !== $actor['id']) {
            $ids[$id] = true;
        }
    }
    $ids = array_slice(array_keys($ids), 0, 500);   // SQLite 바인딩 변수 한도(999) 안쪽
    if (!$ids) {
        gpMgrRespond(array('success' => array()));
    }

    $rows = gpMgrQuery('SELECT id, role, mid, password, password_admin_view FROM gulpin_user
                        WHERE id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')', $ids);
    $passwords = array();
    foreach ($rows as $target) {
        $targetRole = (int) $target['role'];
        $allowed = gpMgrIsAdmin($actor)
            ? $targetRole !== GP_ROLE_ADMIN
            : in_array($targetRole, GP_MGR_CHILD_ROLES[$actor['role']], true) && $target['mid'] === $actor['id'];
        if (!$allowed || empty($target['password_admin_view'])) {
            continue;
        }
        $plain = gpPasswordAdminDecode($target['id'], (string) $target['password_admin_view']);
        $password = (string) $target['password'];
        // 오래된 사본이 노출되지 않도록 현재 로그인용 해시와도 일치하는지 확인한다.
        if ($plain !== null && (gpPasswordIsHash($password) ? password_verify($plain, $password) : hash_equals($password, $plain))) {
            $passwords[$target['id']] = $plain;
        }
    }
    error_log('[password_view] list actor=' . $actor['id'] . ' count=' . count($passwords));
    gpMgrRespond(array('success' => $passwords));
}

/** 임시 비밀번호 — 헷갈리는 글자(0 O 1 l I) 제외 10자 */
function gpTempPassword(): string
{
    $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    do {
        $pw = '';
        for ($i = 0; $i < 10; $i++) {
            $pw .= $chars[random_int(0, strlen($chars) - 1)];
        }
    } while (!preg_match('/[A-Za-z]/', $pw) || !preg_match('/[0-9]/', $pw));
    return $pw;
}

/** 임시 비밀번호로 로그인한 상태면 변경 전까지 막는다 (gpMgrActor / gpSessionUser / gpTeacherActor 에서 호출) */
function gpRequirePasswordChanged(array $userRow): void
{
    if ((int) ($userRow['pw_temp'] ?? 0) === 1) {
        gpMgrRespond(array('error' => '임시 비밀번호로 로그인했습니다. 새 비밀번호로 변경해 주세요.', 'mustChange' => true), 403);
    }
}

/** 로그인한 본인의 비밀번호 변경 (login/changepw.php) */
function SChangePassword($data)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $id = (string) ($_SESSION['user'] ?? '');
    $rows = $id === '' ? array() : gpMgrQuery('SELECT * FROM gulpin_user WHERE id = ? AND status = 1 AND confirm = 1', array($id));
    if (!$rows) {
        gpMgrRespond(array('error' => '로그인이 만료되었습니다. 다시 로그인해 주세요.'), 401);
    }
    $user = $rows[0];
    $current = (string) ($data['current'] ?? '');
    $next = trim((string) ($data['next'] ?? ''));

    if (!gpPasswordCheck($user, $current)) {
        gpMgrRespond(array('error' => '현재(임시) 비밀번호가 맞지 않습니다.'), 400);
    }
    if (!preg_match(GP_PW_PATTERN, $next)) {
        gpMgrRespond(array('error' => GP_PW_RULE_TEXT), 400);
    }
    if ($next === $current) {
        gpMgrRespond(array('error' => '현재 비밀번호와 다른 비밀번호를 입력해 주세요.'), 400);
    }
    gpPasswordSave($id, (int) $user['role'], $next, false);
    gpMgrRespond(array('success' => true));
}

/** 상위 계정이 하위 계정 비밀번호 재설정 — 임시 비밀번호를 한 번만 보여준다 (branchmgr.php, adminLogin.php) */
function SResetPassword($data)
{
    $actor = gpMgrActor();
    $target = gpMgrManagedTarget($actor, (string) ($data['id'] ?? ''));
    if (!gpPasswordHashed((int) $target['role'])) {
        gpMgrRespond(array('error' => '학생 비밀번호는 교사 학생관리 화면에서 바꿀 수 있습니다.'), 400);
    }
    $temp = gpTempPassword();
    gpPasswordSave($target['id'], (int) $target['role'], $temp, true);
    gpMgrRespond(array('success' => true, 'id' => $target['id'], 'temp' => $temp));
}
