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
    <script src="../js/roles.js"></script>
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
            /* 구분 과정 지사명 대표자명 전화 아이디 암호 [주소검색] 우편번호 주소 [등록] */
            grid-template-columns: 124px 82px 130px 130px 115px 100px 100px 92px 80px minmax(180px, 1.5fr) 72px;
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
            width: 100%;
        }

        .kgarden-table-wrap .tabulator {
            width: 100%;
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

        .branch-delete-btn {
            min-width: 48px;
            padding: 6px 9px;
            border: 0;
            border-radius: 7px;
            background: #dc3545;
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
        }

        .branch-delete-btn:hover {
            background: #b91c2c;
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
                            <label for="idOwner" id="idOwnerLabel">지사/가맹점</label>
                            <input id="idOwner" type="text" placeholder="지사회사/가맹지점명" maxlength="30">
                        </div>
                        <div class="kgarden-field">
                            <label for="idName" id="idNameLabel">원/학원명</label>
                            <input id="idName" type="text" placeholder="유치원명/학원명" maxlength="20">
                        </div>
                        <div class="kgarden-field">
                            <label for="idMobile">전화번호</label>
                            <input id="idMobile" type="text" placeholder="전화번호">
                        </div>
                        <div class="kgarden-field">
                            <label for="idID">아이디</label>
                            <input id="idID" type="text" placeholder="아이디" maxlength="30">
                        </div>
                        <div class="kgarden-field">
                            <label for="idPasswd">암호</label>
                            <input id="idPasswd" type="text" placeholder="영문/숫자 4~30자" pattern="^[a-zA-Z0-9]{4,30}$" maxlength="30" required>
                        </div>
                        <button class="btn btn-outline-primary kgarden-btn" type="button" onclick="execDaumPostcode('idZip','idAddr')">주소검색</button>
                        <div class="kgarden-field">
                            <label for="idZip">우편번호</label>
                            <input id="idZip" type="text" placeholder="우편번호">
                        </div>
                        <div class="kgarden-field kgarden-field-wide">
                            <label for="idAddr">주소</label>
                            <input id="idAddr" type="text" placeholder="주소">
                        </div>

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

        // 로그인 role 별로 등록·관리할 수 있는 하위 계정 — role 정의는 js/roles.js
        // 실제 권한 검사는 서버(Server/mgr_policy.php)에서 하며, 이 표와 같게 유지한다.
        //   본사(9) → 유치원·학원·홈스쿨·화상수업·방과후 지사(1~5),  지사 → 자기 그룹 선생
        //   grades: 구분 select 값 ('va' = 전체, 그 외 role 번호)
        const MGR_POLICY = {
            9: { title: '지사관리', grades: ['va', 1, 2, 3, 4, 5], ownerLabel: '지사명', nameLabel: '대표자명' },
            1: { title: '유치원관리', grades: [10], ownerLabel: '유치원명', nameLabel: '선생님 이름' },
            2: { title: '학원 등록', grades: [20], ownerLabel: '학원명', nameLabel: '선생님 이름' },
            3: { title: '홈스쿨 선생님 등록', grades: [30], ownerLabel: '홈스쿨 지점명', nameLabel: '선생님 이름' },
            4: { title: '화상수업 선생님 등록', grades: [40], ownerLabel: '화상수업 지점명', nameLabel: '선생님 이름' },
            5: { title: '방과후 선생님 등록', grades: [50], ownerLabel: '방과후 지점명', nameLabel: '선생님 이름' },
        };
        const actorRole = Number(role);
        const isAdmin = GP_ROLE.isAdmin(actorRole);
        const policy = MGR_POLICY[actorRole];
        const gradeSelect = document.getElementById('idGrade');

        function roleName(value) {
            return GP_ROLE.name(value);
        }

        function mgrError(e, fallback) {
            const message = (e && e.responseJSON && e.responseJSON.error) || fallback;
            if (e && e.status === 401) {
                alert(message);
                localStorage.removeItem('infochaitalk');
                window.location.href = '../index.php';
                return;
            }
            CallToast(message, 'error');
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

        // 방과후(지사·선생)는 단계 과정, 선생님은 전체 과정, 그 외 지사는 0AB 고정
        function applyClassOptions(grade) {
            if (grade === '5' || grade === '50') {
                setClassOptions(bangwahooClassOptions);
                return;
            }
            setClassOptions(originalClassOptions);
            if (!GP_ROLE.isTeacher(grade)) {
                setAllOptions(false);
                setOptionState('0AB', true);
                setSelectedValue('0AB');
            }
        }

        document.getElementById('idPasswd').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '');
        });

        AddBranch = () => {
            const newRole = gradeSelect.value === 'va' ? null : Number(gradeSelect.value);
            if (!newRole) {
                alert('등록할 구분을 선택해 주세요.');
                gradeSelect.focus();
                return;
            }

            const items = {
                id: $("#idID").val().trim(),
                name: $("#idName").val().trim(),
                owner: $("#idOwner").val().trim(),
                password: $("#idPasswd").val().trim(),
                mobile: $("#idMobile").val().trim(),
                addr: $("#idAddr").val().trim(),
                zipcode: $("#idZip").val().trim(),
                role: newRole,
                class: document.getElementById("idClass").value,
            };

            const requiredFields = [
                { label: "아이디", value: items.id, elementId: "idID" },
                { label: policy.nameLabel, value: items.name, elementId: "idName" },
                { label: policy.ownerLabel, value: items.owner, elementId: "idOwner" },
                { label: "암호", value: items.password, elementId: "idPasswd" },
                { label: "전화번호", value: items.mobile, elementId: "idMobile" },
                { label: "주소", value: items.addr, elementId: "idAddr" },
                { label: "우편번호", value: items.zipcode, elementId: "idZip" },
                { label: "과정", value: items.class, elementId: "idClass" },
            ];
            const missingFields = requiredFields.filter(field => String(field.value).trim() === "");

            if (missingFields.length > 0) {
                alert(`${missingFields.map(field => field.label).join(", ")}을(를) 다시 입력해 주세요.`);
                document.getElementById(missingFields[0].elementId).focus();
                return;
            }
            if (!/^[A-Za-z0-9]{4,30}$/.test(items.password)) {
                alert('암호는 영문/숫자 4~30자로 입력해 주세요.');
                document.getElementById('idPasswd').focus();
                return;
            }

            var options = {
                functionName: 'SRegistermgr',
                otherData: {
                    items
                }
            };
            CallAjax("SMethods.php", "POST", options, (resp) => {
                CallToast(`${items.id} 계정이 등록되었습니다.`, "success");
                table.addRow({
                    ...items,
                    role: roleName(newRole),
                    confirm: resp.confirm === 0 ? "미승인" : "승인",
                    rdate: formatDate(),
                });
                updateRowCount(table.getDataCount());
            }, (e) => mgrError(e, '등록에 실패했습니다.'));
        };

        UpdateBranch = () => {
            const selected = table.getSelectedData();
            if (!selected || selected.length === 0) {
                alert('선택된 지사나 원이 없습니다. 체크 박스를 선택해주세요.');
                return;
            }

            // 승인 상태와 과정은 본사만 변경 (서버에서도 동일하게 검사)
            const items = selected.map(el => {
                const item = {
                    id: el.id,
                    owner: el.owner,
                    name: el.name,
                    mobile: el.mobile,
                    addr: el.addr,
                    zipcode: el.zipcode,
                    password: el.password,
                };
                if (isAdmin) {
                    item.confirm = el.confirm === "승인" ? 1 : 0;
                    item.class = el.class;
                }
                return item;
            });

            var options = {
                functionName: 'SShowConfirmUpdate',
                otherData: {
                    items
                }
            };
            CallAjax("SMethods.php", "POST", options, () => {
                CallToast('수정되었습니다.', "success");
                BranchList();
            }, (e) => mgrError(e, '수정에 실패했습니다.'));
        }

        function updateRowCount(cnt) {
            document.getElementById("table-header").innerText = `총 ${isAdmin ? '지사' : '선생님'} 수: ${cnt}`;
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

        // 조회 범위는 서버가 로그인 세션 기준으로 결정 (본사: 전체, 그 외: 본인이 등록한 하위 계정)
        BranchList = () => {
            var options = {
                functionName: 'SShowMgr',
                otherData: {
                    role: gradeSelect.value
                }
            };
            CallAjax("SMethods.php", "POST", options, (resp) => {
                const items = (resp.success || []).map(el => ({
                    ...el,
                    role: roleName(el.role),
                    confirm: Number(el.confirm) === 1 ? "승인" : "미승인",
                }));
                table.setData(items);
                updateRowCount(items.length);
                setUpdateButtonState();
            }, (e) => mgrError(e, '목록을 불러오지 못했습니다.'));
        }

        BranchDelete = (row) => {
            const data = row.getData();
            if (data.role === GP_ROLE.name(GP_ROLE.ADMIN)) {
                alert("admin관리자는  삭제 할 수 없습니다!!!!");
                return;
            }
            if (!confirm(`${data.id} 계정을 삭제할까요?`)) return;

            var options = {
                functionName: 'SDeleteMgr',
                otherData: {
                    id: data.id
                }
            };
            CallAjax("SMethods.php", "POST", options, () => {
                row.delete();
                updateRowCount(table.getDataCount());
                setUpdateButtonState();
            }, (e) => mgrError(e, '삭제에 실패했습니다.'));
        }

        gradeSelect.addEventListener("change", function() {
            applyClassOptions(this.value);
            BranchList();
        });

        var deleteIcon = function(cell, formatterParams) {
            return "<button type='button' class='branch-delete-btn' aria-label='지사 삭제'>삭제</button>";
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
                    width: 42,
                    minWidth: 42,
                    frozen: true,
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
                },
                {
                    title: policy ? policy.ownerLabel : "소속",
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
                    // 과정 변경은 본사만 가능
                    editable: function() {
                        return isAdmin;
                    },
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
                    title: policy ? policy.nameLabel : "이름",
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
                    minWidth: 180,
                    widthGrow: 1,
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
                    // 승인 변경은 본사만 가능
                    editable: function() {
                        return isAdmin;
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
                },
                {
                    title: "삭제",
                    formatter: deleteIcon,
                    width: 72,
                    minWidth: 72,
                    visible: true,
                    responsive: 0,
                    hozAlign: "center",
                    headerHozAlign: "center",
                    headerSort: false,
                    cellClick: function(e, cell) {
                        e.stopPropagation();
                        BranchDelete(cell.getRow());
                    },
                },
            ],
            responsiveLayout: "hide",
        });

        if (typeof table.on === "function") {
            table.on("rowSelectionChanged", setUpdateButtonState);
            table.on("rowSelected", setUpdateButtonState);
            table.on("rowDeselected", setUpdateButtonState);
        }

        if (policy) {
            gradeSelect.replaceChildren(...policy.grades.map(value => new Option(value === 'va' ? '전체' : GP_ROLE.name(value), value)));
            document.getElementById("brachname").textContent = `[${user}] ${policy.title} 리스트`;
            document.title = policy.title;
            document.getElementById("idOwnerLabel").textContent = policy.ownerLabel;
            document.getElementById("idNameLabel").textContent = policy.nameLabel;
            document.getElementById("idOwner").placeholder = policy.ownerLabel;
            document.getElementById("idName").placeholder = policy.nameLabel;
            if (!isAdmin && owner) document.getElementById("idOwner").value = owner;
            applyClassOptions(gradeSelect.value);
            BranchList();
        } else {
            CallToast("지사 관리 권한으로 로그인하세요", "error");
            document.querySelectorAll('.kgarden-toolbar input, .kgarden-toolbar select, .kgarden-toolbar button, .kgarden-actions button')
                .forEach(el => el.disabled = true);
        }
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
