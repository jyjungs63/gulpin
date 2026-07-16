<?php

require_once 'dbinit.php';

$urlFromGET = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['functionName'])) {
        $functionName = $_POST['functionName'];

        if (isset($_GET['dest'])) {
            $urlFromGET = $_GET['dest'];
        }

        if (is_callable($functionName)) {
            if (
                "SUploadBoardPDF" == $functionName
                || "SUploadBoard" == $functionName
                || "Sfindpassword" == $functionName
                || "SUdateBoard" == $functionName
                || "SShoppingSaveProduct" == $functionName
            )
                $functionName($_POST);
            else if ("Slogon" == $functionName || "SRegister" == $functionName)
                $functionName($_POST, $urlFromGET);
            else {
                $otherData = isset($_POST['otherData']) ? $_POST['otherData'] : array();
                $functionName($otherData);
            }
        }
    }
}
function Sfindpassword($data)
{
    $Id     = $data["id"];
    $Mobile = $data["mobile"];

    if (!empty($Id) && !empty($Mobile)) {

        $res = CheckPasswd($Id, $Mobile);

        if ($res) {
            $characterToRemove = "-";
            $passwd1 = str_replace(' ', '', str_replace($characterToRemove, "", $res['mobile']));
            $passwd2 = str_replace(' ', '', str_replace($characterToRemove, "", $Mobile));
            if ($passwd1 == $passwd2)
                echo json_encode(array("success" => $res['password']));
            else
                echo json_encode(array("error" => "사용자 아이디 와 휴대폰 번호가 일치하지 않아요"), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array("error" => "사용자 아이디 와 휴대폰 번호가 일치하지 않아요"), JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(array("error" => "사용자 아이디 와 휴대폰을 입력하세요"), JSON_UNESCAPED_UNICODE);
    }
}
function CheckPasswd($login, $password)
{
    global $conn;
    $user = "";
    $login = mysqli_escape_string($conn, $login);
    $rs = mysqli_query($conn, "select * from chaitalk_user where id='{$login}' ");

    if ($rs) {
        $user = mysqli_fetch_assoc($rs);
        $passwordnew = mysqli_escape_string($conn, password_hash($password, PASSWORD_BCRYPT));
        mysqli_free_result($rs);
    }
    $conn->close();
    return $user;
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

        if ($res) {
            session_start();
            $_SESSION["authenticated"] = 'true';
            $_SESSION["user"] = $res['id'];
            $_SESSION["name"] = $res['name'];
            $_SESSION["role"] = $res['role'];
            $_SESSION["confirm"] = $res['confirm'];
            $_SESSION["location"] = $location;
            $_SESSION["dest"] = $dest;

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
                    'clas'  => $res['etc']
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
        $sql = "UPDATE chaitalk_user SET logstat = 1 , logdate = now()  WHERE id = '{$id}' ";
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
    $login = mysqli_escape_string($conn, $login);
    $rs = mysqli_query($conn, "select * from chaitalk_user where id='{$login}'");

    if ($rs) {
        $user = mysqli_fetch_assoc($rs);
        mysqli_free_result($rs);
    }
    $conn->close();
    return $user;
}

function CheckUser($login, $password)
{
    global $conn;
    $user = "";
    $login = mysqli_escape_string($conn, $login);
    $rs = mysqli_query($conn, "select * from chaitalk_user where id='{$login}' and status = 1 and confirm = 1 ");

    if ($rs) {
        $user = mysqli_fetch_assoc($rs);
        $passwordnew = mysqli_escape_string($conn, password_hash($password, PASSWORD_BCRYPT));
        if ($user &&  strcmp($password, $user['password']) != 0) {
            $user = "";
        }
        mysqli_free_result($rs);
    }
    //$conn->close();
    return $user;
}


function SRegister($data, $dest)
{

    $id       = $data['id'];
    $name     = $data['name'];
    $password = $data['password'];
    $mobile   = $data['mobile'];
    $addr     = $data['addr'];
    $zipcode  = $data['zipcode'];

    $role = 0;
    if (isset($data['idrolebm']))   // 지사장
        $role = 1;
    if (isset($data['idrolet']))     // 원장, 선생님
        $role = 2;
    if (isset($data['idrolesite']))     // 가맹
        $role = 3;
    if (isset($data['idroleother']))     // 일반 유료 회원
        $role = 4;

    global $conn;

    try {

        $id = mysqli_escape_string($conn, $id);

        $sqlstring = "insert into chaitalk_user (id, name, password, mobile, addr, zipcode, role, confirm) 
        values ( '{$id}', '{$name}','{$password}', '{$mobile} ','{$addr}', '{$zipcode}', $role , 1)";

        $res = mysqli_query($conn, $sqlstring);

        $conn->close();
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    }

    header('Content-Type: application/json');
    if ($res === TRUE) {
        $result = true;
        //header('location: ../login/login.php');
        echo json_encode(array('success' => '../login/login.php'));
    } else {
        $result = json_encode(array("falure: " => $conn->error));
        echo $result;
    }
}

function SstudentList($data)
{
    session_start();

    $tid       = $data['tid'];
    $role     = $data['role'];

    global $conn;

    $sqlString = "SELECT name FROM chaitalk_user u where u.tid = '{$tid}' and role = {$role}";

    $rows = array();

    $i = 0;

    try {

        $rs = mysqli_query($conn, $sqlString);

        while ($row = mysqli_fetch_array($rs)) {
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

    session_start();

    $id = $data['id'];

    global $conn;

    //$sqlString = "SELECT p.*, u.name FROM chaitalk_porlist p chaitalk_user u where confirm = 0";
    if ($id == "admin")
        $sqlString = "SELECT p.*, u.owner bname FROM chaitalk_porlist p , chaitalk_user u where u.id = p.id order by p.rdate ";
    //$sqlString = "SELECT p.*, u.owner bname FROM chaitalk_porlist p , chaitalk_user u where u.id = p.id and p.confirm = 0";
    else
        $sqlString = "SELECT p.*, u.owner bname FROM chaitalk_porlist p , chaitalk_user u where u.id = p.id and  u.id = '{$id}' order by p.rdate";
    //$sqlString = "SELECT p.*, u.owner bname FROM chaitalk_porlist p , chaitalk_user u where u.id = p.id and p.confirm = 0 and u.id = '{$id}'";

    $rows = array();

    $i = 0;

    try {

        $rs = mysqli_query($conn, $sqlString);

        while ($row = mysqli_fetch_array($rs)) {
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
    session_start();

    global $conn;

    $pid  = $data['id'];
    $rows = array();
    $i = 0;
    $res = "";

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = "select * from chaitalk_porlist where por_id='{$pid}' ";

    try {

        $rs = mysqli_query($conn, $stmt);

        while ($row = mysqli_fetch_array($rs)) {
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
    session_start();

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
    $tsql = "select p.*, u.owner owner from chaitalk_porlist p ,  chaitalk_user u  where u.id = p.id and ";

    if ($id == "admin" || $id == "chaitalk") {

        if ($sortobj == "주문상태")
            $stmt = $tsql . "p.rdate between '{$start}' and '{$end}' order by confirm {$ss} , id asc, rdate desc";
        else if ($sortobj == "날짜")
            $stmt = $tsql . "p.rdate between '{$start}' and '{$end}' order by  rdate {$ds}, id {$bs} ";
        else
            $stmt = $tsql . "p.rdate between '{$start}' and '{$end}' order by id {$bs}, rdate {$ds} ";
    } else {
        if ($name == "전유치원") {
            if ($sortobj == "주문상태")
                $stmt = $tsql . "p.id = '{$id}'  and p.rdate between '{$start}' and '{$end}' order by confirm {$ss} , id asc, rdate desc";
            else if ($sortobj == "날짜")
                $stmt = $tsql . "p.id = '{$id}'  and p.rdate between '{$start}' and '{$end}' order by  rdate {$ds}, id {$bs} ";
            else
                $stmt = $tsql . "p.id = '{$id}'  and p.rdate between '{$start}' and '{$end}' order by id {$bs}, rdate {$ds} ";
        } else {
            if ($sortobj == "주문상태")
                $stmt = $tsql . "p.id = '{$id}' and p.order =  '{$name}' and p.rdate between '{$start}' and '{$end}' order by confirm {$ss} , id asc, rdate desc";
            else if ($sortobj == "날짜")
                $stmt = $tsql . "p.id = '{$id}' and p.order =  '{$name}' and p.rdate between '{$start}' and '{$end}' order by  rdate {$ds}, id {$bs} ";
            else
                $stmt = $tsql . "p.id = '{$id}' and p.order =  '{$name}' and p.rdate between '{$start}' and '{$end}' order by id {$bs}, rdate {$ds} ";
        }
    }
    try {

        $rs = mysqli_query($conn, $stmt);

        while ($row = mysqli_fetch_array($rs)) {
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
        $stmt = "select * from chaitalk_parcel where  DATE_FORMAT(`date`,'%Y-%m') = '{$nstart}' ";
        if ($id != "전지사")
            $stmt = "select * from chaitalk_parcel where id='{$id}' and DATE_FORMAT(`date`,'%Y-%m') = '{$nstart}' ";

        $rs1 = mysqli_query($conn, $stmt);
        $rows = [];
        while ($row = mysqli_fetch_array($rs1)) {
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
    session_start();

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
    $tsql = "select p.*, u.owner owner from chaitalk_porlist p ,  chaitalk_user u  where u.id = p.id and ";


    if ($name == "전지사")
        $stmt = $tsql . "p.rdate between '{$start}' and '{$end}' order by id , rdate desc";

    else if ($name == "전유치원")
        $stmt = $tsql . "u.id = '{$id}' and p.rdate between '{$start}' and '{$end}' order by p.id, rdate desc";

    else {
        if ($id != "admin")
            $stmt = $tsql . "p.id = '{$id}' and p.order =  '{$name}' and  p.rdate >= '{$start}' and  p.rdate <= '{$end}' order by p.id, rdate desc";
        // $stmt = $tsql . "p.id = '{$id}' and p.order =  '{$name}' and  p.rdate between '{$start}' and '{$end}' order by p.id, rdate desc";
        else
            $stmt = $tsql . "p.id = '{$name}' and p.rdate between '{$start}' and '{$end}' order by p.id, rdate desc";
    }

    try {

        $rs = mysqli_query($conn, $stmt);

        while ($row = mysqli_fetch_array($rs)) {
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
        $stmt = "select * from chaitalk_parcel where  DATE_FORMAT(`date`,'%Y-%m') = '{$nstart}' ";
        if ($id != "전지사")
            $stmt = "select * from chaitalk_parcel where id='{$id}' and DATE_FORMAT(`date`,'%Y-%m') = '{$nstart}' ";

        $rs1 = mysqli_query($conn, $stmt);
        $rows = [];
        while ($row = mysqli_fetch_array($rs1)) {
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

    try {
        global $conn;
        $res = "";

        // check id, date exist

        //$stmt = "select * from chaitalk_parcel where id='{$id}' and DATE_FORMAT(`date`,'%Y-%m') = '{$start}' ";
        $stmt = "select * from chaitalk_parcel where id='{$id}' and `date` = '{$start} 00:00:00' ";

        $rs1 = mysqli_query($conn, $stmt);

        if ($rs1->num_rows > 0) {
            $sql = "UPDATE chaitalk_parcel SET price =  {$price}  WHERE  id = '{$id}' and  `date` = '{$start} 00:00:00'";
            //$sql = "UPDATE chaitalk_parcel SET price =  {$price}  WHERE  id = '{$id}' and  DATE_FORMAT(`date`,'%Y-%m') = '{$start}'";
            $res = $conn->query($sql);
        } // update
        else {
            $sqlstring = "insert into chaitalk_parcel (id, name, price) 
            values ( '{$id}', '{$name}',{$price} )";
            if ($start != "")
                $sqlstring = "insert into chaitalk_parcel (id, name, price, `date`) 
            values ( '{$id}', '{$name}', {$price}, '{$start}' )";

            $res = mysqli_query($conn, $sqlstring);
        }

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

function SShowMgr($data)
{
    session_start();

    global $conn;

    $role = $data['role'];
    $id   = $data['id'];

    try {

        if ($role == "va") {
            if ($id == 'admin' || $id == "chaitalk")
                $sqlString = "SELECT * FROM chaitalk_user where role IN (1, 2, 3, 7) and status = 1 ";
            else
                $sqlString = "SELECT * FROM chaitalk_user where mid = '" . $id . "'  and status = 1";
        } else if ($role == "v4")
            if ($id == 'admin' || $id == "chaitalk")
                $sqlString = "SELECT * FROM chaitalk_user where role = 1  and status = 1";
            else
                $sqlString = "SELECT * FROM chaitalk_user where role = 1 and mid = '" . $id . "'  and status = 1";
        else  if ($role == "v5")
            if ($id == 'admin' || $id == "chaitalk")
                $sqlString = "SELECT * FROM chaitalk_user where role = 2  ";
            else
                $sqlString = "SELECT * FROM chaitalk_user where role = 2 and mid = '" . $id . "' and status = 1";
        else  if ($role == "v6")
            if ($id == 'admin' || $id == "chaitalk")
                $sqlString = "SELECT * FROM chaitalk_user where role = 3  ";
            else
                $sqlString = "SELECT * FROM chaitalk_user where role = 3 and mid = '" . $id . "'  and status = 1";
        else  if ($role == "v7")
            if ($id == 'admin' || $id == "chaitalk")
                $sqlString = "SELECT * FROM chaitalk_user where role = 7  ";
            else
                $sqlString = "SELECT * FROM chaitalk_user where role = 7 and mid = '" . $id . "'  and status = 1";

        $rs = mysqli_query($conn, $sqlString);
        $rows = array();

        $i = 0;

        while ($row = mysqli_fetch_array($rs)) {
            array_push(
                $rows,
                array(
                    'id'        => $row['id'],
                    'name'      => $row['name'],
                    'owner'     => $row['owner'],
                    'password'  => $row['password'],
                    'mobile'    => $row['mobile'],
                    'addr'      => $row['addr'],
                    'zipcode'   => $row['zipcode'],
                    'confirm'   => $row['confirm'],
                    'rdate'     => $row['rdate'],
                    'role'     => $row['role'],
                    'class'     => $row['etc'],
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

function SShowAddr($data)
{
    session_start();

    global $conn;

    $id   = $data['id'];

    try {


        $sqlString = "SELECT * FROM chaitalk_addrlist where mid = '" . $id . "'  order by rdate desc";


        $rs = mysqli_query($conn, $sqlString);
        $rows = array();

        $i = 0;

        while ($row = mysqli_fetch_array($rs)) {
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

        $sqlString2 = "SELECT * FROM chaitalk_user where mid = '" . $id . "'";
        $rs = mysqli_query($conn, $sqlString2);

        while ($row = mysqli_fetch_array($rs)) {
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
    session_start();

    $id  = $data["id"];

    global $conn;
    $res = "";

    try {

        $sql = "DELETE FROM chaitalk_user WHERE id = '" . $id . "'";

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
    session_start();

    $id  = $data["id"];

    global $conn;
    $res = "";

    try {

        $sql = "DELETE FROM chaitalk_addrlist WHERE id = '" . $id . "'";

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

function SRemovPorID($data)    // 프로그램 update 필요함
{
    //session_start();

    $id  = $data["id"];
    $pdf = $data["pdfname"];

    global $conn;
    $res = "";

    try {

        $sql = "DELETE FROM chaitalk_porlist WHERE por_id = '" . $id . "'";

        if ($conn->query($sql) === TRUE) {
            $res = true;
            if ($pdf != "" && file_exists('../assets/por/uploads/' . $pdf)) {
                if (unlink('../assets/por/uploads/' . $pdf)) {
                    $res = true;
                } else {
                    $res = false;
                }
            }
        } else {
            $res =  json_encode(array("Error deleting record: " . $conn->error));
        }

        $conn->close();
    } catch (Exception $e) {
        echo json_encode($e->getMessage());
    }

    echo json_encode($res);
}

// function SAddDelever($data)    // 프로그램 update 필요함
// {
//     //session_start();

//     $id  = $data["id"];
//     $status  = $data["status"];
//     $invoice  = $data["invoice"];

//     global $conn;
//     $res = "";

//     try {
//         $sql = "UPDATE chaitalk_porlist  confirm={$status}, invoice='{$invoice}' WHERE por_id='{$id}'";

//         //$sql = "UPDATE chaitalk_porlist SET confirm = 1  WHERE  por_id = '{$id}'";

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
    $id = $data["id"];
    $status = $data["status"];
    $invoice = $data["invoice"];
    $refund = $data["refund"];

    global $conn;
    $res = "";

    try {
        $sql = "UPDATE chaitalk_porlist SET confirm = ?, invoice = ?, refund = ? WHERE por_id = ?";
        $stmt = $conn->prepare($sql);

        // 빈 문자열이면 NULL 처리
        $invoiceValue = ($invoice === "") ? null : $invoice;
        $refundValue = ($refund === "") ? null : $refund;

        // 데이터 타입 변경: "issi" → "isii"
        $stmt->bind_param("isss", $status, $invoiceValue, $refundValue, $id);

        if ($stmt->execute()) {
            $res = true;
        } else {
            $res = "SQL 실행 오류: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $res = "예외 발생: " . $e->getMessage();
    }

    echo json_encode($res);
}


function SDeleteMgr($data)
{
    session_start();

    $id  = $data["id"];

    global $conn;
    $res = "";

    try {

        $sql = "DELETE FROM chaitalk_user WHERE id = '" . $id . "'";

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

function SRegistermgr($data)
{

    session_start();
    $result = "";

    //$json = file_get_contents('php://input');
    // $arr = json_decode($data, true); 
    // $row = $arr['item'];

    $id       = $data['items']['id'];
    $name     = $data['items']['name'];
    $owner     = $data['items']['owner'];
    $password = $data['items']['password'];
    $mobile   = $data['items']['mobile'];
    $addr     = $data['items']['addr'];
    $zipcode  = $data['items']['zipcode'];
    // $idrolebm = $data['items']['idrolebm'];
    $mid      = $data['items']['mid'];
    $role     = $data['items']['role'];
    $confirm = 1;
    if (isset($data['items']['confirm']))
        $confirm = (int) $data['items']['confirm'];
    $class = $data['items']['class'];
    $error = "";

    try {
        global $conn;

        $sqlstring = "insert into chaitalk_user (id, name,owner, password, mobile, addr, zipcode, role, mid, confirm,etc) 
                    values ( '{$id}', '{$name}','{$owner}', '{$password}', '{$mobile} ','{$addr}', '{$zipcode}', $role, '{$mid}', $confirm ,'{$class}')";

        $res = mysqli_query($conn, $sqlstring);

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

function SRegistermgr2($data)
{

    session_start();
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

    $error = "";

    try {
        global $conn;

        $sqlstring = "insert into chaitalk_addrlist ( name, owner,  mobile, addr, zipcode,  mid ) 
                    values (  '{$name}','{$owner}', '{$mobile} ','{$addr}', '{$zipcode}',  '{$mid}' )";

        $res = mysqli_query($conn, $sqlstring);

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
    session_start();

    $num = $data['num'];

    global $conn;
    
    $sqlString = "SELECT *  FROM chaitalk_user where role > 0 AND role < 9  order by rdate desc";

    if ($num == "2")
        $sqlString = "SELECT *  FROM chaitalk_user where (role > 0 AND role < 9 ) and confirm = 0  or confirm is null order by rdate desc";
    else if ($num == "1")
        $sqlString = "SELECT *  FROM chaitalk_user where (role > 0 AND role < 9 )  and confirm = 1 order by rdate desc";


    $rs = mysqli_query($conn, $sqlString);
    $rows = array();

    $i = 0;

    try {

        while ($row = mysqli_fetch_array($rs)) {
            array_push(
                $rows,
                array(
                    'id'        => $row['id'],
                    'name'      => $row['name'],
                    'password'  => $row['password'],
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
    session_start();

    $num = $data['num'];

    global $conn;
    
    if ( $num == "0" )
        $sqlString = "SELECT *  FROM chaitalk_user where role > 0 AND role < 9  order by rdate desc";
    else
        $sqlString = "SELECT *  FROM chaitalk_user where role = " .$num. " order by rdate desc";



    $rs = mysqli_query($conn, $sqlString);
    $rows = array();

    $i = 0;

    try {

        while ($row = mysqli_fetch_array($rs)) {
            array_push(
                $rows,
                array(
                    'id'        => $row['id'],
                    'name'      => $row['name'],
                    'password'  => $row['password'],
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

function SShowConfirmUpdate($data)
{
    session_start();

    // $json = file_get_contents('php://input');
    // $arr = json_decode($json, true);

    $arr = $data;
    $user = $data['items'][0]['user'] || "";
    //$user =  "";
    $id = "";
    global $conn;
    $result = "";

    // if (isset($data['owner']))   //
    //     $owner    = $data['owner'];
    // else
    //     $owner = '';

    // if (isset($data['addr']))   //
    //     $addr    = $data['addr'];
    // else
    //     $addr = '';   
    // if (isset($data['zipcode']))   //
    //     $zipcode    = $data['zipcode'];
    // else
    //     $zipcode = '';    
    // if (isset($data['mobile']))   //
    //     $mobile    = $data['mobile'];
    // else
    //     $mobile = '';    


    try {
        foreach ($arr['items'] as $row) {
            if ($user == "chaitalk" || $user == "admin") {
                $sql = "UPDATE chaitalk_user SET id = '" . $row['id'] . "'
                ,name = '" . $row['name'] . "' 
                ,owner = '" . $row['owner'] . "'
                ,password = '" . $row['password'] . "' 
                ,confirm = " . $row['confirm'] . " 
                ,mobile = '" . $row['mobile'] . "'
                ,addr = '" . $row['addr'] . "'
                ,etc = '" . $row['class'] . "'
                WHERE id = '" . $row['id'] . "'";
            } else {
                $sql = "UPDATE chaitalk_user SET id = '" . $row['id'] . "'
                ,name = '" . $row['name'] . "' 
                ,owner = '" . $row['owner'] . "'
                ,password = '" . $row['password'] . "' 
                ,confirm = " . $row['confirm'] . " 
                ,mobile = '" . $row['mobile'] . "'
                ,addr = '" . $row['addr'] . "'
                WHERE id = '" . $row['id'] . "'";
            }
            if ($conn->query($sql) === TRUE) {
                $result = true;
            }
        }

        $conn->close();
    } catch (Exception $e) {
        $result = $e->getMessage();
    }

    echo json_encode(array("result: " => $result));
}

function SShowConfirmUpdatePOR($data)
{
    session_start();

    // $json = file_get_contents('php://input');
    // $arr = json_decode($json, true);

    $arr = $data;
    global $conn;
    $result = "";

    try {

        $id = $arr['data']['id'];
        $por_id = $arr['data']['porid'];
        $sql = "UPDATE chaitalk_porlist SET confirm = 1  WHERE  por_id = '{$por_id}'";

        if ($conn->query($sql) === TRUE) {
            $result = true;
        }


        $conn->close();
    } catch (Exception $e) {
        $result = $e->getMessage();
    }

    header('Content-Type: application/json');

    echo json_encode(array("result: " => $result));
}

function SShowStudentList($data)
{
    session_start();

    global $conn;
    $tid = $data['id'];
    $step = $data['step'];
    $sel = $data['sel'];
    if (isset($data['kgarden']))   //
        $kgarden    = $data['kgarden'];


    if ($tid == "admin") {
        if ($sel == '1') {
            if ($step == '전체')
                $sqlString = "SELECT *  FROM chaitalk_user where  role = 0";
            else
                //$sqlString = "SELECT *  FROM chaitalk_user where  step = '{$step}' and role = 0 and tid='{$kgarden}'";
                $sqlString = "SELECT *  FROM chaitalk_user au, (select id from chaitalk_user ) u where u.id = au.tid and role = 0 and step='{$step}'";
        } else if ($sel == '2') {

            //$sqlString = "SELECT *  FROM chaitalk_user where classnm = '{$step}' and role = 0 and tid='{$kgarden}'";
            $sqlString = "SELECT *  FROM chaitalk_user  u where role = 0 and classnm='{$step}'";
        } else if ($sel == '3')
            //$sqlString = "SELECT *  FROM chaitalk_user where tid = '{$step}' and role = 0";
            if ($step == '전체')
                //$sqlString = "SELECT *  FROM chaitalk_user where  role = 0";
                $sqlString = "SELECT * , u.owner FROM chaitalk_user au, (select id uid, owner from chaitalk_user  ) u where u.uid = au.tid and role = 0";
            else
                $sqlString =  "SELECT au.*  FROM chaitalk_user au, (select id from chaitalk_user ) u where u.id = au.tid and role = 0";
    } else {
        if ($sel == '1') {
            if ($step == '전체')
                $sqlString = "SELECT *  FROM chaitalk_user where tid = '{$tid}' ";
            else
                $sqlString = "SELECT *  FROM chaitalk_user where tid = '{$tid}' and step = '{$step}'";
        } else if ($sel == '2')
            $sqlString = "SELECT *  FROM chaitalk_user where tid = '{$tid}' and classnm = '{$step}'";
        else if ($sel == '3')
            if ($step == '전체')
                $sqlString = "SELECT *  FROM chaitalk_user where tid = '{$tid}' ";
            else
                $sqlString = "SELECT *  FROM chaitalk_user where tid = '{$tid}' and owner = '{$step}'";
        else if ($sel == '4')
            if ($step == '전체')
                $sqlString = "SELECT *  FROM chaitalk_user where tid = '{$tid}' ";
            else
                $sqlString = "SELECT *  FROM chaitalk_user where tid = '{$tid}' and status = 1";
    }
    $rows = array();

    $i = 0;

    try {

        $rs = mysqli_query($conn, $sqlString);

        while ($row = mysqli_fetch_array($rs)) {
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
    session_start();

    global $conn;
    $tid = $data['id'];
    $step = $data['step'];
    $start = $data['start'];
    $end = $data['end'];

    $tsql = "select u.id id, u.name name , s.volume v, s.step s, s.uid uid, count(u.id) cnt from study_record s, chaitalk_user u where ";
    if ($step == '전체')
        $sqlString = $tsql . "u.id = s.id and u.tid = '{$tid}'  and  s.rdate >= '{$start}' and s.rdate <= '{$end}'  group by id";
    else
        $sqlString = $tsql . " s.step = '{$step}' and u.id = s.id and u.tid = '{$tid}'  and  s.rdate >= '{$start}' and s.rdate <= '{$end}' group by id, s.step";

    $rows = array();

    $i = 0;

    try {

        $rs = mysqli_query($conn, $sqlString);

        while ($row = mysqli_fetch_array($rs)) {
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
    session_start();

    global $conn;
    $result = 0;
    $id = $data['id'];

    $sql = "select id from  chaitalk_user where id like '{$id}%' and role = 0";

    $rows = array();

    $i = 0;
    $max = -1;
    try {

        $rs = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_array($rs)) {
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

        if (mysqli_num_rows($rs) > 0) {
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
    session_start();

    global $conn;
    $tid = $data['id'];
    $classnm = $data['classnm'];
    $start = $data['start'];
    $end = $data['end'];

    $tsql = "select u.id id, u.name name ,u.classnm classnm, s.volume v, s.step s, s.uid uid, count(u.id) cnt from study_record s, chaitalk_user u where ";
    if ($classnm == '전체' || $classnm == "")
        $sqlString = $tsql . "u.id = s.id and u.tid = '{$tid}' and  s.rdate >= '{$start}' and s.rdate <= '{$end}' group by id";
    else
        $sqlString = $tsql . " u.classnm = '{$classnm}' and u.id = s.id and u.tid = '{$tid}' and s.rdate >= '{$start}' and s.rdate <= '{$end}' group by id";

    $rows = array();

    $i = 0;

    try {

        $rs = mysqli_query($conn, $sqlString);

        while ($row = mysqli_fetch_array($rs)) {
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
    session_start();

    $tid = $data['id'];
    //$kgarden = $data['kgarden'];

    global $conn;

    $sqlString = "select DISTINCT classnm from chaitalk_user where tid = '{$tid}'";

    if ($tid == "admin")
        //$sqlString = "select DISTINCT classnm from chaitalk_user where tid = '{$kgarden}' and classnm is not null";
        $sqlString = "SELECT DISTINCT classnm FROM chaitalk_user au, (select id from chaitalk_user ) u where u.id = au.tid and role = 0";
    $rows = array();

    $i = 0;

    try {

        $rs = mysqli_query($conn, $sqlString);


        while ($row = mysqli_fetch_array($rs)) {
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
    session_start();

    $tid = $data['id'];

    global $conn;

    $sqlString = "select DISTINCT owner from chaitalk_user where mid = '{$tid}' and owner is not null and role=2";

    if ($tid == "admin")
        $sqlString = "select DISTINCT owner from chaitalk_user where owner is not null and role=2";
    $rows = array();

    $i = 0;

    try {

        $rs = mysqli_query($conn, $sqlString);

        while ($row = mysqli_fetch_array($rs)) {
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

    session_start();

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
            $role    = 0;
            $confirm = 1;
            $sql = "INSERT INTO chaitalk_user (id, name, password, role, tid, classnm, rdate, step, confirm)
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

    session_start();

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
            $sql = "update chaitalk_user set  
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
    session_start();

    $result = "";

    try {
        foreach ($data['items'] as $row) {
            $id = $row['id'];
            $name = $row['name'];
            $password = $row['passwd'];
            $tid = $row['tid'];
            $classnm = $row['classnm'];
            $step = $row['step'];
            $role = 30;
            $p_mobile = $row['p_mobile'];
            $mobile  = $row['mobile'];
            $addr  = $row['addr'];
            $status = (int)($row['status']);
            $etc = $row['etc'];
            $confirm = 1;

            // ID 존재 여부 확인
            $checkSql = "SELECT COUNT(*) as count FROM chaitalk_user WHERE id = '{$id}' AND tid = '{$tid}'";
            $checkResult = $conn->query($checkSql);
            $exists = $checkResult->fetch_assoc()['count'] > 0;

            // 존재 여부에 따라 쿼리 생성
            if (!$exists) {
                // INSERT
                $sql = "INSERT INTO chaitalk_user (id, name, password, role, tid, classnm, rdate, step, confirm, p_mobile, mobile, addr, etc) 
                        VALUES ('{$id}', '{$name}', '{$password}', {$role}, '{$tid}', '{$classnm}', NOW(), '{$step}', {$confirm}, '{$p_mobile}', '{$mobile}', '{$addr}', '{$etc}')";
                $action = "created";
            } else {
                // UPDATE
                $sql = "UPDATE chaitalk_user 
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
    $porid   = $data['porid'];
    $addr    = $data['addr'];
    $mobile  = $data['mobile'];
    $order   = $data['order'];
    $zip     = isset($data['zip']) ? $data['zip'] : '';
    $pdfname = '';
    $user    = 'admin';
    
    global $conn;
    global $location;

    if ( $location == "localhost" )
         $url = "http://localhost:3000/assets/por/uploads/";
    else
        $url = "https://www.chaitalkkid.co.kr/assets/por/uploads/";

    $fileUploadError = '';
    
    try {
        // DB INSERT 먼저 수행 (파일 업로드 실패와 무관하게 처리)
        $sqlstring = "
            INSERT INTO chaitalk_porlist (id, por_id, por_list, rdate, `order`, addr, mobile, pdfname, zip)
            VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($sqlstring);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssssssss", $id, $porid, $porlist, $order, $addr, $mobile, $pdfname, $zip);
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

                $RandomName = generateRandomString(15);
                $pdfname    = $RandomName . basename($fileName);

                if (!move_uploaded_file($fileTempName, __DIR__ . "/../assets/por/uploads/" . $pdfname)) {
                    throw new Exception("Failed to move uploaded file.");
                }

                // 파일 업로드 성공 후 DB 업데이트 (pdfname 업데이트)
                $updateSql = "UPDATE chaitalk_porlist SET pdfname = ? WHERE id = ? AND por_id = ?";
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
            'url' => !empty($pdfname) ? $url . $pdfname : '',
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
    session_start();
    $content   = $data['idContent'];
    $desc   = $data['idDesc'];
    $cate   = $data['idSelect2'];
    $user =  'admin';
    $user =   $data['user'];
    set_time_limit(300);


    global $conn;
    $rows = array();

    if (!empty($user)) {
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

            $sqlstring = "insert into chaitalk_repository ( title, id, contents, `desc`, rdate, cate ) values ( '$content', '$user',  '$jsarr', '$desc',  NOW(), '$cate')";
            $res = mysqli_query($conn,  $sqlstring);

            if ($res === TRUE) {
                $result = true;
            } else {
                $result =  "Error: " . $sqlstring . "<br>" . $conn->error;
            }

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

    global $conn;

    if (!empty($user)) {
        try {
            // Update query
            $sqlstring = "UPDATE chaitalk_repository 
                          SET title = '$content', 
                              `desc` = '$desc', 
                              cate = '$cate', 
                              rdate = NOW() 
                          WHERE num = '$id'";

            $res = mysqli_query($conn, $sqlstring);

            if ($res === TRUE) {
                $result = true;
            } else {
                $result = "Error: " . $sqlstring . "<br>" . $conn->error;
            }

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
    session_start();

    $num = $data['num'];
    $cate = $data['cate'];

    global $conn;

    try {
        if ($cate == "1") {
            if ($num == "전체")
                $sqlString = "SELECT num, title, id, contents, `desc`, rdate, cate  FROM chaitalk_repository order by num desc";
            else
                $sqlString = "SELECT num, title, id, contents, `desc`, rdate, cate  FROM chaitalk_repository where cate = '$num' order by num desc";
        } else
            $sqlString = "SELECT num, title, id, contents, `desc`, rdate, cate  FROM chaitalk_repository where num = $num order by num desc";

        $rs = mysqli_query($conn, $sqlString);
        $rows = array();

        $i = 0;

        while ($row = mysqli_fetch_array($rs)) {
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
    session_start();

    $num  = $data["num"][0];

    global $conn;
    $res = "";
    $rows = array();
    $cont = "";

    try {
        $sql = "SELECT contents from chaitalk_repository WHERE num = " . $num . "";

        $rs = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_array($rs)) {
            $cont =  $row['contents'];
        }
        $jsobj = json_decode($cont, true);

        if ($jsobj !== null) {
            $arrlen = count($jsobj);
            for ($i = 0; $i < $arrlen; $i++) {
                if (unlink('../assets/board/uploads/' . $jsobj[$i]['fakename'])) {
                    $res = true;
                } else {
                    $res = false;
                }
            }
        }

        $sql = "DELETE FROM chaitalk_repository WHERE num = '" . $num . "'";

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
    session_start();

    global $conn;

    $sqlString = "select MAX(price) price from chaitalk_price";

    $rows = array();

    $i = 0;

    try {

        $rs = mysqli_query($conn, $sqlString);

        while ($row = mysqli_fetch_array($rs)) {
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
    // require_once 'dbinit.php'; // 필요 시 주석 해제
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

function SShoppingListProducts($data)
{
    global $conn;
    $rows = [];

    try {
        $rs = mysqli_query($conn, "SELECT id, name, description, price, image_url, created_at FROM shopping_products ORDER BY id DESC");
        while ($row = mysqli_fetch_assoc($rs)) {
            $row['id'] = (int) $row['id'];
            $row['price'] = (float) $row['price'];
            $rows[] = $row;
        }
        shoppingJson(['products' => $rows]);
    } catch (Exception $e) {
        shoppingJson(['error' => $e->getMessage()], 500);
    }
}

function SShoppingGetProduct($data)
{
    global $conn;
    $id = (int) ($data['id'] ?? 0);
    $product = shoppingFindProduct($conn, $id);

    if (!$product) {
        shoppingJson(['error' => 'Product not found'], 404);
    }

    shoppingJson(['product' => $product]);
}

function SShoppingSaveProduct($data)
{
    global $conn;
    shoppingRequireAdmin();

    $id = (int) ($data['id'] ?? 0);
    $name = trim((string) ($data['name'] ?? ''));
    $description = trim((string) ($data['description'] ?? ''));
    $price = (float) ($data['price'] ?? 0);
    $imageUrl = trim((string) ($data['image_url'] ?? ''));
    $uploadedUrl = shoppingUploadImage();

    if ($uploadedUrl !== '') {
        $imageUrl = $uploadedUrl;
    }

    if ($name === '' || $description === '' || $price <= 0) {
        shoppingJson(['error' => 'name, description, and positive price are required'], 422);
    }

    if ($id > 0) {
        if (!shoppingFindProduct($conn, $id)) {
            shoppingJson(['error' => 'Product not found'], 404);
        }

        $stmt = $conn->prepare("UPDATE shopping_products SET name = ?, description = ?, price = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("ssdsi", $name, $description, $price, $imageUrl, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO shopping_products (name, description, price, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $description, $price, $imageUrl);
        $stmt->execute();
        $id = (int) $conn->insert_id;
        $stmt->close();
    }

    shoppingJson(['product' => shoppingFindProduct($conn, $id)]);
}

function SShoppingDeleteProduct($data)
{
    global $conn;
    shoppingRequireAdmin();

    $id = (int) ($data['id'] ?? 0);
    if (!shoppingFindProduct($conn, $id)) {
        shoppingJson(['error' => 'Product not found'], 404);
    }

    $stmt = $conn->prepare("DELETE FROM shopping_products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    shoppingJson(['ok' => true]);
}

function SShoppingCreateOrder($data)
{
    global $conn;
    shoppingStartSession();

    $productId = (int) ($data['product_id'] ?? 0);
    $quantity = max(1, (int) ($data['quantity'] ?? 1));
    $userId = $_SESSION['user'] ?? null;

    if (!shoppingFindProduct($conn, $productId)) {
        shoppingJson(['error' => 'Product not found'], 404);
    }

    $stmt = $conn->prepare("INSERT INTO shopping_orders (user_id, product_id, quantity, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("sii", $userId, $productId, $quantity);
    $stmt->execute();
    $orderId = (int) $conn->insert_id;
    $stmt->close();

    shoppingJson(['order' => [
        'id' => $orderId,
        'user_id' => $userId,
        'product_id' => $productId,
        'quantity' => $quantity,
        'status' => 'pending'
    ]]);
}

function SShoppingMe($data)
{
    shoppingStartSession();
    $user = null;

    if (!empty($_SESSION['user'])) {
        $user = [
            'id' => $_SESSION['user'],
            'name' => $_SESSION['name'] ?? '',
            'role' => $_SESSION['role'] ?? ''
        ];
    }

    shoppingJson(['user' => $user]);
}

function SShoppingLogout($data)
{
    shoppingStartSession();
    session_destroy();
    shoppingJson(['ok' => true]);
}

function shoppingFindProduct(mysqli $conn, int $id): ?array
{
    if ($id <= 0) {
        return null;
    }

    $stmt = $conn->prepare("SELECT id, name, description, price, image_url, created_at FROM shopping_products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        return null;
    }

    $product['id'] = (int) $product['id'];
    $product['price'] = (float) $product['price'];
    return $product;
}

function shoppingRequireAdmin(): void
{
    shoppingStartSession();
    $user = $_SESSION['user'] ?? '';
    $role = (string) ($_SESSION['role'] ?? '');

    if ($user !== 'admin' && $user !== 'chaitalk' && $role !== '9') {
        shoppingJson(['error' => 'Admin login required'], 401);
    }
}

function shoppingUploadImage(): string
{
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        shoppingJson(['error' => 'Image upload failed'], 422);
    }

    $tmp = $_FILES['image']['tmp_name'];
    $mime = mime_content_type($tmp);
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    if (!isset($extensions[$mime])) {
        shoppingJson(['error' => 'Only jpg, png, webp, and gif images are allowed'], 422);
    }

    $dir = __DIR__ . '/../assets/shopping/uploads';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $filename = generateRandomString(15) . '.' . $extensions[$mime];
    if (!move_uploaded_file($tmp, $dir . '/' . $filename)) {
        shoppingJson(['error' => 'Could not save uploaded image'], 500);
    }

    return '../assets/shopping/uploads/' . $filename;
}

function shoppingStartSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function shoppingJson(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

?>
