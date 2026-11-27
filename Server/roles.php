<?php
/**
 * 글핀 계정 role 정의 — 관리페이지.xlsx 기준 (JS 쪽: js/roles.js, 두 파일을 같게 유지)
 *
 *   첫째 자리 = 그룹   1 유치원, 2 학원, 3 홈스쿨, 4 화상수업, 5 방과후
 *   자릿수   = 단계   1자리 지사, 2자리 선생, 3자리 학생
 *   9 = 본사(admin)
 *
 *   본사(9) → 유치원지사(1)   → 유치원선생(10)   → 유치원생(100)
 *          → 학원지사(2)     → 학원선생(20)     → 학원생(200)
 *          → 홈스쿨지사(3)   → 홈스쿨선생(30)   → 홈스쿨생(300)
 *          → 화상수업지사(4) → 화상수업선생(40) → 화상수업생(400)
 *          → 방과후지사(5)   → 방과후선생(50)   → 방과후생(500)
 *
 * 계정은 바로 위 단계가 생성한다. mid = 생성한 부모 id, tid = 학생의 담당 선생 id
 */

const GP_ROLE_ADMIN = 9;

const GP_ROLE_NAMES = array(
    9   => '본사',
    1   => '유치원지사',
    10  => '유치원교사',
    100 => '유치원생',
    2   => '학원지사',
    20  => '학원교사',
    200 => '학원생',
    3   => '방문수업지사',
    30  => '방문수업교사',
    300 => '방문수업생',
    4   => '화상수업지사',
    40  => '화상수업교사',
    400 => '화상수업생',
    5   => '방과후지사',
    50  => '방과후교사',
    500 => '방과후생',
);

// role => 바로 아래 단계로 생성할 수 있는 role
const GP_ROLE_CHILDREN = array(
    9  => array(1, 2, 3, 4, 5),
    1  => array(10),
    2  => array(20),
    3  => array(30),
    4  => array(40),
    5  => array(50),
    10 => array(100),
    20 => array(200),
    30 => array(300),
    40 => array(400),
    50 => array(500),
);

const GP_BRANCH_ROLES  = array(1, 2, 3, 4, 5);
const GP_TEACHER_ROLES = array(10, 20, 30, 40, 50);
const GP_STUDENT_ROLES = array(100, 200, 300, 400, 500);

// 초기 화면 영역 => 로그인할 수 있는 그룹 (franchise = 홈스쿨·화상수업·방과후 공용 어휘담 화면)
const GP_AREA_GROUP = array(
    'kinder'      => array(1),
    'academy'     => array(2),
    'homeschool'  => array(3),
    'online'      => array(4),
    'afterschool' => array(5),
    'franchise'   => array(3, 4, 5),
);
const GP_AREA_NAMES = array(
    'kinder'      => '유치원',
    'academy'     => '학원',
    'homeschool'  => '방문수업',
    'online'      => '화상수업',
    'afterschool' => '방과후',
    'franchise'   => '방문수업·화상수업·방과후',
);

/** 그룹 번호 (1 유치원, 2 학원, 3 홈스쿨, 4 화상수업, 5 방과후), 본사는 0 */
function gpRoleGroup(int $role): int
{
    return $role === GP_ROLE_ADMIN ? 0 : (int) substr((string) $role, 0, 1);
}

/** 선생 role 의 학생 role (예: 20 → 200), 선생이 아니면 null */
function gpStudentRoleOf(int $teacherRole): ?int
{
    return in_array($teacherRole, GP_TEACHER_ROLES, true) ? GP_ROLE_CHILDREN[$teacherRole][0] : null;
}

/** 해당 영역에서 로그인할 수 있는지 — 본사는 모든 영역, 영역 지정이 없으면 허용 */
function gpCanUseArea(int $role, string $area): bool
{
    if ($area === '' || $role === GP_ROLE_ADMIN) {
        return true;
    }
    return isset(GP_AREA_GROUP[$area]) && in_array(gpRoleGroup($role), GP_AREA_GROUP[$area], true);
}
