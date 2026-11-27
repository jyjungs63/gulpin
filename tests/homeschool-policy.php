<?php
// Isolated policy checks; no production DB or real account data is accessed.
const GP_PW_PATTERN = '/^[A-Za-z0-9]{4,30}$/';
class HomeschoolReply extends Exception
{
    public $payload;
    public $status;
    public function __construct($payload, $status) { $this->payload = $payload; $this->status = $status; }
}
$testRole = 30;
$testCourse = '방문수업';
$testExisting = array();
$testWrites = array();
function gpTeacherActor(): array { global $testRole; return array('id' => 'teacherA', 'role' => $testRole, 'owner' => 'school'); }
function gpUserColumn(string $column): ?array { return array('name' => $column); }
function gpPriceCatalog(): array { return json_decode(file_get_contents(__DIR__ . '/../Server/price_catalog.json'), true); }
function gpMgrRespond(array $payload, int $status = 200): void { throw new HomeschoolReply($payload, $status); }
function gpHomeschoolTeacherCourse(string $etc): string { return $etc === '급파한' ? '급파한' : '방문수업'; }
function gpMgrQuery(string $sql, array $params = array())
{
    global $testExisting, $testWrites, $testCourse;
    if (strpos($sql, 'SELECT etc') === 0) return array(array('etc' => $testCourse));
    if (strpos($sql, 'SELECT id, tid, role') === 0) return $testExisting;
    if (strpos($sql, 'INSERT') === 0 || strpos($sql, 'UPDATE') === 0) {
        $testWrites[] = array($sql, $params);
        return 1;
    }
    throw new RuntimeException('Unexpected query');
}
// Load this module with its infrastructure dependency stubbed above.
$source = file_get_contents(__DIR__ . '/../Server/homeschool_policy.php');
$source = preg_replace('/^require_once .*;\R/m', '', substr($source, 5));
eval($source);
$base = array('id' => 'student1', 'name' => '학생', 'passwd' => 'Pass123', 'birth_year' => '2018',
    'classnm' => '학교', 'p_mobile' => '010-1234-5678', 'course' => '방문수업', 'material_code' => 'SO1-A',
    'sdate' => '2026-10-01', 'edate' => '2026-12-31');
$cases = array(
    array('new student', array(), array(), 30, 200),
    array('other course material', array('material_code' => 'L1-A'), array(), 30, 400),
    array('old course name', array('course' => '급파한', 'material_code' => 'L1-A'), array(), 30, 400),
    array('legacy 전체 teacher', array(), array(), 30, 200, '전체'),
    array('gupahan teacher', array('course' => '급파한', 'material_code' => 'L1-A'), array(), 30, 200, '급파한'),
    array('gupahan teacher visit book', array('course' => '급파한'), array(), 30, 400, '급파한'),
    array('gupahan teacher visit course', array('material_code' => 'L1-A'), array(), 30, 400, '급파한'),
    array('edit own student', array('original_id' => 'student1'), array(array('id' => 'student1', 'tid' => 'teacherA', 'role' => 300)), 30, 200),
    array('duplicate id', array(), array(array('id' => 'student1', 'tid' => 'teacherA', 'role' => 300)), 30, 409),
    array('foreign teacher', array('original_id' => 'student1'), array(array('id' => 'student1', 'tid' => 'teacherB', 'role' => 300)), 30, 403),
    array('foreign student role', array('original_id' => 'student1'), array(array('id' => 'student1', 'tid' => 'teacherA', 'role' => 100)), 30, 403),
    array('not homeschool teacher', array(), array(), 10, 403),
    array('change id', array('original_id' => 'oldStudent'), array(), 30, 400),
    array('course mismatch', array('course' => '急'), array(), 30, 400),
    array('unknown book', array('material_code' => 'fake'), array(), 30, 400),
    array('invalid year', array('birth_year' => '1899'), array(), 30, 400),
    array('future year', array('birth_year' => '2999'), array(), 30, 400),
    array('invalid date', array('sdate' => '2026-02-30'), array(), 30, 400),
    array('reversed dates', array('edate' => '2026-09-01'), array(), 30, 400),
    array('empty name', array('name' => ''), array(), 30, 400),
    array('invalid password', array('passwd' => 'a'), array(), 30, 400),
    array('long phone', array('p_mobile' => str_repeat('1', 21)), array(), 30, 400),
    array('optional end date', array('edate' => ''), array(), 30, 200),
);
foreach (array_diff(array_keys($base), array('edate')) as $field) {
    $cases[] = array('required empty ' . $field, array($field => ''), array(), 30, 400);
    $cases[] = array('required whitespace ' . $field, array($field => '  '), array(), 30, 400);
}
foreach ($cases as $case) {
    [$name, $override, $testExisting, $testRole, $expected] = $case;
    $testCourse = $case[5] ?? '방문수업';
    $testWrites = array();
    try {
        SSaveHomeschoolStudent(array_merge($base, $override));
        throw new RuntimeException('No reply: ' . $name);
    } catch (HomeschoolReply $reply) {
        if ($reply->status !== $expected || (($expected === 200) !== (count($testWrites) === 1))) {
            throw new RuntimeException('Failed: ' . $name . ' status=' . $reply->status);
        }
        if ($name === 'edit own student' && strpos($testWrites[0][0], 'AND tid = ? AND role = 300') === false) {
            throw new RuntimeException('Missing update scope');
        }
    }
}
echo 'PASS ' . count($cases) . ' homeschool policy checks' . PHP_EOL;
