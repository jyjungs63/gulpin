<?php
include "../libs/header.php";
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
?>
<!DOCTYPE html>
<html lang="utf-8">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    include '../libs/include.php';
    ?>

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/alasql/4.5.0/alasql.min.js"></script> -->
    <script src="../js/common.js"></script>
    <!-- <link href="../common.css" rel="stylesheet"> -->
    <?php
        include '../libs/includescr.php';
    ?>
    <script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
    <!-- <script src="branchmgr.js"></script> -->
    <script src="../js/header.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Jua&family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
    <title>지사 및 원관리</title>
    <link rel="stylesheet" href="../role-nav.css">
    <script src="../js/role-nav.js" defer></script>
    <style>
        .gp-visitor-strip{background:#1f5233; color:#eafff0; font-size:.78rem; padding:6px 20px; display:flex; justify-content:flex-end; align-items:center; gap:10px;}
        .gp-visitor-strip .pill{background:rgba(255,255,255,.15); border-radius:999px; padding:3px 12px; font-weight:700;}
        .gp-site-header{background:linear-gradient(180deg,#3da35d,#2e7d46); padding:14px 28px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:1050; box-shadow:0 4px 18px rgba(0,0,0,.08);}
        .gp-strip-logo{height:24px; display:block; margin-right:auto;}
        .gp-home-link{display:flex; align-items:center; gap:6px; color:#fff; font-weight:700; font-size:.95rem; text-decoration:none;}
        .gp-home-link:hover{opacity:.85; color:#fff;}
        .gp-main-nav{display:flex; gap:28px; align-items:center;}
        .gp-main-nav a{color:rgba(255,255,255,.75); font-weight:700; font-size:.95rem; transition:color .2s ease; text-decoration:none; cursor:pointer;}
        .gp-main-nav a:hover,.gp-main-nav a.on{color:#fff;}
        .gp-burger{display:none; color:#fff; font-size:1.5rem; background:none; border:none; cursor:pointer;}
        @media(max-width:840px){
            .gp-main-nav{position:fixed; top:0; right:-100%; height:100%; width:70%; max-width:300px; background:#2e7d46; flex-direction:column; padding:90px 30px; gap:26px; transition:right .3s ease; z-index:1060;}
            .gp-main-nav.open{right:0;}
            .gp-burger{display:block;}
            .gp-site-header{padding:12px 16px;}
        }
        :root {
            --kg-bg: #f3f6fb;
            --kg-panel: #ffffff;
            --kg-line: #d9e2ef;
            --kg-text: #1f2937;
            --kg-muted: #6b7280;
            --kg-primary: #2563eb;
        }

        body.kgarden-page {
            background: var(--kg-bg) !important;
            color: var(--kg-text);
        }

        .kgarden-shell {
            max-width: 1560px;
            margin: 0 auto;
            padding: 18px;
        }

        .kgarden-heading {
            margin-bottom: 14px;
        }

        .kgarden-heading h5 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .kgarden-heading .breadcrumb {
            margin-bottom: 0;
            background: transparent;
            font-size: 14px;
            justify-content: flex-end;
        }

        .kgarden-card {
            border: 1px solid var(--kg-line);
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            background: var(--kg-panel);
        }

        .kgarden-toolbar {
            background: linear-gradient(135deg, #ffffff 0%, #eef6ff 100%) !important;
            border-bottom: 1px solid var(--kg-line);
            padding: 18px;
        }

        .kgarden-form {
            display: grid;
            grid-template-columns: 88px 82px 130px 130px 115px 100px 100px 80px minmax(180px, 1.5fr) 92px 72px;
            gap: 8px;
            align-items: end;
            width: 100%;
        }

        .kgarden-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .kgarden-field-wide {
            grid-column: auto;
        }

        .kgarden-field label {
            margin: 0;
            color: var(--kg-muted);
            font-size: 12px;
            font-weight: 700;
        }

        .kgarden-field input,
        .kgarden-field select {
            width: 100%;
            height: 38px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 7px 8px;
            font-size: 13px;
            background-color: #fff;
        }

        .kgarden-field input:focus,
        .kgarden-field select:focus {
            border-color: var(--kg-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.16);
        }

        .kgarden-btn {
            height: 38px;
            border-radius: 10px;
            font-weight: 700;
            white-space: nowrap;
            padding-left: 10px;
            padding-right: 10px;
        }

        .kgarden-body {
            background: var(--kg-panel) !important;
            padding: 18px;
        }

        .kgarden-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .kgarden-actions-right {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        #table-header {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: 8px 12px;
            border: 1px solid var(--kg-line);
            border-radius: 10px;
            background: #f8fafc;
            color: #111827;
            font-weight: 800;
        }

        .kgarden-table-wrap {
            overflow-x: auto;
            border: 1px solid var(--kg-line);
            border-radius: 12px;
            background: #fff;
        }

        #idTable {
            min-width: 1100px;
        }

        .kgarden-table-wrap .tabulator {
            border: 0;
            font-size: 14px;
        }

        .kgarden-table-wrap .tabulator-header,
        .kgarden-table-wrap .tabulator-col {
            background: #f8fafc;
        }

        .kgarden-table-wrap .tabulator-header {
            border-bottom: 1px solid var(--kg-line);
        }

        .kgarden-table-wrap .tabulator-row.tabulator-row-even {
            background: #f8fafc;
        }

        .kgarden-table-wrap .tabulator-row:hover {
            background: #eff6ff;
        }

        @media (max-width: 1199px) {
            .kgarden-form {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .kgarden-shell {
                padding: 12px;
            }

            .kgarden-heading .row {
                gap: 8px;
            }

            .kgarden-heading h5 {
                font-size: 17px;
            }

            .kgarden-heading .breadcrumb {
                float: none !important;
                justify-content: flex-end;
            }

            .kgarden-toolbar,
            .kgarden-body {
                padding: 14px;
            }

            .kgarden-form {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .kgarden-field-wide {
                grid-column: auto;
            }

            .kgarden-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .kgarden-actions-right {
                display: grid;
                grid-template-columns: 1fr 1fr;
                width: 100%;
            }

            .kgarden-actions-right .btn,
            #table-header {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body class="kgarden-page">
    <header id="roleNav"></header>

    <div class="kgarden-shell">
        <section class="kgarden-heading">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 id="brachname">지사/원관리 리스트</h5>
                    </div>
                    <div class="col-sm-6 text-right">
                        <ol class="breadcrumb justify-content-end">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:window.history.back();">이전</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="kgarden-card">
                <div class="kgarden-toolbar">
                    <div class="kgarden-form">
                        <div class="kgarden-field">
                            <label for="idGrade">구분</label>
                            <select id="idGrade" data-placeholder="Choose Items">
                                <option value="va">전체</option>
                                <option value="v4">지사관리</option>
                                <option value="v5" selected>원관리</option>
                                <option value="v6">가맹관리</option>
                                <option value="v7">방과후</option>
                            </select>
                        </div>
                        <div class="kgarden-field">
                            <label for="idClass">과정</label>
                            <select id="idClass">
                                <option value="0AB" selected>0AB</option>
                                <option value="0">0</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="T">T</option>
                                <option value="0A">0A</option>
                                <option value="0B">0B</option>
                                <option value="AB">AB</option>
                                <option value="0T">0T</option>
                                <option value="AT">AT</option>
                                <option value="BT">BT</option>
                                <option value="0AT">0AT</option>
                                <option value="0BT">0BT</option>
                                <option value="ABT">ABT</option>
                                <option value="0ABT">0ABT</option>
                            </select>
                        </div>
                        <div class="kgarden-field">
                            <label for="idOwner">지사/가맹점</label>
                            <input id="idOwner" type="text" placeholder="지사회사/가맹지점명">
                        </div>
                        <div class="kgarden-field">
                            <label for="idName">원/학원명</label>
                            <input id="idName" type="text" placeholder="유치원명/학원명">
                        </div>
                        <div class="kgarden-field">
                            <label for="idMobile">전화번호</label>
                            <input id="idMobile" type="text" placeholder="전화번호">
                        </div>
                        <div class="kgarden-field">
                            <label for="idID">아이디</label>
                            <input id="idID" type="text" placeholder="아이디">
                        </div>
                        <div class="kgarden-field">
                            <label for="idPasswd">암호</label>
                            <input id="idPasswd" type="text" placeholder="암호(영어)" pattern="^[a-zA-Z0-9\s]*$" required>
                        </div>
                        <div class="kgarden-field">
                            <label for="idZip">우편번호</label>
                            <input id="idZip" type="text" placeholder="우편번호">
                        </div>
                        <div class="kgarden-field kgarden-field-wide">
                            <label for="idAddr">주소</label>
                            <input id="idAddr" type="text" placeholder="주소">
                        </div>
                        <button class="btn btn-outline-primary kgarden-btn" type="button" onclick="execDaumPostcode('idZip','idAddr')">주소검색</button>
                        <button class="btn btn-success kgarden-btn" type="button" onclick="AddBranch()">등록</button>
                    </div>
                </div>
                <div class="kgarden-body">
                    <div class="kgarden-actions">
                        <div id="table-header">총 지사 수: 0 지사</div>
                        <div class="kgarden-actions-right">
                            <button class="btn btn-outline-secondary kgarden-btn" type="button" data-toggle="tooltip" title="지사 List Excel 저장" onclick="DownloadExcel()">
                                <i class="fa-solid fa-print"></i> Excel
                            </button>
                            <button id="idUpdateBranchBtn" class="btn btn-primary kgarden-btn" type="button" data-toggle="tooltip" title="지사 추가 하기" onclick="UpdateBranch()" disabled>
                                <i class="fa-solid fa-user"></i> 수정
                            </button>
                        </div>
                    </div>
                    <div class="kgarden-table-wrap">
                        <div id="idTable"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>



    <script>
        var table;


        if (role != '1' && role != '9') {
            CallToast("지사 관리 권한으로 로긴 하세요", "error");
            window.location.href = "../index.php";
            const selectElement = document.getElementById('idGrade');

            // v4와 v6 옵션을 찾아 disabled로 설정
            selectElement.querySelector('option[value="v4"]').disabled = false;
            selectElement.querySelector('option[value="v6"]').disabled = false;
        }
        if (role == 1) {
            // id가 'idGrade'인 select 요소를 선택
            const selectElement = document.getElementById('idGrade');

            // v4, v6, v7 옵션을 찾아 disabled로 설정
            selectElement.querySelector('option[value="v4"]').disabled = true;
            selectElement.querySelector('option[value="v6"]').disabled = true;
            selectElement.querySelector('option[value="v7"]').disabled = true;
        }

        function setAllOptions(isEnabled) {
            const select = document.getElementById('idClass');
            Array.from(select.options).forEach(option => {
                option.disabled = !isEnabled;
            });
        }

        function setOptionState(value, isEnabled) {
            const select = document.getElementById('idClass');
            const option = Array.from(select.options).find(opt => opt.value === value);
            if (option) {
                option.disabled = !isEnabled;
            }
        }

        function setSelectedValue(value) {
            const select = document.getElementById('idClass');
            select.value = value;
        }

        const originalClassOptions = [
            { value: '0AB',  text: '0AB',  selected: true },
            { value: '0',    text: '0' },
            { value: 'A',    text: 'A' },
            { value: 'B',    text: 'B' },
            { value: 'T',    text: 'T' },
            { value: '0A',   text: '0A' },
            { value: '0B',   text: '0B' },
            { value: 'AB',   text: 'AB' },
            { value: '0T',   text: '0T' },
            { value: 'AT',   text: 'AT' },
            { value: 'BT',   text: 'BT' },
            { value: '0AT',  text: '0AT' },
            { value: '0BT',  text: '0BT' },
            { value: 'ABT',  text: 'ABT' },
            { value: '0ABT', text: '0ABT' },
        ];

        const bangwahooClassOptions = [
            { value: '전체',  text: '전체',  selected: true },
            { value: '1단계', text: '1단계' },
            { value: '2단계', text: '2단계' },
        ];

        function setClassOptions(optionsArr) {
            const select = document.getElementById('idClass');
            select.innerHTML = '';
            optionsArr.forEach(opt => {
                const o = document.createElement('option');
                o.value = opt.value;
                o.textContent = opt.text;
                if (opt.selected) o.selected = true;
                select.appendChild(o);
            });
        }

        setAllOptions(true); // 모든 옵션 활성화
        //setOptionState('ca', true); // 'A' 옵션 비활성화
        //updateOptions(['all'])


        document.getElementById("brachname").innerHTML = "[" + user + "]" + "지사/원관리 리스트";

        document.getElementById('idPasswd').addEventListener('input', function(e) {
            const englishOnly = /^[a-zA-Z0-9\s]*$/;
            if (!englishOnly.test(e.target.value)) {
                e.target.value = e.target.value.replace(/[^a-zA-Z0-9\s]/g, '');
            }
        });

        AddBranch = () => {

            var selectElement = document.getElementById("idGrade"); // 지사 또는 원관리
            var selectedValue = selectElement.value;

            selectElement = document.getElementById("idClass"); // 지사 또는 원관리
            var selectedClass = selectElement.value;

            var id = $("#idID").val().trim(); // 아이디
            var name = $("#idName").val().trim(); // 이름
            var owner = $("#idOwner").val().trim(); // 지사명
            var password = $("#idPasswd").val().trim();
            var mobile = $("#idMobile").val().trim();
            var addr = $("#idAddr").val().trim();
            var zipcode = $("#idZip").val().trim();
            var role = selectedValue == "v4" ? 1 :
                selectedValue == "v5" ? 2 :
                selectedValue == "v6" ? 3 :
                selectedValue == "v7" ? 7 : 3 // 1 지사관리, 2 원관리, 3 가맹관리, 7 방과후
            var rdate = "";

            const formattedDate = formatDate();
            if (rdate == undefined || rdate == "") rdate = formattedDate;

            const requiredFields = [
                { label: "아이디", value: id, elementId: "idID" },
                { label: "유치원명/학원명", value: name, elementId: "idName" },
                { label: "지사회사/가맹지점명", value: owner, elementId: "idOwner" },
                { label: "암호", value: password, elementId: "idPasswd" },
                { label: "전화번호", value: mobile, elementId: "idMobile" },
                { label: "주소", value: addr, elementId: "idAddr" },
                { label: "우편번호", value: zipcode, elementId: "idZip" },
                { label: "사용자", value: user },
                { label: "권한", value: role },
                { label: "등록일", value: rdate },
                { label: "과정", value: selectedClass, elementId: "idClass" },
            ];
            const missingFields = requiredFields.filter(field => field.value === undefined || field.value === null || String(field.value).trim() === "");

            if (missingFields.length > 0) {
                alert(`${missingFields.map(field => field.label).join(", ")}을(를) 다시 입력해 주세요.`);
                const firstInput = missingFields.find(field => field.elementId);
                if (firstInput) {
                    document.getElementById(firstInput.elementId).focus();
                }
                return;
            }

            var items = {
                id: id,
                name: name,
                owner: owner,
                password: password,
                mobile: mobile,
                addr: addr,
                zipcode: zipcode,
                mid: user,
                role: role,
                rdate: rdate,
                class: selectedClass,
            }

            var data = {
                "item": items
            }

            function getRoleName(role) {
                switch (role) {
                    case 1:
                        return "지사관리";
                    case 2:
                        return "원관리";
                    case 3:
                        return "가맹관리";
                    case 7:
                        return "방과후";
                    case 9:
                        return "관리자";
                    default:
                        return "원생";
                }
            }

            dispList = (resp) => {
                if ('success' in resp) {
                    CallToast('New Branch Manager added successfully!!', "success")
                    //items['role'] = items['role'] == "1" ? "지사관리" : ( items['role'] == "2" ? "원관리" : (items['role'] == "9" ? "관리자" : "원생") );
                    items['role'] = getRoleName(items['role'])
                    table.addRow(items);
                } else
                    CallToast('New Branch Manager added falure!', "error")

            }
            dispErr = (xhr) => {
                CallToast(`${id}은 이미등록 되있습니다. 다른 ID를 사용하세요`, "error")
            }
            jsdata = JSON.stringify(items);
            var options = {
                functionName: 'SRegistermgr',
                otherData: {
                    items
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);
        };

        UpdateBranch = () => {
            var item = table.getSelectedData();
            var items = [];
            let confm = 0;
                // 1. 선택된 항목이 있는지 확인
            // if (role != '9') {
            //     alert("승인 상태를 수정할 권한이 없습니다.");
            //     return;
            // }
            if (!item || item.length === 0) {
                // 선택된 항목이 없는 경우 메시지 표시
                alert('선택된 지사나 원이 없습니다. 체크 박스를 선택해주세요.', "warning");
                return; // 함수 실행 중단
            }

            item.forEach(el => {
                let confm = el['confirm'] === "승인" ? 1 : 0;
                var jarr = {
                    "id": el['id'],
                    "owner": el['owner'],
                    "name": el['name'],
                    "mobile": el['mobile'],
                    "addr": el['addr'],
                    "zipcode": el['zipcode'],
                    "password": el['password'],
                    "confirm": confm,
                    "class": el['class'],
                    "user": user,
                }
                items.push(jarr);
            })
            var data = {
                "item": items
            }

            dispList = (resp) => {
                CallToast('New Branch Manager Updated successfully!!', "success");
                BranchList();
            }
            dispErr = (xhr) => {
                CallToast('New Branch Manager Update falure!', "error")
            }

            var options = {
                functionName: 'SShowConfirmUpdate',
                otherData: {
                    items
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);

        }

        function updateRowCount(cnt) {
            // var count = tab.getDataCount();
            //document.getElementById("row-count").innerText = `총 지사 수: ${cnt} 명`;
            document.getElementById("table-header").innerText = `총 지사 수: ${cnt} 지사`;
        }

        function setUpdateButtonState() {
            const updateButton = document.getElementById("idUpdateBranchBtn");
            if (updateButton && table) {
                updateButton.disabled = table.getSelectedData().length === 0;
            }
        }

        function refreshUpdateButtonAfterSelection() {
            setTimeout(setUpdateButtonState, 0);
        }

        BranchList = () => {
            var items = [];

            var role = "";
            dispList = (resp) => {
                resp['success'].forEach(el => {

                    switch (el['role']) {
                        case "1":
                            role = "지사관리"
                            break;
                        case "2":
                            role = "원관리"
                            break;
                        case "3":
                            role = "가맹관리"
                            break;
                        case "7":
                            role = "방과후"
                            break;
                        case "9":
                            role = "Admin"
                            break
                        default:
                            role = "원생"
                    }
                    if (el['role'] == "1" || el['role'] == "2" || el['role'] == "3" || el['role'] == "7")
                        var jarr = {
                            "role": role,
                            "id": el['id'],
                            "name": el['name'],
                            "owner": el['owner'],
                            "mobile": el['mobile'],
                            "addr": el['addr'],
                            "zipcode": el['zipcode'],
                            "password": el['password'],
                            "rdate": el['rdate'],
                            "class": el['class'],
                            "confirm": el['confirm'] == 1 ? "승인" : "미승인",
                        }
                    items.push(jarr);
                });
                table.clearData();
                table.setData(items);
                var count = table.getDataCount();
                updateRowCount(count);
                setUpdateButtonState();
            }
            dispErr = (e) => {
                alert("SShowMgr Error!" + e);
            }

            var options = {
                functionName: 'SShowMgr',
                otherData: {
                    role: "va", //전체
                    id: user
                }
            };

            CallAjax("SMethods.php", "POST", options, dispList, dispErr);
        }

        BranchDelete = (cell) => {
            var result = confirm("Are you sure to delete ??");
            var id = cell._row.data['id'];
            if (cell._row.data['role'] != "Admin") {
                dispList = (resp) => {
                    cell.delete();
                }
                dispErr = (xhr) => {
                    alert("SDeleteMgr Error" + xhr.statusText);
                }

                var options = {
                    functionName: 'SDeleteMgr',
                    otherData: {
                        id: id
                    }
                };

                if (result) {
                    CallAjax("SMethods.php", "POST", options, dispList, dispErr);
                } else
                    console.log("delete row cancel branchmgr Branch Delete r.260!!");
            } else
                alert("admin관리자는  삭제 할 수 없습니다!!!!");

        }

        document.getElementById("idGrade").addEventListener("change", function() {
            // 선택된 옵션 가져오기
            var selectedOption = this.options[this.selectedIndex];
            //const selectedOption = document.getElementById('idGrade');

            // 선택된 옵션의 값(value) 가져오기
            var selectedValue = selectedOption.value;

            if (selectedValue === 'v7') {
                // 방과후: 전체/1단계/2단계 전용 옵션으로 교체
                setClassOptions(bangwahooClassOptions);
            } else {
                // 방과후 외: 원래 옵션으로 복원 후 활성화 제어
                setClassOptions(originalClassOptions);
                if (selectedOption.text != "원관리") {
                    setAllOptions(false);
                    setOptionState('0AB', true);
                    setSelectedValue('0AB');
                } else {
                    setAllOptions(true);
                }
            }



            // 선택된 옵션의 텍스트 가져오기
            var selectedText = this.options[this.selectedIndex].text;

            var items = [];
            dispList = (resp) => {
                resp['success'].forEach(el => {
                    var jarr = {
                        "role": el['role'] == "1" ? "지사관리" : (el['role'] == "2" ? "원관리" :
                            (el['role'] == "3" ? "가맹관리" : (el['role'] == "7" ? "방과후" : "기타"))),
                        "id": el['id'],
                        "name": el['name'],
                        "owner": el['owner'],
                        "mobile": el['mobile'],
                        "addr": el['addr'],
                        "zipcode": el['zipcode'],
                        "password": el['password'],
                        "rdate": el['rdate'],
                        "class": el['class'],
                        "confirm": el['confirm'] == 1 ? "승인" : "미승인",
                    }
                    items.push(jarr);
                });
                table.clearData();
                table.setData(items);
                var count = table.getDataCount();
                updateRowCount(count);
                setUpdateButtonState();
            }
            dispErr = (e) => {
                alert("SShowMgr Error!" + e);
            }

            var options = {
                functionName: 'SShowMgr',
                otherData: {
                    role: selectedValue,
                    id: user
                }
            };

            CallAjax("SMethods.php", "POST", options, dispList, dispErr);
        });

        var deleteIcon = function(cell, formatterParams) {
            //plain text value
            return "<i class='fa fa-trash'></i>";
        };

        table = new Tabulator("#idTable", {
            height: "700px",
            layout: "fitColumns",
            //autoColumns: true,
            selectable: true,
            rowSelectionChanged: setUpdateButtonState,
            rowSelected: setUpdateButtonState,
            rowDeselected: setUpdateButtonState,
            rowClick: function(e, row) {
                if (e.target.closest('.tabulator-cell[tabulator-field="check"]')) {
                    return;
                }
                row.toggleSelect();
                refreshUpdateButtonAfterSelection();
            },
            columns: [
                {
                    formatter: "rowSelection",
                    field: "check",
                    width: "1%",
                    titleFormatter: "rowSelection",
                    hozAlign: "center",
                    headerSort: false,
                    cellClick: function (e, cell) {
                        e.stopPropagation();
                        cell.getRow().toggleSelect();
                        refreshUpdateButtonAfterSelection();
                    },
                },
                {
                    title: "권한",
                    field: "role",
                    width: "9%",
                    editor: "input",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true,
                    },
                },
                {
                    title: "지사/가맹점명",
                    field: "owner",
                    width: "8%",
                    editor: "input",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true,
                    },
                    headerFilter: "input",
                },
                {
                    title: "과정",
                    field: "class",
                    width: "5%",
                    editor: "input",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true,
                    },
                    headerFilter: "input",
                },
                {
                    title: "유치원/학원명",
                    field: "name",
                    width: "8%",
                    editor: "input",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true,
                    },
                    headerFilter: "input",
                },
                {
                    title: "전화번호",
                    field: "mobile",
                    width: "8%",
                    editor: "input",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true,
                    },
                    headerFilter: "input",
                },
                {
                    title: "아이디",
                    field: "id",
                    width: "8%",
                    editor: "false",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true,
                    },
                    headerFilter: "input",
                },
                {
                    title: "비밀번호",
                    field: "password",
                    width: "6%",
                    editor: "input",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true,
                    },
                },
                {
                    title: "우편번호",
                    field: "zipcode",
                    width: "5%",
                    editor: "input",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true,
                    },
                },
                {
                    title: "주소",
                    field: "addr",
                    width: "25%",
                    editor: "input",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true,
                    },
                },
                {
                    title: "승인",
                    field: "confirm",
                    width: "4%",
                    // role이 '9'일 때만 에디터(select)가 활성화되도록 설정
                    editable: function(cell) {
                        // 전역 변수 role 또는 로그인 시 저장된 권한 변수를 확인
                        return role == '9'; 
                    },
                    editor: "select",
                    editorParams: {
                        values: ["승인", "미승인"],
                    },
                },
                {
                    title: "등록일",
                    field: "rdate",
                    width: "7%",
                    editor: "input",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true,
                    },
                },
                {
                    formatter: deleteIcon,
                    width: "5%",
                    hozAlign: "center",
                    cellClick: function(e, cell) {
                        BranchDelete(cell.getRow());
                    },
                },
            ],
                responsiveLayout: true,
    responsiveLayoutCollapseStart: 768, // Hide columns when the screen width is less than 768 pixels
    responsiveLayoutCollapseEnd: 0, // Show all columns when the screen width is less than 0 pixels (never)
    responsiveLayoutCollapseFormatter: function (data) {
      // Define which columns to hide in responsive layout
      return data.columns.filter(
        (column) =>
          column.field !== "name" &&
          column.field !== "id" &&
          column.field !== "confirm"
      );
    },
        });

        if (typeof table.on === "function") {
            table.on("rowSelectionChanged", setUpdateButtonState);
            table.on("rowSelected", setUpdateButtonState);
            table.on("rowDeselected", setUpdateButtonState);
        }

        BranchList();
        setUpdateButtonState();

        function DownloadExcel() {

            table.download("xlsx", "branch.xlsx", {
                sheetName: "지사관리"
            });

        }
    </script>
    <script>
    (function(){
        var info = getLocalStorage('infochaitalk');
        if(info && info.name){
            document.getElementById('gpWelcome').textContent = info.name + '님 환영합니다';
        }
    })();
    </script>
</body>

</html>
