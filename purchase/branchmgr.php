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
    <script src="../js/roles.js?v=<?= filemtime(__DIR__ . '/../js/roles.js') ?>"></script>
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

        /* 본사가 선생 등록 시 소속 지사 칸이 구분 다음에 추가된다 */
        @media (min-width: 1200px) {
            .kgarden-form.has-parent {
                grid-template-columns: 124px 150px 82px 130px 130px 115px 100px 100px 92px 80px minmax(180px, 1.5fr) 72px;
            }
            /* 방문수업 선생님 등록: 주소검색·우편번호·주소 칸 없음 */
            .kgarden-form.no-address {
                grid-template-columns: 124px 82px 130px 130px 115px 100px 100px 72px;
            }
            .kgarden-form.no-address.has-parent {
                grid-template-columns: 124px 150px 82px 130px 130px 115px 100px 100px 72px;
            }
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

        .branch-activate-btn {
            background: #2E7D46;
        }

        .branch-activate-btn:hover {
            background: #256339;
        }

        .tabulator-row.branch-inactive .tabulator-cell:not([tabulator-field="status"]) {
            color: #9ca3af;
        }

        /* 입력 폼에서 수정 중인 행 */
        .tabulator-row.branch-editing {
            background: #fff7d6 !important;
        }

        .tabulator-row:not(.tabulator-calcs) {
            cursor: pointer;
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
                        <div class="kgarden-field" id="idParentField" style="display:none">
                            <label for="idParent">소속 지사</label>
                            <select id="idParent"></select>
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
                            <input id="idID" type="text" placeholder="영문/숫자 2~30자" pattern="^[a-zA-Z0-9]{2,30}$" maxlength="30" autocomplete="off">
                        </div>
                        <div class="kgarden-field">
                            <label for="idPasswd">암호</label>
                            <input id="idPasswd" type="text" placeholder="영문/숫자 4~30자" pattern="^[a-zA-Z0-9]{4,30}$" maxlength="30" required>
                        </div>
                        <button id="idAddrSearch" class="btn btn-outline-primary kgarden-btn" type="button" onclick="execDaumPostcode('idZip','idAddr')">주소검색</button>
                        <div class="kgarden-field">
                            <label for="idZip">우편번호</label>
                            <input id="idZip" type="text" placeholder="우편번호">
                        </div>
                        <div class="kgarden-field kgarden-field-wide">
                            <label for="idAddr">주소</label>
                            <input id="idAddr" type="text" placeholder="주소">
                        </div>

                        <button id="idSubmitBtn" class="btn btn-success kgarden-btn" type="button" onclick="SubmitBranch()">등록</button>
                        <button id="idCancelEditBtn" class="btn btn-outline-secondary kgarden-btn" type="button" onclick="CancelEdit()" style="display:none">취소</button>
                    </div>
                </div>
                <div class="kgarden-body">
                    <div class="kgarden-actions">
                        <div id="table-header">총 지사 수: 0 지사</div>
                        <div class="kgarden-actions-right">
                            <button class="btn btn-outline-secondary kgarden-btn" type="button" data-toggle="tooltip" title="지사 List Excel 저장" onclick="DownloadExcel()">
                                <i class="fa-solid fa-print"></i> Excel
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
        //   본사(9) → 유치원·학원·방문수업·화상수업·방과후 지사(1~5) 등록, 선생(10~50)은 소속 지사를 골라 등록·조회·관리,  지사 → 자기 그룹 선생
        //   grades: 구분 select 값 ('va' = 전체, 'sales' = 영업 계정, 그 외 role 번호)
        //   영업 계정: 본사만 등록, 유치원생(100) + 과정 '영업' — maljam/index.php 에서 각 별의 1호만 열린다 (Server/catalog.php)
        const MGR_POLICY = {
            9: { title: '지사관리', grades: ['va', 1, 2, 3, 4, 5, 10, 20, 30, 40, 50, 'sales'], ownerLabel: '지사/원명', nameLabel: '대표자/교사' },
            1: { title: '유치원관리', grades: [10], ownerLabel: '유치원명', nameLabel: '교사 이름' },
            2: { title: '학원 등록', grades: [20], ownerLabel: '학원명', nameLabel: '교사 이름' },
            3: { title: '방문수업 교사 등록', grades: [30], ownerLabel: '방문수업 지점명', nameLabel: '교사 이름' },
            4: { title: '화상수업 교사 등록', grades: [40], ownerLabel: '화상수업 지점명', nameLabel: '교사 이름' },
            5: { title: '방과후 교사 등록', grades: [50], ownerLabel: '방과후 지점명', nameLabel: '교사 이름' },
        };
        const actorRole = Number(role);
        const isAdmin = GP_ROLE.isAdmin(actorRole);
        const policy = MGR_POLICY[actorRole];
        const gradeSelect = document.getElementById('idGrade');

        const SALES_CLASS = '영업';
        const isSalesGrade = (grade) => String(grade) === 'sales';

        function roleName(value) {
            return isSalesGrade(value) ? '영업' : GP_ROLE.name(value);
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

        // 과정 없음 (빈 항목) — 0AB/A/B/T 조합 과정은 더 이상 쓰지 않는다
        const blankClassOptions = [{ value: '', text: '', selected: true }];

        const bangwahooClassOptions = [
            { value: '전체',  text: '전체',  selected: true },
            { value: '1단계', text: '1단계' },
            { value: '2단계', text: '2단계' },
        ];

        // 유치원 과정 — 값은 과정 코드 그대로(0 잼잼별, A 말잼별, B 글잼별), 이름만 바꿔 보여준다
        //   유치원 학생관리(kgardenmgr.php STEP_OPTIONS)가 선생님 과정 코드로 고를 수 있는 과정을 제한한다
        const KINDER_COURSE = { '0': '잼잼별', 'A': '말잼별', 'B': '글잼별' };
        const kinderClassLabel = (code) => [...String(code || '')].map(c => KINDER_COURSE[c]).filter(Boolean).join('·');
        const kinderClassOptions = ['전체', '0AB', '0', 'A', 'B', '0A', '0B', 'AB']
            .map((value, i) => ({ value, text: kinderClassLabel(value) || value, selected: i === 0 }));
        const isKinderRole = (roleText) => [GP_ROLE.name(1), GP_ROLE.name(10)].includes(roleText);

        // 방문수업 과정 — 지사는 전체 고정, 교사는 방문수업/급파한 중 하나 (서버 검증: Server/mgr_policy.php gpHomeschoolClass)
        //   교사 과정에 따라 주문 교재군과 학습 콘텐츠 이용이 달라진다 (Server/catalog.php)
        const homeschoolBranchClassOptions = [{ value: '전체', text: '전체', selected: true }];
        const homeschoolTeacherClassOptions = [
            { value: '방문수업', text: '방문수업', selected: true },
            { value: '급파한', text: '급파한' },
        ];

        // 학원지사 과정 — 학원 가격표 교재군 (서버 검증: Server/mgr_policy.php gpMgrValidate)
        const academyClassOptions = ['전체', '급파한', '한자파닉스', '한어사', '어휘잼', '어휘의신']
            .map((value, i) => ({ value, text: value, selected: i === 0 }));

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

        // 방과후(지사·선생)는 단계 과정, 방문수업은 방문수업, 유치원은 잼잼별/말잼별/글잼별, 학원지사는 학원 교재군,
        // 그 외(전체, 화상수업 지사·선생, 학원선생)는 과정 없음
        function applyClassOptions(grade) {
            if (grade === '5' || grade === '50') {
                setClassOptions(bangwahooClassOptions);
                return;
            }
            if (grade === '3') {
                setClassOptions(homeschoolBranchClassOptions);
                return;
            }
            if (grade === '30') {
                setClassOptions(homeschoolTeacherClassOptions);
                return;
            }
            if (grade === '2') {
                setClassOptions(academyClassOptions);
                return;
            }
            if (grade === '1' || grade === '10') {
                setClassOptions(kinderClassOptions);
                return;
            }
            setClassOptions(blankClassOptions);
        }

        // 지사·선생 아이디는 영문/숫자만 (한글·특수문자 입력 즉시 제거)
        document.getElementById('idID').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '');
        });

        document.getElementById('idPasswd').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '');
        });

        AddBranch = () => {
            const sales = isSalesGrade(gradeSelect.value);
            const newRole = gradeSelect.value === 'va' ? null : sales ? 'sales' : Number(gradeSelect.value);
            if (!newRole) {
                alert('등록할 구분을 선택해 주세요.');
                gradeSelect.focus();
                return;
            }
            // 본사가 선생을 등록할 때는 소속 지사를 골라 부모(mid)로 보낸다
            const needParent = isAdmin && GP_ROLE.isTeacher(newRole);
            if (needParent && !parentSelect.value) {
                alert('소속 지사를 선택해 주세요.');
                parentSelect.focus();
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
            if (needParent) items.mid = parentSelect.value;

            // 영업 계정은 이름·아이디·암호만 필수
            const requiredFields = [
                { label: "아이디", value: items.id, elementId: "idID" },
                { label: policy.nameLabel, value: items.name, elementId: "idName" },
                { label: "암호", value: items.password, elementId: "idPasswd" },
            ];
            if (!sales) {
                requiredFields.push({ label: policy.ownerLabel, value: items.owner, elementId: "idOwner" },
                    { label: "전화번호", value: items.mobile, elementId: "idMobile" });
            }
            if (!noAddressRole(newRole)) {
                requiredFields.push({ label: "주소", value: items.addr, elementId: "idAddr" },
                    { label: "우편번호", value: items.zipcode, elementId: "idZip" });
            }
            // 과정 목록이 빈 항목뿐인 구분은 과정 없이 등록
            if (Array.from(document.getElementById("idClass").options).some(o => o.value !== '')) {
                requiredFields.push({ label: "과정", value: items.class, elementId: "idClass" });
            }
            const missingFields = requiredFields.filter(field => String(field.value).trim() === "");

            if (missingFields.length > 0) {
                alert(`${missingFields.map(field => field.label).join(", ")}을(를) 다시 입력해 주세요.`);
                document.getElementById(missingFields[0].elementId).focus();
                return;
            }
            if (!/^[A-Za-z0-9]{2,30}$/.test(items.id)) {
                alert('아이디는 영문/숫자 2~30자로 입력해 주세요.');
                document.getElementById('idID').focus();
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
                    ...(sales ? { class: SALES_CLASS } : {}),
                    role: roleName(newRole),
                    roleNo: newRole,
                    confirm: resp.confirm === 0 ? "미승인" : "승인",
                    rdate: formatDate(),
                });
                updateRowCount(table.getDataCount());
            }, (e) => mgrError(e, '등록에 실패했습니다.'));
        };

        // 목록 행 클릭 → 입력 폼에 채워 수정 (구분·아이디·소속 지사는 바꿀 수 없음, 과정은 본사만 — 서버 SShowConfirmUpdate)
        let editing = null;
        const formIds = ['idOwner', 'idName', 'idMobile', 'idID', 'idPasswd', 'idZip', 'idAddr'];

        function EditBranch(row) {
            const data = row.getData();
            editing = data;
            gradeSelect.dataset.prev = gradeSelect.value;
            if (![...gradeSelect.options].some(o => o.value === String(data.roleNo))) {
                gradeSelect.add(new Option(roleName(data.roleNo), data.roleNo));
            }
            gradeSelect.value = String(data.roleNo);
            gradeSelect.disabled = true;
            applyClassOptions(gradeSelect.value);
            applyAddressFields(gradeSelect.value);
            document.getElementById('idParentField').style.display = 'none';
            document.querySelector('.kgarden-form').classList.remove('has-parent');

            const classSelect = document.getElementById('idClass');
            // 저장값이 선택지에 없으면(예전 값) 첫 선택지로 — 저장하면 그 값으로 바뀐다
            if ([...classSelect.options].some(o => o.value === (data.class || ''))) classSelect.value = data.class || '';
            classSelect.disabled = !isAdmin;

            const values = { idOwner: data.owner, idName: data.name, idMobile: data.mobile, idID: data.id, idPasswd: '', idZip: data.zipcode, idAddr: data.addr };
            formIds.forEach(id => { document.getElementById(id).value = values[id] || ''; });
            document.getElementById('idID').readOnly = true;
            document.getElementById('idPasswd').placeholder = '변경할 때만 입력';
            document.getElementById('idSubmitBtn').textContent = '수정';
            document.getElementById('idCancelEditBtn').style.display = '';
            table.getRows().forEach(r => r.getElement().classList.toggle('branch-editing', r === row));
            document.querySelector('.kgarden-toolbar').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        CancelEdit = () => {
            if (!editing) return;
            editing = null;
            gradeSelect.value = gradeSelect.dataset.prev;
            gradeSelect.disabled = false;
            document.getElementById('idClass').disabled = false;
            formIds.forEach(id => { document.getElementById(id).value = ''; });
            if (!isAdmin && owner) document.getElementById('idOwner').value = owner;
            document.getElementById('idID').readOnly = false;
            document.getElementById('idPasswd').placeholder = '영문/숫자 4~30자';
            document.getElementById('idSubmitBtn').textContent = '등록';
            document.getElementById('idCancelEditBtn').style.display = 'none';
            table.getRows().forEach(r => r.getElement().classList.remove('branch-editing'));
            applyClassOptions(gradeSelect.value);
            applyAddressFields(gradeSelect.value);
            applyParentField(gradeSelect.value);
        };

        SubmitBranch = () => (editing ? SaveEdit() : AddBranch());

        function SaveEdit() {
            const item = {
                id: editing.id,
                owner: $('#idOwner').val().trim(),
                name: $('#idName').val().trim(),
                mobile: $('#idMobile').val().trim(),
                addr: $('#idAddr').val().trim(),
                zipcode: $('#idZip').val().trim(),
            };
            const sales = isSalesGrade(editing.roleNo);
            const missing = [
                { label: policy.ownerLabel, value: item.owner, elementId: 'idOwner', skip: sales },
                { label: policy.nameLabel, value: item.name, elementId: 'idName' },
                { label: '전화번호', value: item.mobile, elementId: 'idMobile', skip: sales },
            ].filter(field => !field.skip && field.value === '');
            if (missing.length > 0) {
                alert(`${missing.map(field => field.label).join(', ')}을(를) 다시 입력해 주세요.`);
                document.getElementById(missing[0].elementId).focus();
                return;
            }
            const password = $('#idPasswd').val().trim();
            if (password !== '') {
                if (!/^[A-Za-z0-9]{4,30}$/.test(password)) {
                    alert('암호는 영문/숫자 4~30자로 입력해 주세요.');
                    document.getElementById('idPasswd').focus();
                    return;
                }
                item.password = password;
            }
            if (isAdmin) {
                item.confirm = editing.confirm === '승인' ? 1 : 0;
                if (!sales) item.class = document.getElementById('idClass').value;
            }
            CallAjax("SMethods.php", "POST", { functionName: 'SShowConfirmUpdate', otherData: { items: [item] } }, () => {
                CallToast(`${item.id} 계정을 수정했습니다.`, "success");
                CancelEdit();
                BranchList();
            }, (e) => mgrError(e, '수정에 실패했습니다.'));
        }

        function updateRowCount(cnt) {
            document.getElementById("table-header").innerText = `총 ${isAdmin ? '계정' : '교사'} 수: ${cnt}`;
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
                const items = (resp.success || []).map(el => {
                    const roleNo = Number(el.role) === 100 && el.class === SALES_CLASS ? 'sales' : Number(el.role);
                    return {
                        ...el,
                        role: roleName(roleNo),
                        roleNo,
                        confirm: Number(el.confirm) === 1 ? "승인" : "미승인",
                        status: Number(el.status) === 1 ? 1 : 0,
                    };
                });
                table.setData(items);
                updateRowCount(items.length);
                loadPasswords(items.map(el => el.id));
            }, (e) => mgrError(e, '목록을 불러오지 못했습니다.'));
        }

        // 비밀번호 열 표시 방식 — 'view': 비밀번호 표시(확인용 사본이 없는 계정은 재설정 버튼), 'reset': 재설정 버튼만
        //   정착 후 'reset' 으로 바꾸면 비밀번호를 조회하지 않는다
        const PASSWORD_DISPLAY = 'view';

        function loadPasswords(ids) {
            if (PASSWORD_DISPLAY !== 'view' || ids.length === 0) return;
            CallAjax("SMethods.php", "POST", { functionName: 'SViewManagedPasswords', otherData: { ids } }, (resp) => {
                const passwords = resp.success || {};
                table.getRows().forEach(row => {
                    const pw = passwords[row.getData().id];
                    if (pw) row.update({ pwView: pw });
                });
            }, (e) => mgrError(e, '비밀번호를 불러오지 못했습니다.'));
        }

        BranchDelete = (row) => {
            const data = row.getData();
            if (data.role === GP_ROLE.name(GP_ROLE.ADMIN)) {
                alert("admin관리자는  삭제 할 수 없습니다!!!!");
                return;
            }
            if (!confirm(`${data.id} 계정을 삭제할까요?`)) return;

            // 하위 계정(교사·학생)이 있으면 서버가 개수를 알려 준다 — 한 번 더 확인하고 함께 삭제
            const remove = (cascade) => CallAjax("SMethods.php", "POST", { functionName: 'SDeleteMgr', otherData: cascade ? { id: data.id, cascade: 1 } : { id: data.id } }, (resp) => {
                if (resp.deleted > 1) {
                    CallToast(`${data.id} 계정과 하위 계정 ${resp.deleted - 1}개를 삭제했습니다.`, "success");
                    BranchList();   // 목록에 있던 하위 교사도 지워졌으므로 다시 불러온다
                    return;
                }
                row.delete();
                updateRowCount(table.getDataCount());
            }, (e) => {
                const children = e && e.status === 409 && e.responseJSON && e.responseJSON.children;
                if (children && !cascade) {
                    if (confirm(`${data.id} 아래에 하위 계정(교사·학생)이 ${children}개 있습니다.\n하위 계정까지 모두 삭제할까요? 되돌릴 수 없습니다.`)) remove(true);
                    return;
                }
                mgrError(e, '삭제에 실패했습니다.');
            });
            remove(false);
        }

        // 본사가 선생 구분을 고르면 소속 지사(선생 role 의 바로 위 지사) 목록을 보여준다
        const parentSelect = document.getElementById('idParent');
        function applyParentField(grade) {
            const show = isAdmin && GP_ROLE.isTeacher(grade);
            document.getElementById('idParentField').style.display = show ? '' : 'none';
            document.querySelector('.kgarden-form').classList.toggle('has-parent', show);
            parentSelect.replaceChildren(new Option('지사 선택', ''));
            if (!show) return;

            const branchRole = Number(grade) / 10;
            CallAjax("SMethods.php", "POST", { functionName: 'SShowMgr', otherData: { role: String(branchRole) } }, (resp) => {
                if (gradeSelect.value !== grade) return;   // 그 사이 구분이 바뀜
                parentSelect.replaceChildren(new Option('지사 선택', ''),
                    ...(resp.success || []).filter(b => Number(b.status) === 1).map(b => {
                        const o = new Option(`${b.owner || b.name} (${b.id})`, b.id);
                        o.dataset.owner = b.owner || '';
                        return o;
                    }));
            }, (e) => mgrError(e, '지사 목록을 불러오지 못했습니다.'));
        }

        // 선생의 지사/원명은 소속 지사 이름으로 채운다 (지사가 직접 등록할 때와 같게)
        parentSelect.addEventListener("change", function() {
            const opt = this.selectedOptions[0];
            if (opt && opt.value) document.getElementById("idOwner").value = opt.dataset.owner;
        });

        // 방문수업 선생님은 주소 없이 등록 — 입력 폼의 주소검색/우편번호/주소와 목록의 우편번호/주소 컬럼을 숨긴다
        const noAddressRole = (grade) => String(grade) === '30' || isSalesGrade(grade);
        function applyAddressFields(grade) {
            const off = noAddressRole(grade);
            document.querySelector('.kgarden-form').classList.toggle('no-address', off);
            document.getElementById('idAddrSearch').style.display = off ? 'none' : '';
            ['idZip', 'idAddr'].forEach(elementId => {
                const input = document.getElementById(elementId);
                input.closest('.kgarden-field').style.display = off ? 'none' : '';
                if (off) input.value = '';
            });
            const toggleColumns = () => {
                // 나머지 컬럼은 고정 너비라 주소 컬럼이 빠진 자리는 지점명 컬럼이 채운다
                const ownerColumn = table.getColumn('owner');
                if (ownerBaseWidth === null) ownerBaseWidth = ownerColumn.getWidth();
                ownerColumn.setWidth(ownerBaseWidth);
                ['zipcode', 'addr'].forEach(field => off ? table.hideColumn(field) : table.showColumn(field));
                if (!off) return;
                const used = table.getColumns().filter(column => column.isVisible()).reduce((sum, column) => sum + column.getWidth(), 0);
                const free = table.element.querySelector('.tabulator-tableholder').clientWidth - used - 2;
                if (free > 0) ownerColumn.setWidth(ownerBaseWidth + free);
            };
            if (table.initialized) toggleColumns(); else table.on('tableBuilt', toggleColumns);
        }
        let ownerBaseWidth = null;

        gradeSelect.addEventListener("change", function() {
            applyClassOptions(this.value);
            applyAddressFields(this.value);
            applyParentField(this.value);
            BranchList();
        });

        var deleteIcon = function(cell, formatterParams) {
            return "<button type='button' class='branch-delete-btn' aria-label='지사 삭제'>삭제</button>";
        };

        // 활성/비활성 전환 (비활성 = 로그인·사용 차단). 삭제는 본사만
        var statusIcon = function(cell) {
            return cell.getValue() === 1
                ? "<button type='button' class='branch-delete-btn' aria-label='계정 비활성'>비활성</button>"
                : "<button type='button' class='branch-delete-btn branch-activate-btn' aria-label='계정 활성'>활성</button>";
        };

        BranchSetStatus = (row) => {
            const data = row.getData();
            const next = data.status === 1 ? 0 : 1;
            const message = next === 0
                ? `${data.id} 계정을 비활성할까요?\n비활성된 계정은 로그인하거나 사용할 수 없습니다.`
                : `${data.id} 계정을 다시 활성할까요?`;
            if (!confirm(message)) return;
            CallAjax("SMethods.php", "POST", { functionName: 'SSetMgrStatus', otherData: { id: data.id, status: next } }, () => {
                row.update({ status: next });
                row.reformat();
            }, (e) => mgrError(e, '상태 변경에 실패했습니다.'));
        }

        // 임시 비밀번호 발급 — 계정 주인이 그 비밀번호로 로그인하면 새 비밀번호로 바꾸게 된다
        function resetPassword(row) {
            const data = row.getData();
            if (!confirm(`${data.id} 계정의 비밀번호를 재설정할까요?\n임시 비밀번호가 발급되고, 다음 로그인 때 새 비밀번호로 바꾸게 됩니다.`)) {
                return;
            }
            CallAjax("SMethods.php", "POST", { functionName: "SResetPassword", otherData: { id: data.id } }, (resp) => {
                if (PASSWORD_DISPLAY === 'view') row.update({ pwView: resp.temp });
                prompt(`${data.id} 임시 비밀번호입니다. 계정 주인에게 전달해 주세요.\n(이 창을 닫으면 다시 볼 수 없습니다)`, resp.temp);
            }, (e) => mgrError(e, '비밀번호 재설정에 실패했습니다.'));
        }

        table = new Tabulator("#idTable", {
            height: "700px",
            layout: "fitColumns",
            //autoColumns: true,
            columns: [
                {
                    title: "권한",
                    field: "role",
                    width: "9%",
                },
                {
                    title: policy ? policy.ownerLabel : "소속",
                    field: "owner",
                    width: "8%",
                    headerFilter: "input",
                },
                {
                    title: "과정",
                    field: "class",
                    // 유치원 계정은 과정 이름으로 표시 (저장값은 과정 코드)
                    formatter: (cell) => {
                        const value = cell.getValue() || '';
                        return isKinderRole(cell.getRow().getData().role) ? (kinderClassLabel(value) || value) : GP_ROLE.courseLabel(value);
                    },
                    width: "5%",
                    headerFilter: "input",
                },
                {
                    title: policy ? policy.nameLabel : "이름",
                    field: "name",
                    width: "8%",
                    headerFilter: "input",
                },
                {
                    title: "전화번호",
                    field: "mobile",
                    width: "8%",
                    headerFilter: "input",
                },
                {
                    title: "아이디",
                    field: "id",
                    width: "8%",
                    headerFilter: "input",
                },
                {
                    // 로그인용 비밀번호는 해시 — 표시는 확인용 사본(SViewManagedPasswords), 없으면 임시 비밀번호 재설정 (SResetPassword)
                    title: "비밀번호",
                    field: "pwView",
                    width: "6%",
                    hozAlign: "center",
                    headerSort: false,
                    download: false,
                    formatter: (cell) => cell.getValue()
                        ? Object.assign(document.createElement('span'), { textContent: cell.getValue() })
                        : "<button type='button' class='btn btn-sm btn-outline-secondary' style='padding:0 8px; font-size:12px'>재설정</button>",
                    cellClick: function(e, cell) {
                        e.stopPropagation();
                        if (e.target.closest('button')) resetPassword(cell.getRow());
                    },
                },
                {
                    title: "우편번호",
                    field: "zipcode",
                    width: "5%",
                },
                {
                    title: "주소",
                    field: "addr",
                    minWidth: 180,
                    widthGrow: 1,
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
                    // 고르면 바로 저장
                    cellEdited: function(cell) {
                        const data = cell.getRow().getData();
                        const items = [{ id: data.id, confirm: cell.getValue() === "승인" ? 1 : 0 }];
                        CallAjax("SMethods.php", "POST", { functionName: 'SShowConfirmUpdate', otherData: { items } }, () => {
                            CallToast(`${data.id} 계정을 ${cell.getValue()}(으)로 변경했습니다.`, "success");
                        }, (e) => {
                            cell.restoreOldValue();
                            mgrError(e, '승인 변경에 실패했습니다.');
                        });
                    },
                },
                {
                    // 본사 화면: 계정을 등록한 상위 계정 (선생 → 소속 지사)
                    title: "등록 지사",
                    field: "mid",
                    width: "7%",
                    visible: isAdmin,
                    headerFilter: "input",
                },
                {
                    title: "등록일",
                    field: "rdate",
                    width: "7%",
                },
                {
                    title: "상태",
                    field: "status",
                    formatter: statusIcon,
                    width: 72,
                    minWidth: 72,
                    responsive: 0,
                    hozAlign: "center",
                    headerHozAlign: "center",
                    headerSort: false,
                    download: false,
                    cellClick: function(e, cell) {
                        e.stopPropagation();
                        BranchSetStatus(cell.getRow());
                    },
                },
                {
                    title: "삭제",
                    formatter: deleteIcon,
                    width: 72,
                    minWidth: 72,
                    visible: isAdmin,
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
            // 비활성 계정은 흐리게
            rowFormatter: function(row) {
                row.getElement().classList.toggle('branch-inactive', row.getData().status !== 1);
            },
            responsiveLayout: "hide",
        });

        if (typeof table.on === "function") {
            // 행 클릭 → 위 입력 폼에서 수정 (승인·버튼 칸 제외)
            table.on("rowClick", function(e, row) {
                if (e.target.closest('button, input, select, .tabulator-cell[tabulator-field="confirm"]')) {
                    return;
                }
                EditBranch(row);
            });
        }

        if (policy) {
            gradeSelect.replaceChildren(...policy.grades.map(value => new Option(value === 'va' ? '전체' : roleName(value), value)));
            // 제목은 아이디 대신 이름·지점명 (같거나 비면 생략, 둘 다 없으면 아이디)
            const heading = [...new Set([name, owner].map(v => String(v || '').trim()).filter(Boolean))].join(' ') || user;
            document.getElementById("brachname").textContent = `[${heading}] ${policy.title} 리스트`;
            document.title = policy.title;
            document.getElementById("idOwnerLabel").textContent = policy.ownerLabel;
            document.getElementById("idNameLabel").textContent = policy.nameLabel;
            document.getElementById("idOwner").placeholder = policy.ownerLabel;
            document.getElementById("idName").placeholder = policy.nameLabel;
            if (!isAdmin && owner) document.getElementById("idOwner").value = owner;
            applyClassOptions(gradeSelect.value);
            applyAddressFields(gradeSelect.value);
            BranchList();
        } else {
            CallToast("지사 관리 권한으로 로그인하세요", "error");
            document.querySelectorAll('.kgarden-toolbar input, .kgarden-toolbar select, .kgarden-toolbar button, .kgarden-actions button')
                .forEach(el => el.disabled = true);
        }

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
