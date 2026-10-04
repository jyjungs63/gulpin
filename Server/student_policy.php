<?php
/**
 * 선생님 학생관리(purchase/institutemgr.php) — 학원·화상수업·방과후 선생(20/40/50)
 *
 * 학생부(글핀 시스템관리 엑셀): 선생님이 직접 작성, 학생 아이디는 필요 없다.
 *   → 아이디/암호는 서버가 무작위로 만들고 confirm = 0 으로 저장해 로그인할 수 없게 한다.
 * 과정/단계: 선생님 그룹의 가격표(price_catalog.json, catalog.php) 교재군(family) / 교재(title)
 * 저장 컬럼: step = 교재 코드, classnm = 학교명, mobile = 연락처,
 *            sdate/edate = 수업 시작/종료일, status = 학습상태(1 등원 / 0 퇴원)
 * 선생님은 본인(tid)에게 등록된 학생만 조회·수정·삭제한다.
 *
 * 아이디 학생관리(purchase/kgardenmgr.php) — 유치원·홈스쿨 선생(10/30)
 *   학생이 학습 화면에 로그인하므로 선생님이 아이디/암호를 만든다 (confirm = 1).
 *   step = 과정 — 유치원 잼잼별/말잼별/글잼별, 홈스쿨 급파한/홈스쿨
 *
 * SMethods.php / SMethodsSQLite.php 공용 (catalog.php 다음에 require)
 */

require_once __DIR__ . '/catalog.php';

// 단계로 고를 수 없는 교재군 (노트는 부교재)
const GP_STUDENT_SKIP_FAMILIES = array('노트');

/** 로그인한 선생님 + 소속 지사명 — 세션 + DB 로 확인 */
function gpTeacherActor(): array
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin !== '' && parse_url($origin, PHP_URL_HOST) !== parse_url('//' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST)) {
        gpMgrRespond(array('error' => '허용되지 않은 요청입니다.'), 403);
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $id = (string) ($_SESSION['user'] ?? '');
    $rows = $id === '' ? array() : gpMgrQuery(
        'SELECT t.id, t.name, t.role, t.owner, b.owner AS branch
         FROM gulpin_user t LEFT JOIN gulpin_user b ON b.id = t.mid
         WHERE t.id = ? AND t.status = 1 AND t.confirm = 1',
        array($id)
    );
    if (!$rows) {
        gpMgrRespond(array('error' => '로그인이 만료되었습니다. 다시 로그인해 주세요.'), 401);
    }

    $actor = $rows[0];
    $actor['role'] = (int) $actor['role'];
    if (!in_array($actor['role'], GP_TEACHER_ROLES, true)) {
        gpMgrRespond(array('error' => '학생관리는 선생님 계정으로 이용할 수 있습니다.'), 403);
    }
    return $actor;
}

/** 선생님 그룹 가격표의 과정 목록 — 교재군 => [{code, title, step}] (화상수업은 홈스쿨 가격표, GP_PRICE_GROUP_OF) */
function gpStudentCourses(array $actor): array
{
    $key = GP_PRICE_GROUP_OF[gpRoleGroup($actor['role'])] ?? '';
    $courses = array();
    foreach (gpPriceCatalog()['groups'][$key]['items'] ?? array() as $item) {
        if (!in_array($item['family'], GP_STUDENT_SKIP_FAMILIES, true)) {
            $courses[$item['family']][] = array('code' => $item['code'], 'title' => $item['title'], 'step' => $item['step'] ?? null);
        }
    }
    return $courses;
}

/** 본인에게 등록된 학생인지 확인 */
function gpTeacherOwnStudent(array $actor, string $id): void
{
    $rows = gpMgrQuery(
        'SELECT id FROM gulpin_user WHERE id = ? AND tid = ? AND role IN (' . implode(',', GP_STUDENT_ROLES) . ')',
        array($id, $actor['id'])
    );
    if (!$rows) {
        gpMgrRespond(array('error' => "{$id} 학생을 관리할 권한이 없습니다."), 403);
    }
}

/** 입력값 검증 — 컬럼 길이(gulpin_user), 교재 코드(가격표), 날짜 형식 */
function gpStudentValidate(array $item, array $codes): array
{
    $clean = array();
    foreach (array('name' => array(20, '학생명'), 'mobile' => array(20, '연락처'), 'classnm' => array(45, '학교명')) as $key => [$max, $label]) {
        $clean[$key] = trim((string) ($item[$key] ?? ''));
        if (mb_strlen($clean[$key]) > $max) {
            gpMgrRespond(array('error' => "{$label}은(는) {$max}자 이내로 입력해 주세요."), 400);
        }
    }
    if ($clean['name'] === '') {
        gpMgrRespond(array('error' => '학생명을 입력해 주세요.'), 400);
    }
    $who = $clean['name'];

    $clean['step'] = trim((string) ($item['step'] ?? ''));
    if (!in_array($clean['step'], $codes, true)) {
        gpMgrRespond(array('error' => "{$who}: 단계를 선택해 주세요."), 400);
    }

    foreach (array('sdate' => '수업시작일', 'edate' => '수업종료일') as $key => $label) {
        $value = trim((string) ($item[$key] ?? ''));
        if ($value !== '' && !(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m) && checkdate((int) $m[2], (int) $m[3], (int) $m[1]))) {
            gpMgrRespond(array('error' => "{$who}: {$label}이(가) 올바르지 않습니다."), 400);
        }
        $clean[$key] = $value;
    }
    if ($clean['sdate'] !== '' && $clean['edate'] !== '' && $clean['edate'] < $clean['sdate']) {
        gpMgrRespond(array('error' => "{$who}: 수업종료일이 수업시작일보다 빠릅니다."), 400);
    }

    $clean['status'] = (int) ($item['status'] ?? 1) === 0 ? 0 : 1;
    return $clean;
}

/** 로그인에 쓰지 않는 무작위 학생 아이디 */
function gpStudentNewId(): string
{
    do {
        $id = 'gs' . bin2hex(random_bytes(5));
    } while (gpMgrQuery('SELECT id FROM gulpin_user WHERE id = ?', array($id)));
    return $id;
}

function STeacherStudents($data)
{
    $actor = gpTeacherActor();
    $rows = gpMgrQuery(
        'SELECT id, name, mobile, classnm, step, sdate, edate, status, rdate
         FROM gulpin_user WHERE tid = ? AND role IN (' . implode(',', GP_STUDENT_ROLES) . ')
         ORDER BY status DESC, name',
        array($actor['id'])
    );
    gpMgrRespond(array(
        'success' => $rows,
        'branch'  => (string) $actor['branch'],
        'academy' => (string) $actor['owner'],
        'teacher' => (string) $actor['name'],
        'courses' => gpStudentCourses($actor),
    ));
}

/** 학생 등록/수정 — id 가 없으면 새 학생. 전부 검증한 뒤 저장해 일부만 반영되는 것을 막는다 */
function STeacherStudentSave($data)
{
    $actor = gpTeacherActor();
    $items = is_array($data['items'] ?? null) ? $data['items'] : array();
    if (!$items) {
        gpMgrRespond(array('error' => '저장할 학생이 없습니다.'), 400);
    }

    $codes = array_column(array_merge(...array_values(gpStudentCourses($actor))), 'code');
    $rows = array();
    foreach ($items as $item) {
        $id = trim((string) ($item['id'] ?? ''));
        if ($id !== '') {
            gpTeacherOwnStudent($actor, $id);
        }
        $rows[] = array($id, gpStudentValidate($item, $codes));
    }

    $created = 0;
    foreach ($rows as [$id, $f]) {
        if ($id === '') {
            gpMgrQuery(
                "INSERT INTO gulpin_user (id, name, owner, password, mobile, role, confirm, status, tid, mid, classnm, step, sdate, edate)
                 VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?, NULLIF(?, ''), NULLIF(?, ''))",
                array(gpStudentNewId(), $f['name'], $actor['owner'], bin2hex(random_bytes(8)), $f['mobile'],
                    gpStudentRoleOf($actor['role']), $f['status'], $actor['id'], $actor['id'], $f['classnm'], $f['step'],
                    $f['sdate'], $f['edate'])
            );
            $created++;
        } else {
            gpMgrQuery(
                "UPDATE gulpin_user SET name = ?, mobile = ?, classnm = ?, step = ?, sdate = NULLIF(?, ''), edate = NULLIF(?, ''), status = ?
                 WHERE id = ?",
                array($f['name'], $f['mobile'], $f['classnm'], $f['step'], $f['sdate'], $f['edate'], $f['status'], $id)
            );
        }
    }
    gpMgrRespond(array('success' => true, 'created' => $created, 'updated' => count($rows) - $created));
}

function STeacherStudentDelete($data)
{
    $actor = gpTeacherActor();
    $id = (string) ($data['id'] ?? '');
    gpTeacherOwnStudent($actor, $id);
    gpMgrQuery('DELETE FROM gulpin_user WHERE id = ?', array($id));
    gpMgrRespond(array('success' => true));
}

// 아이디 학생관리 그룹 => 선택할 수 있는 과정(step) (JS 쪽: purchase/kgardenmgr.php STEP_LIST, 두 곳을 같게 유지)
const GP_ID_STUDENT_STEPS = array(
    1 => array('잼잼별', '말잼별', '글잼별'),
    3 => array('급파한', '홈스쿨'),
);

/** 아이디 학생관리 선생님(유치원·홈스쿨) */
function gpIdTeacherActor(): array
{
    $actor = gpTeacherActor();
    if (!isset(GP_ID_STUDENT_STEPS[gpRoleGroup($actor['role'])])) {
        gpMgrRespond(array('error' => '아이디 학생관리는 유치원·홈스쿨 선생님 계정으로 이용할 수 있습니다.'), 403);
    }
    return $actor;
}

function SIdStudents($data)
{
    $actor = gpIdTeacherActor();
    $rows = gpMgrQuery(
        'SELECT id, name, password AS passwd, classnm, step, tid
         FROM gulpin_user WHERE tid = ? AND role IN (' . implode(',', GP_STUDENT_ROLES) . ')
         ORDER BY classnm, id',
        array($actor['id'])
    );
    gpMgrRespond(array('success' => $rows));
}

/** 시작아이디(영문)로 이미 쓰는 가장 큰 번호 — 다른 선생님 학생 포함, 없으면 0 */
function SIdStudentMaxNo($data)
{
    gpIdTeacherActor();
    $prefix = trim((string) ($data['id'] ?? ''));
    if (!preg_match('/^[A-Za-z]{1,20}$/', $prefix)) {
        gpMgrRespond(array('error' => '시작아이디는 영문으로 입력해 주세요.'), 400);
    }
    $max = 0;
    foreach (gpMgrQuery('SELECT id FROM gulpin_user WHERE id LIKE ?', array($prefix . '%')) as $row) {
        $rest = substr($row['id'], strlen($prefix));
        if (strcasecmp(substr($row['id'], 0, strlen($prefix)), $prefix) === 0 && ctype_digit($rest)) {
            $max = max($max, (int) $rest);
        }
    }
    gpMgrRespond(array('result' => $max));
}

/** 아이디 학생 생성/수정 — 없는 아이디는 생성, 본인 학생이면 수정, 다른 계정 아이디면 거부. 전부 검증한 뒤 저장 */
function SIdStudentSave($data)
{
    $actor = gpIdTeacherActor();
    $group = gpRoleGroup($actor['role']);
    $items = is_array($data['items'] ?? null) ? $data['items'] : array();
    if (!$items) {
        gpMgrRespond(array('error' => '저장할 학생이 없습니다.'), 400);
    }

    $rows = array();
    foreach ($items as $item) {
        $id = trim((string) ($item['id'] ?? ''));
        $f = array(
            'name'     => trim((string) ($item['name'] ?? '')),
            'password' => trim((string) ($item['passwd'] ?? '')),
            'classnm'  => trim((string) ($item['classnm'] ?? '')),
            'step'     => trim((string) ($item['step'] ?? '')),
        );
        if (!preg_match('/^[A-Za-z0-9]{2,30}$/', $id)) {
            gpMgrRespond(array('error' => "아이디({$id})는 영문/숫자 2~30자로 입력해 주세요."), 400);
        }
        if (isset($rows[$id])) {
            gpMgrRespond(array('error' => "{$id} 아이디가 중복되었습니다."), 400);
        }
        if ($f['name'] === '' || mb_strlen($f['name']) > 20) {
            gpMgrRespond(array('error' => "{$id}: 이름을 20자 이내로 입력해 주세요."), 400);
        }
        if (!preg_match('/^[A-Za-z0-9]{4,30}$/', $f['password'])) {
            gpMgrRespond(array('error' => "{$id}: 비밀번호는 영문/숫자 4~30자로 입력해 주세요."), 400);
        }
        if (mb_strlen($f['classnm']) > 45) {
            gpMgrRespond(array('error' => "{$id}: 반이름은 45자 이내로 입력해 주세요."), 400);
        }
        if (!in_array($f['step'], GP_ID_STUDENT_STEPS[$group], true)) {
            gpMgrRespond(array('error' => "{$id}: 과정을 선택해 주세요."), 400);
        }

        $exists = gpMgrQuery('SELECT tid, role FROM gulpin_user WHERE id = ?', array($id));
        $own = $exists && $exists[0]['tid'] === $actor['id'] && in_array((int) $exists[0]['role'], GP_STUDENT_ROLES, true);
        if ($exists && !$own) {
            gpMgrRespond(array('error' => "{$id}은(는) 이미 사용중인 아이디입니다. 다른 아이디를 사용하세요."), 409);
        }
        $rows[$id] = array((bool) $exists, $f);
    }

    $created = 0;
    foreach ($rows as $id => [$exists, $f]) {
        if ($exists) {
            gpMgrQuery(
                'UPDATE gulpin_user SET name = ?, password = ?, classnm = ?, step = ? WHERE id = ?',
                array($f['name'], $f['password'], $f['classnm'], $f['step'], $id)
            );
        } else {
            gpMgrQuery(
                'INSERT INTO gulpin_user (id, name, owner, password, role, confirm, status, tid, mid, classnm, step)
                 VALUES (?, ?, ?, ?, ?, 1, 1, ?, ?, ?, ?)',
                array($id, $f['name'], $actor['owner'], $f['password'], gpStudentRoleOf($actor['role']),
                    $actor['id'], $actor['id'], $f['classnm'], $f['step'])
            );
            $created++;
        }
    }
    gpMgrRespond(array('success' => true, 'created' => $created, 'updated' => count($rows) - $created));
}
