<?php

require_once 'sqlite_compat.php';
require_once 'mgr_policy.php'; // SShowMgr, SRegistermgr, SShowConfirmUpdate, SDeleteMgr
require_once 'catalog.php';    // SGetPriceCatalog, SGetLearningAccess (가격표.xlsx)
require_once 'student_policy.php'; // STeacherStudents/Save/Delete, SIdStudents/MaxNo/Save
require_once __DIR__ . '/homeschool_policy.php';
require_once __DIR__ . '/payment_policy.php';

$urlFromGET = "";
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {

    if (isset($_POST['functionName'])) {
        $functionName = $_POST['functionName'];

        if (isset($_GET['dest'])) {
            $urlFromGET = $_GET['dest'];
        }

        // 이 프로젝트에서 정의한 S* API 함수만 호출 (system 등 PHP 내장 함수 차단. 함수명은 대소문자 무시)
        $callable = is_string($functionName)
            && preg_match('/^S[A-Za-z0-9_]+$/D', $functionName)
            && function_exists($functionName)
            && ($method = new ReflectionFunction($functionName))->isUserDefined()
            && substr($method->getName(), 0, 1) === 'S'
            // SetLogin: 로그인 내부 처리용 / 나머지: 화면에서 쓰지 않는 함수 (권한 확인 없음)
            && !in_array(strtolower($functionName), array('setlogin', 'sremovechild', 'scheckstudentid',
                'sinsertstudent', 'supdatestudent', 'smanagestudent', 'sgetlistprice'), true);
        if (!$callable) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('error' => '허용되지 않은 요청입니다.'), JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (is_callable($functionName)) {
            $functionName = $method->getName();
            if (
                "SUploadBoardPDF" == $functionName
                || "SUploadBoard" == $functionName
                || "Sfindpassword" == $functionName
                || "SUdateBoard" == $functionName
            )
                $functionName($_POST);
            else if ("Slogon" == $functionName || "SRegister" == $functionName)
                $functionName($_POST, $urlFromGET);
            else
                $functionName(isset($_POST['otherData']) ? $_POST['otherData'] : array());
        }
    }
}
function Sfindpassword($data)
{
    gpMgrRespond(['error' => '담당 선생님, 지사 또는 본사에 비밀번호 재설정을 요청해 주세요.'], 403);
}

function Slogon($data, $dest)
{
    $Email    = $data["Email"];
    $Password = $data["Password"];
    global $location;

    //$dest = "classroom";
    $rows = array();

    if (!empty($Email) && !empty($Password)) {

        $res = CheckUser($Email, $Password);

        // 초기 화면 영역(유치원/학원/홈스쿨·방과후·온라인)과 계정 그룹이 다르면 로그인하지 않는다
        $area = (string) ($data['area'] ?? '');
        if ($res && !gpCanUseArea((int) $res['role'], $area)) {
            header('Content-Type: application/json');
            $areaName = GP_AREA_NAMES[$area] ?? $area;
            echo json_encode(array('falure' => 'area', 'message' => "{$areaName} 영역에서 로그인할 수 없는 계정입니다."), JSON_UNESCAPED_UNICODE);
            return;
        }

        if ($res) {
            // 세션 쿠키: JS 접근 차단, 외부 사이트 요청엔 미전송(Lax — PG 결제 후 복귀는 유지), HTTPS 면 Secure
            session_set_cookie_params(array(
                'httponly' => true,
                'samesite' => 'Lax',
                'secure'   => gpIsHttpsRequest(),
            ));
            sqlite_start_session();
            session_regenerate_id(true);   // 로그인 전 세션 id 재사용(세션 고정) 방지
            $_SESSION["authenticated"] = 'true';
            $_SESSION["user"] = $res['id'];
            $_SESSION["name"] = $res['name'];
            $_SESSION["role"] = $res['role'];
            $_SESSION["confirm"] = $res['confirm'];
            $_SESSION["location"] = $location;
            $_SESSION["dest"] = $dest;

            // 로그인과 영역 검증이 끝난 뒤 입력 비밀번호로 확인용 사본을 저장한다.
            $captureStatus = gpPasswordCapture($res, (string) $Password);
            header('X-Gulpin-Password-View: ' . $captureStatus);

            array_push(
                $rows,
                array(
                    'authenticated'   => 'true',
                    'user' => $res['id'],
                    'name' => $res['name'],
                    'owner' => $res['owner'],
                    'role' => $res['role'],
                    'confirm'  => $res['confirm'],
                    'location'  => $location,
                    'dest'  => $dest,
                    'step'  => isset($res['step']) == true ? $res['step'] : "",
                    'clas'  => $res['etc'],
                    'mustChange' => (int) ($res['pw_temp'] ?? 0) === 1   // 임시 비밀번호 → login/changepw.php
                )
            );

            //if ( $res['role'] == 2 ) {
            saveClientlog( $res['id'],$res['name'], $res['role']);
            //}
            header('Content-Type: application/json');

            echo json_encode(array('success' => $rows));
        } else {
            //hearder("Location: login.php");
            echo json_encode(array('falure' => 'password mismatch'));
        }
    } else {
        //header("Location: login.php");
        echo json_encode(array('falure' => 'id or password empty!'));
    }

}
function SetLogin($id)
{
    global $conn;
    try {
        $sql = "UPDATE gulpin_user SET logstat = 1 , logdate = now()  WHERE id = '{$id}' ";
        if ($conn->query($sql) === TRUE) {
            $result = true;
        }
        $conn->close();
    } catch (Exception $e) {
        $result = $e->getMessage();
    }
}

function CheckUser2($login)
{
    global $conn;
    $user = "";
    $login = sqlite_escape_string($conn, $login);
    $rs = sqlite_query($conn, "select * from gulpin_user where id='{$login}'");

    if ($rs) {
        $user = sqlite_fetch_assoc($rs);
        sqlite_free_result($rs);
    }
    $conn->close();
    return $user;
}

function CheckUser($login, $password)
{
    global $conn;
    $user = "";
    $login = sqlite_escape_string($conn, $login);
    $rs = sqlite_query($conn, "select * from gulpin_user where id='{$login}' and status = 1 and confirm = 1 ");

    if ($rs) {
        $user = sqlite_fetch_assoc($rs);
        $passwordnew = sqlite_escape_string($conn, password_hash($password, PASSWORD_BCRYPT));
        if ($user && !gpPasswordCheck($user, (string) $password)) {   // 해시/평문 확인 + 평문이면 해시로 교체
            $user = "";
        }
        sqlite_free_result($rs);
    }
    //$conn->close();
    return $user;
}


function SRegister($data, $dest)
{
    // 계정은 바로 위 단계(본사 → 지사 → 선생 → 학생)가 생성한다 — Server/roles.php
    header('Content-Type: application/json');
    echo json_encode(array('falure' => '계정은 소속 지사/선생님이 등록합니다. 담당자에게 문의하세요.'), JSON_UNESCAPED_UNICODE);
}

function SstudentList($data)
{
    sqlite_start_session();

    $tid       = (string) $data['tid'];
    $role     = $data['role'];

    if (!gpCanView(gpSessionUser(), $tid)) {
        gpMgrRespond(array('error' => '조회 권한이 없습니다.'), 403);
    }

    global $conn;

    $rows = array();

    $i = 0;

    try {

        $rs = gpMgrQueryText("SELECT name FROM gulpin_user u where u.tid = ? and role = ?", array($tid, $role));

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'name'  => $row['name'],
                )
            );
        }
        $conn->close();
    } catch (Exception $e) {
        echo  json_encode(array("error:" => $e->getMessage()));
    }

    header('Content-Type: application/json');
    echo json_encode($rows);
}
function SShowOrderList($data)
{

    sqlite_start_session();

    // 조회 대상은 브라우저가 보낸 id 가 아니라 로그인 계정 — 본사는 전체, 지사는 본인 주문
    $actor = gpMgrActor();

    global $conn;

    $params = array();
    if (gpMgrIsAdmin($actor))
        $sqlString = "SELECT p.*, u.owner bname FROM gulpin_porlist p , gulpin_user u where u.id = p.id order by p.rdate ";
    else {
        $sqlString = "SELECT p.*, u.owner bname FROM gulpin_porlist p , gulpin_user u where u.id = p.id and  u.id = ? order by p.rdate";
        $params[] = $actor['id'];
    }

    $rows = array();

    $i = 0;

    try {

        $rs = gpMgrQueryText($sqlString, $params);

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'id'          => $row['id'],
                    'por_id'      => $row['por_id'],
                    'order'       => $row['order'],
                    'addr'        => $row['addr'],
                    'mobile'      => $row['mobile'],
                    'rdate'       => $row['rdate'],
                    'confirm'     => $row['confirm'],
                    'bname'      => $row['bname'],
                )
            );
        }
        $conn->close();
    } catch (Exception $e) {
        echo  json_encode(array("error:" => $e->getMessage()));
    }

    header('Content-Type: application/json');
    echo json_encode($rows);
}

function SPorDetailList($data)
{
    sqlite_start_session();

    global $conn;

    $pid  = $data['id'];
    $rows = array();
    $i = 0;
    $res = "";

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // 본사는 모든 주문, 지사는 본인 주문만
    $actor = gpMgrActor();
    $stmt = "select * from gulpin_porlist where por_id = ?";
    $params = array($pid);
    if (!gpMgrIsAdmin($actor)) {
        $stmt .= " and id = ?";
        $params[] = $actor['id'];
    }

    try {

        $rs = gpMgrQueryText($stmt, $params);

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'id'   => $row['id'],
                    'por_id' => $row['por_id'],
                    'json' => $row['por_list'],
                    'order' => $row['order'],
                    'rdate' => $row['rdate'],
                    'addr'  => $row['addr'],
                    'mobile'  => $row['mobile'],
                    'zip'  => $row['zip'],
                    'confirm'  => $row['confirm'],
                    'pdfname' => $row['pdfname'],
                    'invoice' => $row['invoice'],
                    'refund' => $row['refund']
                )
            );
        }

        $conn->close();
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    }

    header('Content-Type: application/json');
    echo json_encode($rows);
}

function SPorDetailListRangeSort($data)
{
    sqlite_start_session();

    global $conn;

    $id     = $data['id'];
    $name   = $data['name'];
    $start  = $data['start'];
    $end    = $data['end'];
    $bsort   = $data['bsort'];
    $dsort   = $data['dsort'];
    $ssort   = $data['ssort'];
    $sortobj   = $data['sortobj'];

    $rows = array();
    $ret  = array();
    $i = 0;
    $res = "";

    if ($bsort % 2 == 0)
        $bs = "ASC";
    else
        $bs = "DESC";
    if ($dsort % 2 == 0)
        $ds = "DESC";
    else
        $ds = "ASC";
    if ($ssort % 2 == 0)
        $ss = "ASC";
    else
        $ss = "DESC";

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // 지사는 브라우저가 보낸 id 대신 본인 id 로만 조회
    $actor = gpMgrActor();
    if (!gpMgrIsAdmin($actor)) {
        $id = $actor['id'];
    }

    $tsql = "select p.*, u.owner owner from gulpin_porlist p ,  gulpin_user u  where u.id = p.id and ";

    if (gpMgrIsAdmin($actor) && ($id == "admin" || $id == "chaitalk")) {
        $where  = "p.rdate between ? and ?";
        $params = array($start, $end);
    } else if ($name == "전유치원") {
        $where  = "p.id = ?  and p.rdate between ? and ?";
        $params = array($id, $start, $end);
    } else {
        $where  = "p.id = ? and p.order =  ? and p.rdate between ? and ?";
        $params = array($id, $name, $start, $end);
    }
    if ($sortobj == "주문상태")
        $stmt = $tsql . $where . " order by confirm {$ss} , id asc, rdate desc";
    else if ($sortobj == "날짜")
        $stmt = $tsql . $where . " order by  rdate {$ds}, id {$bs} ";
    else
        $stmt = $tsql . $where . " order by id {$bs}, rdate {$ds} ";
    try {

        $rs = gpMgrQueryText($stmt, $params);

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'id'   => $row['id'],
                    'por_id' => $row['por_id'],
                    'json' => $row['por_list'],
                    'owner' => $row['owner'],
                    'order' => $row['order'],
                    'rdate' => $row['rdate'],
                    'addr'  => $row['addr'],
                    'mobile'  => $row['mobile'],
                    'confirm'  => $row['confirm'],
                    'uname'  => $row['owner'],
                    'invoice' => $row['invoice'],
                    'refund' => $row['refund']
                )
            );
        }
        array_push($ret,  array('list' => $rows));
        $nstart = substr($start, 0, 7);
        $stmt = "select * from gulpin_parcel where  DATE_FORMAT(`date`,'%Y-%m') = ? ";
        $params = array($nstart);
        if ($id != "전지사") {
            $stmt = "select * from gulpin_parcel where id=? and DATE_FORMAT(`date`,'%Y-%m') = ? ";
            $params = array($id, $nstart);
        }

        $rs1 = gpMgrQueryText($stmt, $params);
        $rows = [];
        foreach ($rs1 as $row) {
            array_push(
                $rows,
                array(
                    'id'     => $row['id'],
                    'name'   => $row['name'],
                    'date'   => $row['date'],
                    'price'  => $row['price']
                )
            );
        }
        array_push($ret,  array('parcel' => $rows));
        $conn->close();
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    }

    header('Content-Type: application/json');
    //echo json_encode($rows);
    echo json_encode($ret);
}

function SPorDetailListRange($data)
{
    sqlite_start_session();

    global $conn;

    $id     = $data['id'];
    $name   = $data['name'];
    $start  = $data['start'];
    $end    = $data['end'];

    $rows = array();
    $ret  = array();
    $i = 0;
    $res = "";

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // 지사는 브라우저가 보낸 id 대신 본인 id 로, 전지사(전체) 조회는 본인 주문 전체로
    $actor = gpMgrActor();
    if (!gpMgrIsAdmin($actor)) {
        $id = $actor['id'];
        if ($name == "전지사")
            $name = "전유치원";
    }

    $tsql = "select p.*, u.owner owner from gulpin_porlist p ,  gulpin_user u  where u.id = p.id and ";


    if ($name == "전지사") {
        $stmt = $tsql . "p.rdate between ? and ? order by id , rdate desc";
        $params = array($start, $end);
    }
    else if ($name == "전유치원") {
        $stmt = $tsql . "u.id = ? and p.rdate between ? and ? order by p.id, rdate desc";
        $params = array($id, $start, $end);
    }
    else {
        if ($id != "admin") {
            $stmt = $tsql . "p.id = ? and p.order =  ? and  p.rdate >= ? and  p.rdate <= ? order by p.id, rdate desc";
            $params = array($id, $name, $start, $end);
        }
        // $stmt = $tsql . "p.id = '{$id}' and p.order =  '{$name}' and  p.rdate between '{$start}' and '{$end}' order by p.id, rdate desc";
        else {
            $stmt = $tsql . "p.id = ? and p.rdate between ? and ? order by p.id, rdate desc";
            $params = array($name, $start, $end);
        }
    }

    try {

        $rs = gpMgrQueryText($stmt, $params);

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'id'   => $row['id'],
                    'por_id' => $row['por_id'],
                    'owner' => $row['owner'],
                    'json' => $row['por_list'],
                    'order' => $row['order'],
                    'rdate' => $row['rdate'],   
                    'addr'  => $row['addr'],
                    'mobile'  => $row['mobile'],
                    'confirm'  => $row['confirm'],
                    'uname'  => $row['owner'],
                    'invoice' => $row['invoice'],
                    'refund' => $row['refund']
                )
            );
        }
        array_push($ret,  array('list' => $rows));
        $nstart = substr($start, 0, 7);
        $stmt = "select * from gulpin_parcel where  DATE_FORMAT(`date`,'%Y-%m') = ? ";
        $params = array($nstart);
        if ($id != "전지사") {
            $stmt = "select * from gulpin_parcel where id=? and DATE_FORMAT(`date`,'%Y-%m') = ? ";
            $params = array($id, $nstart);
        }

        $rs1 = gpMgrQueryText($stmt, $params);
        $rows = [];
        foreach ($rs1 as $row) {
            array_push(
                $rows,
                array(
                    'id'     => $row['id'],
                    'name'   => $row['name'],
                    'date'   => $row['date'],
                    'price'  => $row['price']
                )
            );
        }
        array_push($ret,  array('parcel' => $rows));
        $conn->close();
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    }

    header('Content-Type: application/json');
    //echo json_encode($rows);
    echo json_encode($ret);
}

function SPorAddParcel($data)
{
    $start    = $data['start'];
    $id       = $data['id'];
    $name     = $data['name'];
    $price    = $data['price'];

    // 택배비 등록은 본사만 (order.js 에서도 admin 만 버튼 활성)
    if (!gpMgrIsAdmin(gpMgrActor())) {
        gpMgrRespond(array('error' => '본사만 택배비를 등록할 수 있습니다.'), 403);
    }

    try {
        global $conn;
        $res = "";

        // check id, date exist

        //$stmt = "select * from gulpin_parcel where id='{$id}' and DATE_FORMAT(`date`,'%Y-%m') = '{$start}' ";
        $rs1 = gpMgrQuery("select * from gulpin_parcel where id=? and `date` = ? ", array($id, "{$start} 00:00:00"));

        if (count($rs1) > 0) {
            gpMgrQuery("UPDATE gulpin_parcel SET price = ? WHERE id = ? and `date` = ?", array($price, $id, "{$start} 00:00:00"));
            //$sql = "UPDATE gulpin_parcel SET price =  {$price}  WHERE  id = '{$id}' and  DATE_FORMAT(`date`,'%Y-%m') = '{$start}'";
        } // update
        else {
            if ($start != "")
                gpMgrQuery("insert into gulpin_parcel (id, name, price, `date`) values ( ?, ?, ?, ? )", array($id, $name, $price, $start));
            else
                gpMgrQuery("insert into gulpin_parcel (id, name, price) values ( ?, ?, ? )", array($id, $name, $price));
        }
        $res = TRUE;

        $conn->close();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

    header('Content-Type: application/json');
    if ($res === TRUE) {
        echo json_encode(array("success" => $res));
    } else {
        echo json_encode(array("Error" => $error));
    }
}

function SShowAddr($data)
{
    sqlite_start_session();

    global $conn;

    $id   = $data['id'];
    // 지사는 본인 배송지만
    $actor = gpMgrActor();
    if (!gpMgrIsAdmin($actor)) {
        $id = $actor['id'];
    }

    try {


        $rs = gpMgrQueryText("SELECT * FROM gulpin_addrlist where mid = ?  order by rdate desc", array($id));
        $rows = array();

        $i = 0;

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'id'        => $row['id'],
                    'name'      => $row['name'],
                    'owner'     => $row['owner'],
                    'mobile'    => $row['mobile'],
                    'addr'      => $row['addr'],
                    'zipcode'   => $row['zipcode'],
                    'rdate'     => $row['rdate'],
                )
            );
        }

        $rs = gpMgrQueryText("SELECT * FROM gulpin_user where mid = ?", array($id));

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'id'        => $row['id'],
                    'name'      => $row['name'],
                    'owner'     => $row['owner'],
                    'mobile'    => $row['mobile'],
                    'addr'      => $row['addr'],
                    'zipcode'   => $row['zipcode'],
                    'rdate'     => $row['rdate'],
                )
            );
        }

        $conn->close();
        $result["rows"] = $rows;

        header('Content-Type: application/json');
        echo json_encode(array("success" => $rows));
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(array("Error: " => $e->getMessage()));
    }
}

function SRemoveChild($data)
{
    sqlite_start_session();

    $id  = $data["id"];

    global $conn;
    $res = "";

    try {

        $sql = "DELETE FROM gulpin_user WHERE id = '" . $id . "'";

        if ($conn->query($sql) === TRUE) {
            $res = true;
        } else {
            $res =  json_encode(array("Error deleting record: " . $conn->error));
        }

        $conn->close();
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    }

    echo json_encode($res);
}

function SRemoveAddress($data)    // 프로그램 update 필요함
{
    sqlite_start_session();

    $id  = $data["id"];

    // 지사는 본인 배송지만 삭제
    $actor = gpMgrActor();
    $owner = gpMgrQuery("SELECT mid FROM gulpin_addrlist WHERE id = ?", array($id));
    if (!gpMgrIsAdmin($actor) && (!$owner || $owner[0]['mid'] !== $actor['id'])) {
        gpMgrRespond(array('error' => '본인 배송지만 삭제할 수 있습니다.'), 403);
    }

    global $conn;
    $res = "";

    try {

        gpMgrQuery("DELETE FROM gulpin_addrlist WHERE id = ?", array($id));
        $res = true;

        $conn->close();
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    }

    echo json_encode($res);
}

function SRemovPorID($data)    // 프로그램 update 필요함
{
    $actor = gpOrderActor();
    $id = (string) ($data['id'] ?? '');
    try {
        $pdf = gpPaymentTransaction(function () use ($id, $actor) {
            $order = gpPaymentLockOrder('por', $id);
            if (!gpMgrIsAdmin($actor) && ($order['owner'] !== $actor['id'] || (int) $order['status'] !== 0)) {
                throw new GpPaymentError('물품준비중인 본인 주문만 취소할 수 있습니다.', 403);
            }
            gpPaymentGuardPorEdit($id, null);
            $row = gpPaymentQuery('SELECT pdfname FROM gulpin_porlist WHERE por_id = ?', [$id])[0];
            gpPaymentQuery('DELETE FROM gulpin_porlist WHERE por_id = ?', [$id]);
            return basename((string) $row['pdfname']);
        });
        $path = __DIR__ . '/../assets/por/uploads/' . $pdf;
        if ($pdf !== '' && is_file($path) && !unlink($path)) {
            error_log('[orders] failed to remove order PDF');
        }
        echo json_encode(true);
    } catch (GpPaymentError $e) {
        gpMgrRespond(['error' => $e->getMessage()], $e->getCode());
    }
}

// function SAddDelever($data)    // 프로그램 update 필요함
// {
//     //sqlite_start_session();

//     $id  = $data["id"];
//     $status  = $data["status"];
//     $invoice  = $data["invoice"];

//     global $conn;
//     $res = "";

//     try {
//         $sql = "UPDATE gulpin_porlist  confirm={$status}, invoice='{$invoice}' WHERE por_id='{$id}'";

//         //$sql = "UPDATE gulpin_porlist SET confirm = 1  WHERE  por_id = '{$id}'";

//         if ($conn->query($sql) === TRUE) {
//             $res = true;
//         }

//         $conn->close();
//     } catch (Exception $e) {
//         $res = $e->getMessage();
//     }

//     echo json_encode($res);
// }
function SAddDelever($data)
{
    if (!gpMgrIsAdmin(gpMgrActor())) {
        gpMgrRespond(['error' => '본사만 주문 상태를 변경할 수 있습니다.'], 403);
    }
    $status = (string) ($data['status'] ?? '');
    if (!in_array($status, ['0', '1', '2', '3', '10', '99', '999'], true)) {
        gpMgrRespond(['error' => '올바르지 않은 주문 상태입니다.'], 400);
    }

    try {
        gpPaymentTransaction(function () use ($data, $status) {
            $id = (string) ($data['id'] ?? '');
            gpPaymentLockOrder('por', $id);
            gpPaymentGuardPorEdit($id, (int) $status);
            gpPaymentQuery('UPDATE gulpin_porlist SET confirm = ?, invoice = ?, refund = ? WHERE por_id = ?',
                [(int) $status, ($data['invoice'] ?? '') ?: null, ($data['refund'] ?? '') ?: null, $id]);
        });
        echo json_encode(true);
    } catch (GpPaymentError $e) {
        gpMgrRespond(['error' => $e->getMessage()], $e->getCode());
    }
}


function SRegistermgr2($data)
{

    sqlite_start_session();
    $result = "";

    //$json = file_get_contents('php://input');
    // $arr = json_decode($data, true); 
    // $row = $arr['item'];

    //$id       = $data['items']['id'];
    $name     = $data['items']['name'];
    $owner     = $data['items']['owner'];
    //$password = $data['items']['password'];
    $mobile   = $data['items']['mobile'];
    $addr     = $data['items']['addr'];
    $zipcode  = $data['items']['zipcode'];
    // $idrolebm = $data['items']['idrolebm'];
    $mid      = $data['items']['mid'];
    //$role     = $data['items']['role'];

    // 지사는 본인 배송지로만 등록
    $actor = gpMgrActor();
    if (!gpMgrIsAdmin($actor)) {
        $mid = $actor['id'];
    }

    $error = "";

    try {
        global $conn;

        gpMgrQuery("insert into gulpin_addrlist ( name, owner,  mobile, addr, zipcode,  mid ) values ( ?, ?, ?, ?, ?, ? )",
            array($name, $owner, "{$mobile} ", $addr, $zipcode, $mid));
        $res = TRUE;

        $conn->close();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

    header('Content-Type: application/json');
    if ($res === TRUE) {
        echo json_encode(array("success" => $res));
    } else {
        echo json_encode(array("Error" => $error));
    }
}

function SShowConfirm($data)
{
    sqlite_start_session();

    // 전체 계정(암호 포함) 조회는 본사만 (login/adminLogin.php)
    if (!gpMgrIsAdmin(gpMgrActor())) {
        gpMgrRespond(array('error' => '본사만 조회할 수 있습니다.'), 403);
    }

    $num = $data['num'];

    global $conn;
    
    $sqlString = "SELECT *  FROM gulpin_user where role IN (1, 2, 3, 4, 5, 10, 20, 30, 40, 50) order by rdate desc";

    if ($num == "2")
        $sqlString = "SELECT *  FROM gulpin_user where role IN (1, 2, 3, 4, 5, 10, 20, 30, 40, 50) and (confirm = 0 or confirm is null) order by rdate desc";
    else if ($num == "1")
        $sqlString = "SELECT *  FROM gulpin_user where role IN (1, 2, 3, 4, 5, 10, 20, 30, 40, 50) and confirm = 1 order by rdate desc";


    $rs = sqlite_query($conn, $sqlString);
    $rows = array();

    $i = 0;

    try {

        while ($row = sqlite_fetch_array($rs)) {
            array_push(
                $rows,
                array(
                    'id'        => $row['id'],
                    'name'      => $row['name'],
                    'password'  => gpPasswordHashed((int) $row['role']) ? '' : $row['password'],
                    'mobile'    => $row['mobile'],
                    'addr'      => $row['addr'],
                    'zipcode'   => $row['zipcode'],
                    'confirm'   => $row['confirm'],
                    'rdate'     => $row['rdate'],
                    'role'      => $row['role'],
                )
            );
        }
        $conn->close();
    } catch (Exception $e) {
        json_encode($e->getMessage());
    }


    header('Content-Type: application/json');
    echo json_encode($rows);
}
function SShowConfirm2($data)
{
    sqlite_start_session();

    // 전체 계정(암호 포함) 조회는 본사만 (login/adminLogin.php)
    if (!gpMgrIsAdmin(gpMgrActor())) {
        gpMgrRespond(array('error' => '본사만 조회할 수 있습니다.'), 403);
    }

    $num = $data['num'];

    global $conn;
    
    if ( $num == "0" )
        $sqlString = "SELECT *  FROM gulpin_user where role IN (1, 2, 3, 4, 5, 10, 20, 30, 40, 50) order by rdate desc";
    else
        $sqlString = "SELECT *  FROM gulpin_user where role = " . (int) $num . " order by rdate desc";



    $rs = sqlite_query($conn, $sqlString);
    $rows = array();

    $i = 0;

    try {

        while ($row = sqlite_fetch_array($rs)) {
            array_push(
                $rows,
                array(
                    'id'        => $row['id'],
                    'name'      => $row['name'],
                    'password'  => gpPasswordHashed((int) $row['role']) ? '' : $row['password'],
                    'mobile'    => $row['mobile'],
                    'addr'      => $row['addr'],
                    'zipcode'   => $row['zipcode'],
                    'confirm'   => $row['confirm'],
                    'rdate'     => $row['rdate'],
                    'role'      => $row['role'],
                )
            );
        }
        $conn->close();
    } catch (Exception $e) {
        json_encode($e->getMessage());
    }


    header('Content-Type: application/json');
    echo json_encode($rows);
}

function SShowConfirmUpdatePOR($data)
{
    if (!gpMgrIsAdmin(gpMgrActor())) {
        gpMgrRespond(['error' => '본사만 주문 상태를 변경할 수 있습니다.'], 403);
    }
    $id = (string) ($data['data']['porid'] ?? '');
    try {
        gpPaymentTransaction(function () use ($id) {
            gpPaymentLockOrder('por', $id);
            gpPaymentGuardPorEdit($id, 1);
            gpPaymentQuery('UPDATE gulpin_porlist SET confirm = 1 WHERE por_id = ?', [$id]);
        });
        header('Content-Type: application/json');
        echo json_encode(['result: ' => true]);
    } catch (GpPaymentError $e) {
        gpMgrRespond(['error' => $e->getMessage()], $e->getCode());
    }
}

function SShowStudentList($data)
{
    sqlite_start_session();

    global $conn;
    $tid = $data['id'];
    $step = $data['step'];
    $sel = $data['sel'];
    if (isset($data['kgarden']))   //
        $kgarden    = $data['kgarden'];

    // "admin"(전체) 조회는 본사만, 그 외는 본인/하위 계정의 학생만
    $user = gpSessionUser();
    if ($tid == "admin" ? $user['role'] !== GP_ROLE_ADMIN : !gpCanView($user, (string) $tid)) {
        gpMgrRespond(array('error' => '조회 권한이 없습니다.'), 403);
    }

    $params = array();
    if ($tid == "admin") {
        if ($sel == '1') {
            if ($step == '전체')
                $sqlString = "SELECT *  FROM gulpin_user where  role = 100";
            else {
                //$sqlString = "SELECT *  FROM gulpin_user where  step = '{$step}' and role = 100 and tid='{$kgarden}'";
                $sqlString = "SELECT *  FROM gulpin_user au, (select id from gulpin_user ) u where u.id = au.tid and role = 100 and step=?";
                $params = array($step);
            }
        } else if ($sel == '2') {

            //$sqlString = "SELECT *  FROM gulpin_user where classnm = '{$step}' and role = 100 and tid='{$kgarden}'";
            $sqlString = "SELECT *  FROM gulpin_user au, (select id from gulpin_user ) u where u.id = au.tid and role = 100 and classnm=?";
            $params = array($step);
        } else if ($sel == '3')
            //$sqlString = "SELECT *  FROM gulpin_user where tid = '{$step}' and role = 100";
            if ($step == '전체')
                //$sqlString = "SELECT *  FROM gulpin_user where  role = 100";
                $sqlString = "SELECT * , u.owner FROM gulpin_user au, (select id uid, owner from gulpin_user  ) u where u.uid = au.tid and role = 100";
            else
                $sqlString =  "SELECT au.*  FROM gulpin_user au, (select id from gulpin_user ) u where u.id = au.tid and role = 100";
    } else {
        $params = array($tid);
        if ($sel == '1') {
            if ($step == '전체')
                $sqlString = "SELECT *  FROM gulpin_user where tid = ? ";
            else {
                $sqlString = "SELECT *  FROM gulpin_user where tid = ? and step = ?";
                $params[] = $step;
            }
        } else if ($sel == '2') {
            $sqlString = "SELECT *  FROM gulpin_user where tid = ? and classnm = ?";
            $params[] = $step;
        }
        else if ($sel == '3')
            if ($step == '전체')
                $sqlString = "SELECT *  FROM gulpin_user where tid = ? ";
            else {
                $sqlString = "SELECT *  FROM gulpin_user where tid = ? and owner = ?";
                $params[] = $step;
            }
        else if ($sel == '4')
            if ($step == '전체')
                $sqlString = "SELECT *  FROM gulpin_user where tid = ? ";
            else
                $sqlString = "SELECT *  FROM gulpin_user where tid = ? and status = 1";
    }
    $rows = array();

    $i = 0;

    try {

        $rs = gpMgrQueryText($sqlString, $params);

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'id'      => $row['id'],
                    'name'  => $row['name'],
                    'passwd'   => $row['password'],
                    'step'    => $row['step'],
                    'mobile'  => $row['mobile'],             // 원관리 추가가
                    'p_mobile'  => $row['p_mobile'],         // 원관리 추가가
                    'addr'  => $row['addr'],                 // 원관리 추가가
                    'rdate'   => $row['rdate'],
                    'classnm' => $row['classnm'],
                    'owner'   => $row['owner'],
                    'status'  => $row['status'],
                    'etc'     => $row['etc'],
                    'tid'     => $row['tid']
                )
            );
        }
        $conn->close();
    } catch (Exception $e) {
        echo  json_encode(array("error:" => $e->getMessage()));
    }

    header('Content-Type: application/json');
    echo json_encode(array("json" => $rows));
}

function SShowStudyList($data)
{
    sqlite_start_session();

    global $conn;
    $tid = $data['id'];
    $step = $data['step'];
    $start = $data['start'];
    $end = $data['end'];

    if (!gpCanView(gpSessionUser(), (string) $tid)) {
        gpMgrRespond(array('error' => '조회 권한이 없습니다.'), 403);
    }

    $tsql = "select u.id id, u.name name , s.volume v, s.step s, s.uid uid, count(u.id) cnt from study_record s, gulpin_user u where ";
    if ($step == '전체') {
        $sqlString = $tsql . "u.id = s.id and u.tid = ?  and  s.rdate >= ? and s.rdate <= ?  group by u.id";
        $params = array($tid, $start, $end);
    } else {
        $sqlString = $tsql . " s.step = ? and u.id = s.id and u.tid = ?  and  s.rdate >= ? and s.rdate <= ? group by u.id, s.step";
        $params = array($step, $tid, $start, $end);
    }

    $rows = array();

    $i = 0;

    try {

        $rs = gpMgrQueryText($sqlString, $params);

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'id'    => $row['id'],
                    'name'  => $row['name'],
                    'cnt'   => $row['cnt'],
                )
            );
        }
        $conn->close();
    } catch (Exception $e) {
        echo  json_encode(array("error:" => $e->getMessage()));
    }

    header('Content-Type: application/json');
    echo json_encode(array("json" => $rows));
}

function SCheckStudentID($data)
{
    sqlite_start_session();

    global $conn;
    $result = 0;
    $id = $data['id'];

    $sql = "select id from  gulpin_user where id like '{$id}%' and role IN (100, 200, 300, 400, 500)";

    $rows = array();

    $i = 0;
    $max = -1;
    try {

        $rs = sqlite_query($conn, $sql);
        while ($row = sqlite_fetch_array($rs)) {
            array_push(
                $rows,
                array(
                    'id' => $row['id'],
                )
            );
        }
        $num = count($rows);
        for ($i = 0; $i < $num; $i++) {
            $newid = substr($rows[$i]['id'], strlen($id));
            if ($max < (int)$newid)
                $max = (int)$newid;
        }
        $conn->close();

        if (sqlite_num_rows($rs) > 0) {
            if ($max > -1)
                $result = $max;
        }
    } catch (Exception $e) {
        echo  json_encode(array("error:" => $e->getMessage()));
    }

    header('Content-Type: application/json');
    echo json_encode(array("result" => $result));
}

function SShowStudyList2($data)
{
    sqlite_start_session();

    global $conn;
    $tid = $data['id'];
    $classnm = $data['classnm'];
    $start = $data['start'];
    $end = $data['end'];

    if (!gpCanView(gpSessionUser(), (string) $tid)) {
        gpMgrRespond(array('error' => '조회 권한이 없습니다.'), 403);
    }

    $tsql = "select u.id id, u.name name ,u.classnm classnm, s.volume v, s.step s, s.uid uid, count(u.id) cnt from study_record s, gulpin_user u where ";
    if ($classnm == '전체' || $classnm == "") {
        $sqlString = $tsql . "u.id = s.id and u.tid = ? and  s.rdate >= ? and s.rdate <= ? group by u.id";
        $params = array($tid, $start, $end);
    } else {
        $sqlString = $tsql . " u.classnm = ? and u.id = s.id and u.tid = ? and s.rdate >= ? and s.rdate <= ? group by u.id";
        $params = array($classnm, $tid, $start, $end);
    }

    $rows = array();

    $i = 0;

    try {

        $rs = gpMgrQueryText($sqlString, $params);

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'id'    => $row['id'],
                    'name'  => $row['name'],
                    'cnt'   => $row['cnt'],
                )
            );
        }
        $conn->close();
    } catch (Exception $e) {
        echo  json_encode(array("error:" => $e->getMessage()));
    }

    header('Content-Type: application/json');
    echo json_encode(array("json" => $rows));
}

function SShowClassList($data)
{
    sqlite_start_session();

    $tid = $data['id'];
    //$kgarden = $data['kgarden'];

    global $conn;

    // "admin"(전체) 조회는 본사만, 그 외는 본인/하위 계정만
    $user = gpSessionUser();
    if ($tid == "admin" ? $user['role'] !== GP_ROLE_ADMIN : !gpCanView($user, (string) $tid)) {
        gpMgrRespond(array('error' => '조회 권한이 없습니다.'), 403);
    }

    $sqlString = "select DISTINCT classnm from gulpin_user where tid = ?";
    $params = array($tid);

    if ($tid == "admin") {
        //$sqlString = "select DISTINCT classnm from gulpin_user where tid = '{$kgarden}' and classnm is not null";
        $sqlString = "SELECT DISTINCT classnm FROM gulpin_user au, (select id from gulpin_user ) u where u.id = au.tid and role = 100";
        $params = array();
    }
    $rows = array();

    $i = 0;

    try {

        $rs = gpMgrQueryText($sqlString, $params);


        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'classnm'    => $row['classnm'],
                )
            );
        }
        $conn->close();
    } catch (Exception $e) {
        echo  json_encode(array("error:" => $e->getMessage()));
    }

    header('Content-Type: application/json');
    echo json_encode(array("success" => $rows));
}

function SShowKgardenList($data)
{
    sqlite_start_session();

    $tid = $data['id'];

    global $conn;

    // "admin"(전체) 조회는 본사만, 그 외는 본인/하위 계정만
    $user = gpSessionUser();
    if ($tid == "admin" ? $user['role'] !== GP_ROLE_ADMIN : !gpCanView($user, (string) $tid)) {
        gpMgrRespond(array('error' => '조회 권한이 없습니다.'), 403);
    }

    $sqlString = "select DISTINCT owner from gulpin_user where mid = ? and owner is not null and role=10";
    $params = array($tid);

    if ($tid == "admin") {
        $sqlString = "select DISTINCT owner from gulpin_user where owner is not null and role=10";
        $params = array();
    }
    $rows = array();

    $i = 0;

    try {

        $rs = gpMgrQueryText($sqlString, $params);

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'owner'    => $row['owner'],
                )
            );
        }
        $conn->close();
    } catch (Exception $e) {
        echo  json_encode(array("error:" => $e->getMessage()));
    }

    header('Content-Type: application/json');
    echo json_encode(array("success" => $rows));
}

function SinsertStudent($data)
{

    global $location;
    global $conn;

    sqlite_start_session();

    $id = "";
    $result = "";

    try {

        foreach ($data['items'] as $row) {

            $id      = $conn->real_escape_string($row['id']);
            $name    = $conn->real_escape_string($row['name']);
            $password = $conn->real_escape_string($row['passwd']);
            $tid     = $conn->real_escape_string($row['tid']);
            $classnm = $conn->real_escape_string($row['classnm']);
            $step    = $conn->real_escape_string($row['step']);
            $role    = 100; // 유치원생
            $confirm = 1;
            $sql = "INSERT INTO gulpin_user (id, name, password, role, tid, classnm, rdate, step, confirm)
                    VALUES ('{$id}', '{$name}', '{$password}', {$role}, '{$tid}', '{$classnm}', NOW(), '{$step}', {$confirm})
                    ON DUPLICATE KEY UPDATE
                        name     = VALUES(name),
                        password = VALUES(password),
                        classnm  = VALUES(classnm),
                        step     = VALUES(step)";

            if ($conn->query($sql) === TRUE) {
                $result = $conn->affected_rows === 1 ? "New record created successfully" : "Record updated successfully";
            } else {
                $result = array("error:" => $sql . "<br>" . $conn->error);
            }
        }

        $conn->close();
    } catch (Exception $e) {
        echo  json_encode(array("error:" => $e->getMessage()));
    }

    echo json_encode($result);
}

function SupdateStudent($data)
{

    global $location;
    global $conn;

    sqlite_start_session();

    $id = "";
    $result = "";

    try {
        foreach ($data['items'] as $row) {

            $id = $row['id'];
            $name = $row['name'];
            $password = $row['passwd'];
            $tid = $row['tid'];
            $classnm = $row['classnm'];
            $step = $row['step'];
            $role = 0;
            $confirm = 1;
            $sql = "update gulpin_user set  
                    name     = '" . $row['name'] . "',
                    password = '" . $row['passwd'] . "',                     
                    classnm  = '" . $row['classnm'] . "',
                    step     = '" . $row['step'] . "'
                    WHERE id = '" . $row['id'] . "' and tid ='" . $row['tid'] . "'";
            if ($conn->query($sql) === TRUE) {
                $result =  "New record created successfully";
            } else {
                $result =  "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        $conn->close();
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    }

    echo json_encode($result);
}

function SmanageStudent($data)
{
    global $location;
    global $conn;
    sqlite_start_session();

    $result = "";

    try {
        // 학생 role 은 담당 선생 role 로 결정 (학원 20→200, 홈스쿨 30→300, 화상수업 40→400, 방과후 50→500)
        $teacherRoles = array();
        foreach ($data['items'] as $row) {
            $id = $row['id'];
            $name = $row['name'];
            $password = $row['passwd'];
            $tid = $row['tid'];
            $classnm = $row['classnm'];
            $step = $row['step'];
            if (!isset($teacherRoles[$tid])) {
                $teacher = gpMgrQuery('SELECT role FROM gulpin_user WHERE id = ?', array($tid));
                $teacherRoles[$tid] = gpStudentRoleOf((int) ($teacher[0]['role'] ?? 0)) ?? 200;
            }
            $role = $teacherRoles[$tid];
            $p_mobile = $row['p_mobile'];
            $mobile  = $row['mobile'];
            $addr  = $row['addr'];
            $status = (int)($row['status']);
            $etc = $row['etc'];
            $confirm = 1;

            // ID 존재 여부 확인
            $checkSql = "SELECT COUNT(*) as count FROM gulpin_user WHERE id = '{$id}' AND tid = '{$tid}'";
            $checkResult = $conn->query($checkSql);
            $exists = $checkResult->fetch_assoc()['count'] > 0;

            // 존재 여부에 따라 쿼리 생성
            if (!$exists) {
                // INSERT
                $sql = "INSERT INTO gulpin_user (id, name, password, role, tid, classnm, rdate, step, confirm, p_mobile, mobile, addr, etc) 
                        VALUES ('{$id}', '{$name}', '{$password}', {$role}, '{$tid}', '{$classnm}', NOW(), '{$step}', {$confirm}, '{$p_mobile}', '{$mobile}', '{$addr}', '{$etc}')";
                $action = "created";
            } else {
                // UPDATE
                $sql = "UPDATE gulpin_user 
                        SET name = '{$name}', 
                            password = '{$password}', 
                            classnm = '{$classnm}', 
                            step = '{$step}',
                            p_mobile = '{$p_mobile}',
                            mobile = '{$mobile}',
                            addr =  '{$addr}',
                            status = {$status},
                            etc = '{$etc}'
                        WHERE id = '{$id}' 
                        AND   tid = '{$tid}'";
                $action = "updated";
            }

            // 쿼리 실행
            if ($conn->query($sql) === TRUE) {
                $result = array(
                    "status" => "success"
                    // "message" => "Record " . $action . " successfully"
                );
            } else {
                $result = array(
                    "status" => "error",
                    "message" => $conn->error,
                    "sql" => $sql
                );
            }
        }
        $conn->close();
    } catch (Exception $e) {
        $result = array(
            "status" => "error",
            "message" => $e->getMessage()
        );
    }
    header('Content-Type: application/json');
    echo json_encode($result);
}
function SUploadBoardPDF($data) {
    $id      = $data['id'];
    $porlist = $data['postlist'];
    $addr    = $data['addr'];
    $mobile  = $data['mobile'];
    $order   = $data['order'];
    $zip     = isset($data['zip']) ? $data['zip'] : '';
    $pdfname = '';
    $user    = 'admin';
    
    global $conn;
    global $location;

    // 금액은 가격표(price_catalog.json)로 다시 확인, 주문번호는 서버에서 생성 (P + yy 로 시작: order.php 연도 필터)
    $actor = gpOrderActor();
    if (!gpMgrIsAdmin($actor)) {
        $id = $actor['id'];
    }
    if (($data['agree'] ?? '') !== '1') {
        gpMgrRespond(array('error' => '주문 내용 확인 및 필수 항목에 동의해 주세요.'), 400);
    }
    $pgAmount = gpOrderAmount($actor, (string) $porlist);
    $porid = 'P' . date('ymdHis') . bin2hex(random_bytes(4));

    $fileUploadError = '';
    
    try {
        // DB INSERT 먼저 수행 (파일 업로드 실패와 무관하게 처리)
        $sqlstring = "
            INSERT INTO gulpin_porlist (id, por_id, por_list, rdate, `order`, addr, mobile, pdfname, zip, pg_amount)
            VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($sqlstring);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssssssssi", $id, $porid, $porlist, $order, $addr, $mobile, $pdfname, $zip, $pgAmount);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $insertId = $conn->insert_id; // 생성된 레코드 ID 저장
        $stmt->close();

        // 파일 업로드 처리 (에러가 발생해도 DB insert는 유지)
        try {
            foreach ($_FILES as $file) {
                $fileName     = $file['name'];
                $fileTempName = $file['tmp_name'];
                $fileError    = $file['error'];

                // 파일 업로드 오류 체크
                if ($fileError !== UPLOAD_ERR_OK) {
                    throw new Exception("File upload error (code {$fileError})");
                }

                if (!is_uploaded_file($fileTempName)) {
                    throw new Exception("File is not a valid uploaded file.");
                }

                // 주문서는 PDF 만 (확장자 + 파일 시작 "%PDF")
                if (!gpUploadAllowed($fileName, array('pdf')) || file_get_contents($fileTempName, false, null, 0, 4) !== '%PDF') {
                    throw new Exception("PDF 파일만 업로드할 수 있습니다.");
                }

                $RandomName = generateRandomString(15);
                $pdfname    = $RandomName . '.pdf';

                if (!move_uploaded_file($fileTempName, __DIR__ . "/../assets/por/uploads/" . $pdfname)) {
                    throw new Exception("Failed to move uploaded file.");
                }

                // 파일 업로드 성공 후 DB 업데이트 (pdfname 업데이트)
                $updateSql = "UPDATE gulpin_porlist SET pdfname = ? WHERE id = ? AND por_id = ?";
                $updateStmt = $conn->prepare($updateSql);
                if ($updateStmt) {
                    $updateStmt->bind_param("sss", $pdfname, $id, $porid);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
            }
        } catch (Exception $fileException) {
            // 파일 업로드 에러는 저장하되 DB insert는 유지
            $fileUploadError = $fileException->getMessage();
        }

        // DB insert는 성공했으므로 성공 응답
        $result = [
            'status' => 'success', 
            'porid' => $porid,
            'url' => !empty($pdfname) ? 'order-document.php?id=' . rawurlencode($porid) . '&db=sqlite' : '',
            'file_error' => $fileUploadError // 파일 업로드 에러가 있다면 포함
        ];
        $conn->close();

    } catch (Exception $e) {
        // DB 에러만 실패로 처리
        $result = [
            'status'  => 'error',
            'message' => $e->getMessage()
        ];
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result);
}

function SUploadBoard($data)
{
    sqlite_start_session();
    $content   = $data['idContent'];
    $desc   = $data['idDesc'];
    $cate   = $data['idSelect2'];
    $user =  'admin';
    $user =   $data['user'];
    set_time_limit(300);

    gpRequireBoardWriter();

    global $conn;
    $rows = array();

    if (!empty($user)) {
        // 자료실 첨부 허용 확장자 (php·html·svg 등 실행/스크립트 가능한 파일 차단)
        $allowedExts = array('pdf', 'hwp', 'hwpx', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv',
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp3', 'wav', 'mp4', 'zip');
        foreach ($_FILES as $file) {
            if (!gpUploadAllowed((string) $file['name'], $allowedExts)) {
                gpMgrRespond(array('error' => '업로드할 수 없는 파일 형식입니다: ' . $file['name']), 400);
            }
        }
        foreach ($_FILES as $index => $file) {
            // for easy access
            $fileName     = $file['name'];
            // for easy access
            $fileTempName = $file['tmp_name'];
            // check if there is an error for particular entry in array
            if (!empty($file['error'][$index])) {
                return false;
            }
            // check whether file has temporary path and whether it indeed is an uploaded file
            if (!empty($fileTempName) && is_uploaded_file($fileTempName)) {
                // move the file from the temporary directory to somewhere of your choosing
                $RandomName = generateRandomString(15);
                move_uploaded_file($fileTempName, "../assets/board/uploads/" . $RandomName . $fileName);
                //move_uploaded_file($fileTempName, "uploads/" . $fileName);
                $tmp = $fileTempName;
            }
            array_push($rows, array(
                'name'     => $fileName,
                'fakename' => $RandomName . $fileName,
                'size'     => $file['size'],
            ));
        }
        $jsarr = json_encode($rows, JSON_UNESCAPED_UNICODE);
        try {

            gpMgrQuery("insert into gulpin_repository ( title, id, contents, `desc`, rdate, cate ) values ( ?, ?, ?, ?, NOW(), ? )",
                array($content, $user, $jsarr, $desc, $cate));
            $result = true;

            $conn->close();
        } catch (Exception $e) {
            echo json_encode(array("error:" => $e->getMessage()));
        }

        echo json_encode(array("result:" => $result));
    } else {
        Header("Location: login.php");
    }
}
function SUdateBoard($data)
{
    $id = $data['idNum'];
    $content = $data['idContent'];
    $desc = $data['idDesc'];
    $cate = $data['idSelect2'];
    $user = 'admin';
    $user = $data['user'];

    gpRequireBoardWriter();

    global $conn;

    if (!empty($user)) {
        try {
            // Update query
            gpMgrQuery("UPDATE gulpin_repository
                          SET title = ?,
                              `desc` = ?,
                              cate = ?,
                              rdate = NOW()
                          WHERE num = ?", array($content, $desc, $cate, $id));
            $result = true;

            $conn->close();
        } catch (Exception $e) {
            echo json_encode(array("error:" => $e->getMessage()));
        }

        echo json_encode(array("result:" => $result));
    } else {
        Header("Location: login.php");
    }
}

function SShowBoardList($data)
{
    sqlite_start_session();

    $num = $data['num'];
    $cate = $data['cate'];

    global $conn;

    try {
        $params = array();
        if ($cate == "1") {
            if ($num == "전체")
                $sqlString = "SELECT num, title, id, contents, `desc`, rdate, cate  FROM gulpin_repository order by num desc";
            else {
                $sqlString = "SELECT num, title, id, contents, `desc`, rdate, cate  FROM gulpin_repository where cate = ? order by num desc";
                $params = array($num);
            }
        } else {
            $sqlString = "SELECT num, title, id, contents, `desc`, rdate, cate  FROM gulpin_repository where num = ? order by num desc";
            $params = array($num);
        }

        $rs = gpMgrQueryText($sqlString, $params);
        $rows = array();

        $i = 0;

        foreach ($rs as $row) {
            array_push(
                $rows,
                array(
                    'num'      => $row['num'],
                    'title'    => $row['title'],
                    'id'       => $row['id'],
                    'contents' => $row['contents'],
                    'desc'     => $row['desc'],
                    'rdate'    => $row['rdate'],
                    'cate'     => $row['cate'],
                )
            );
        }
    } catch (Exception $e) {
        return json_encode($e->getMessage());
    }

    $result["rows"] = $rows;
    header('Content-Type: application/json');
    echo json_encode($rows);
}

function SDeleteBoardlist($data)
{
    sqlite_start_session();

    $num  = $data["num"][0];

    gpRequireBoardWriter();

    global $conn;
    $res = "";
    $rows = array();
    $cont = "";

    try {
        $rs = gpMgrQueryText("SELECT contents from gulpin_repository WHERE num = ?", array($num));

        foreach ($rs as $row) {
            $cont =  $row['contents'];
        }
        $jsobj = json_decode($cont, true);

        if ($jsobj !== null) {
            $arrlen = count($jsobj);
            for ($i = 0; $i < $arrlen; $i++) {
                // 파일명에 경로(../)가 섞여도 업로드 폴더 밖은 지우지 않도록 basename
                if (unlink('../assets/board/uploads/' . basename((string) $jsobj[$i]['fakename']))) {
                    $res = true;
                } else {
                    $res = false;
                }
            }
        }

        gpMgrQuery("DELETE FROM gulpin_repository WHERE num = ?", array($num));
        $res = true;

        $conn->close();
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    }

    echo json_encode($res);
}

/** 게시판 등록/수정/삭제는 본사 또는 게시판 계정만 (board/boardmgr.php 버튼 조건과 같음) */
function gpRequireBoardWriter(): void
{
    $user = gpSessionUser();
    if ($user['role'] !== GP_ROLE_ADMIN && $user['id'] !== '게시판') {
        gpMgrRespond(array('error' => '게시판 관리 권한이 없습니다.'), 403);
    }
}

/** 업로드 파일명의 확장자가 허용 목록에 있는지 */
function gpUploadAllowed(string $fileName, array $exts): bool
{
    return in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $exts, true);
}

function generateRandomString($length = 15)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function resize_image($file, $w, $h, $crop = FALSE)
{

    $percent = 0.5;

    list($width, $height) = getimagesize($file);
    $newwidth = $width * $percent;
    $newheight = $height * $percent;

    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresized($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    imagejpeg($dst);

    return $dst;
}

function SGetListPrice($data)
{
    sqlite_start_session();

    global $conn;

    $sqlString = "select MAX(price) price from gulpin_price";

    $rows = array();

    $i = 0;

    try {

        $rs = sqlite_query($conn, $sqlString);

        while ($row = sqlite_fetch_array($rs)) {
            array_push(
                $rows,
                array(
                    'price'    => $row['price'],
                )
            );
        }
        $conn->close();
    } catch (Exception $e) {
        echo  json_encode(array("error:" => $e->getMessage()));
    }

    header('Content-Type: application/json');
    echo json_encode(array("success" => $rows));
}


function getClientIP(): string {
    $ip = '';
    // 순서 중요: X-Forwarded-For가 여러 값이면 첫번째가 원래 클라이언트
    $keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    foreach ($keys as $k) {
        if (!empty($_SERVER[$k])) {
            $val = $_SERVER[$k];
            // X-Forwarded-For 같은 경우 콤마로 여러 IP가 올 수 있음
            if (strpos($val, ',') !== false) {
                $parts = array_map('trim', explode(',', $val));
                foreach ($parts as $p) {
                    if (filter_var($p, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $p;
                    }
                }
            } else {
                if (filter_var($val, FILTER_VALIDATE_IP) !== false) {
                    return $val;
                }
            }
        }
    }
    return '0.0.0.0';
}
function lookupIPGeo(string $ip): array {
    // 예: 무료 ip-api 사용 (상업적/대량 요청은 유의)
    $url = "http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,query";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $res = curl_exec($ch);
    curl_close($ch);
    if ($res === false) return [];
    $data = json_decode($res, true);
    if (!empty($data['status']) && $data['status'] === 'success') {
        return $data;
    }
    return [];
}

function saveClientlog($id, $name, $role) {
    // require_once 'sqlite_compat.php'; // 필요 시 주석 해제
    global $conn;

    // 클라이언트 정보 수집
    $ip       = getClientIP();
    $time     = date('Y-m-d H:i:s');
    $uri      = $_SERVER['REQUEST_URI'] ?? '';
    $ua       = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $referer  = $_SERVER['HTTP_REFERER'] ?? '';
    $country  = lookupIPGeo($ip); // GeoIP API에서 가져올 수 있음

    // country가 배열 또는 비어있을 경우 처리
    if (is_array($country)) {
        $country = $country['country'] ?? 'Unknown';
    } elseif (empty($country)) {
        $country = 'Unknown';
    }

    // SQL 준비
    $sql = "INSERT INTO visitors (user_id, `name`, visit_time, ip, uri, user_agent, referer, country, `role`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // bind_param 타입 지정: s = string, i = integer
        $stmt->bind_param("ssssssssi", $id, $name, $time, $ip, $uri, $ua, $referer, $country, $role);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("쿼리 준비 실패: " . $conn->error);
    }

    // $conn->close(); // 연결을 여기서 닫는 것은 권장되지 않음 (재사용 가능성 고려)
}
