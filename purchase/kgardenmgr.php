<!DOCTYPE html>
<html lang="utf-8">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <?php
    include "../libs/header.php";
    include "../libs/include.php";
    include '../libs/includescr.php';
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script src="https://unpkg.com/@pdf-lib/fontkit@0.0.4/dist/fontkit.umd.min.js"></script>
    <script src="https://unpkg.com/downloadjs@1.4.7"></script>

    <script src="../js/common.js"></script>
    <script src="../js/header.js"></script>
    <script src="../js/libpdf.js"></script>


    <title>학생등록관리</title>
    <style>
        :root {
            --kg-bg: #f3f6fb;
            --kg-panel: #ffffff;
            --kg-line: #d9e2ef;
            --kg-text: #1f2937;
            --kg-muted: #6b7280;
            --kg-primary: #2563eb;
            --kg-primary-dark: #1d4ed8;
            --kg-success: #16a34a;
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
        }

        .kgarden-card .card-header {
            border-bottom: 1px solid var(--kg-line);
        }

        .kgarden-toolbar {
            background: linear-gradient(135deg, #ffffff 0%, #eef6ff 100%) !important;
            padding: 18px;
        }

        .kgarden-form {
            display: grid;
            grid-template-columns: 120px minmax(120px, 1fr) minmax(120px, 1fr) minmax(120px, 1fr) minmax(160px, 1.4fr) minmax(100px, 0.8fr) minmax(100px, 0.8fr) 150px;
            gap: 12px;
            align-items: end;
            width: 100%;
        }

        .kgarden-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
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
            padding: 7px 10px;
            font-size: 14px;
            background-color: #fff;
        }

        .kgarden-field input:focus,
        .kgarden-field select:focus {
            border-color: var(--kg-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.16);
        }

        .kgarden-field input::placeholder {
            color: #9ca3af;
        }

        .kgarden-field input.warning::placeholder {
            color: #dc2626;
        }

        .kgarden-btn {
            height: 38px;
            border-radius: 10px;
            font-weight: 700;
            white-space: nowrap;
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
            min-width: 900px;
        }

        .kgarden-table-wrap .tabulator {
            border: 0;
            font-size: 14px;
        }

        .kgarden-table-wrap .tabulator-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--kg-line);
        }

        .kgarden-table-wrap .tabulator-col {
            background: #f8fafc;
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

            .kgarden-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .kgarden-actions-right {
                display: grid;
                grid-template-columns: 1fr 1fr;
                width: 100%;
            }

            .kgarden-actions-right .btn {
                width: 100%;
            }

            #table-header {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    <link rel="stylesheet" href="../role-nav.css">
    <script src="../js/roles.js"></script>
    <script src="../js/role-nav.js" defer></script>
</head>

<body class="kgarden-page">
    <header id="roleNav"></header>
    <div class="kgarden-shell">
        <section class="content-header kgarden-heading">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 id="idHead">학생등록 관리리스트</h5>
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
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card kgarden-card">
                        <div class="card-header kgarden-toolbar">
                            <div class="kgarden-form">
                                <button class="btn btn-primary kgarden-btn" type="button" onclick="listChild()">전체조회</button>

                                <div class="kgarden-field">
                                    <label for="idStudent">과정 선택</label>
                                    <select class="form-select form-control-sm" id="idStudent" data-placeholder="Choose Items">
                                        <option value="v0"></option>
                                        <!-- <option val="va">전체</option> -->
                                        <option value="sb">잼잼별</option>
                                        <option value="s1">말잼별</option>
                                        <option value="s2">글잼별</option>
                                        <!-- <option val="s3">7세-Step3</option> -->
                                    </select>
                                </div>

                                <div class="kgarden-field">
                                    <label for="idClass">반선택</label>
                                    <select class="form-select form-control-sm" id="idClass" data-placeholder="Choose Items">
                                        <option value="v0"></option>
                                    </select>
                                </div>

                                <div class="kgarden-field">
                                    <label for="idClassname">반이름</label>
                                    <input class="form-control" id="idClassname" type="text" placeholder="반이름">
                                </div>

                                <div class="kgarden-field">
                                    <label for="idNick">시작아이디</label>
                                    <input class="form-control" id="idNick" type="text" placeholder="영문 아이디" pattern="^[a-zA-Z\s]*$" required>
                                </div>

                                <div class="kgarden-field">
                                    <label for="idNumstudent">원아수</label>
                                    <input class="form-control" id="idNumstudent" type="text" placeholder="원아수">
                                </div>

                                <div class="kgarden-field">
                                    <label for="idStartNum">시작번호</label>
                                    <input class="form-control" id="idStartNum" type="text" placeholder="시작번호" value="1">
                                </div>

                                <button class="btn btn-outline-primary kgarden-btn" type="button" onclick="addChild()">원아아이디생성</button>
                            </div>
                        </div>
                        <div class="card-body pad kgarden-body">
                            <div class="kgarden-actions">
                                <div id="table-header">총 학생 수: 0 명</div>
                                <div class="kgarden-actions-right">
                                    <button id="idSaveChildBtn" class="btn btn-outline-primary kgarden-btn" type="button" data-toggle="tooltip" title="원아 추가 하기" onclick="addClassMember()" disabled>
                                        <i class="fa-solid fa-user"></i> DB저장
                                    </button>
                                    <button id="idPrintChildBtn" class="btn btn-success kgarden-btn" type="button" data-toggle="tooltip" title="원아 프린트 하기" onclick="printClassMember()" disabled>
                                        <i class="fa-solid fa-print"></i> 출력
                                    </button>
                                </div>
                            </div>
                            <div class="kgarden-table-wrap">
                                <div id="idTable"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <script>
        var tab;

        if (user == "chaitalk") {
            user = "admin";
        }

        // 유치원(10)·홈스쿨(30) 선생님 공용 — 서버: Server/student_policy.php (SIdStudents/SIdStudentMaxNo/SIdStudentSave)
        const IS_HOMESCHOOL = GP_ROLE.group(role) === 3;
        // [select value, 과정] — 서버 GP_ID_STUDENT_STEPS 와 같게 유지
        //   유치원 과정 sb/s1/s2 = 선생님 과정 코드 0/A/B (STEP_OPTIONS)
        const STEP_LIST = IS_HOMESCHOOL
            ? [['급파한', '급파한'], ['홈스쿨', '홈스쿨']]
            : [['sb', '잼잼별'], ['s1', '말잼별'], ['s2', '글잼별']];
        // 예전 유치원 과정 이름 — 목록에서 새 이름으로 보이고, 그 행을 수정해 저장하면 바뀐다
        const LEGACY_STEP = { '한자뚝0': '잼잼별', '한자뚝A': '말잼별', '한자뚝B': '글잼별' };
        const WORD = IS_HOMESCHOOL
            ? { student: '학생', owner: '선생님', obj: '학생을' }
            : { student: '원아', owner: '유치원', obj: '원아를' };

        const STEP_OPTIONS = {
            '0': ['sb'],
            'A': ['s1'],
            'B': ['s2'],
            '0A': ['sb', 's1'],
            '0B': ['sb', 's2'],
            'AB': ['s1', 's2'],
        };

        const stepSelect = document.getElementById('idStudent');
        stepSelect.replaceChildren(new Option('', 'v0'), ...STEP_LIST.map(([value, text]) => new Option(text, value)));
        if (!IS_HOMESCHOOL) applyStepOptions(clas);
        document.getElementById("idHead").textContent = `[ ${user} ] ${IS_HOMESCHOOL ? '홈스쿨 ' : ''}학생등록 관리리스트`;
        document.querySelector('label[for="idStudent"]').textContent = '과정 선택';
        document.querySelector('label[for="idNumstudent"]').textContent = `${WORD.student}수`;
        document.getElementById('idNumstudent').placeholder = `${WORD.student}수`;
        document.querySelector('button[onclick="addChild()"]').textContent = `${WORD.student}아이디생성`;

        window.addEventListener('resize', function() {
            if (tab && tab.setHeight) {
                tab.setHeight(getTableHeight());
            }
        });

        function applyStepOptions(classCode) {
            const select = document.getElementById('idStudent');
            const enabledValues = STEP_OPTIONS[classCode];

            if (!enabledValues) {
                Array.from(select.options).forEach(option => {
                    option.disabled = false;
                });
                return;
            }

            Array.from(select.options).forEach(option => {
                option.disabled = option.value !== 'v0' && !enabledValues.includes(option.value);
            });
        }

        function mgrError(e, fallback) {
            const message = (e && e.responseJSON && e.responseJSON.error) || fallback;
            if (e && e.status === 401) {
                alert(message);
                localStorage.removeItem('infochaitalk');
                window.location.href = '../index.php';
                return;
            }
            alert(message);
            CallToast(message, 'error');
        }

        function request(functionName, otherData) {
            return new Promise((resolve, reject) => CallAjax("SMethods.php", "POST", { functionName, otherData }, resolve, reject));
        }

        function drawTable() {
            const editorParams = {
                autocomplete: "true",
                allowEmpty: true,
                listOnEmpty: true,
                valuesLookup: true
            };

            const deleteIcon = function() {
                return "<i class='fa fa-trash'></i>";
            };

            tab = new Tabulator("#idTable", {
                height: getTableHeight(),
                layout: "fitColumns",
                selectable: true,
                columns: [{
                        title: "과정",
                        field: "step",
                        width: "10%",
                        minWidth: 100,
                        editor: "list",
                        headerHozAlign: "center",
                        editorParams: { values: STEP_LIST.map(([, text]) => text) }
                    },
                    {
                        title: WORD.owner,
                        field: "tid",
                        width: "10%",
                        minWidth: 120,
                        headerHozAlign: "center",
                        headerFilter: "input"
                    },
                    {
                        title: "반이름",
                        field: "classnm",
                        width: "10%",
                        minWidth: 120,
                        headerHozAlign: "center",
                        editor: "input",
                        editorParams: editorParams,
                        headerFilter: "input"
                    },
                    {
                        title: `${WORD.student}명`,
                        field: "name",
                        width: "20%",
                        minWidth: 140,
                        headerHozAlign: "center",
                        editor: "input",
                        editorParams: editorParams,
                        headerFilter: "input"
                    },
                    {
                        title: "아이디",
                        field: "id",
                        width: "20%",
                        minWidth: 150,
                        // 저장 전에만 아이디 변경 가능
                        editor: "input",
                        editable: (cell) => !cell.getRow().getData().saved,
                        headerHozAlign: "center",
                        headerFilter: "input"
                    },
                    {
                        title: "비밀번호",
                        field: "passwd",
                        width: "20%",
                        minWidth: 150,
                        editor: "input",
                        headerHozAlign: "center",
                        editorParams: editorParams
                    },
                    {
                        title: "삭제",
                        formatter: deleteIcon,
                        width: "10%",
                        minWidth: 80,
                        headerHozAlign: "center",
                        hozAlign: "center",
                        cellClick: function(e, cell) {
                            ChildDelete(cell.getRow())
                        }
                    },

                ],
                pagination: "local",
                paginationSize: 30,
                footerElement: "<div id='table-footer-count' style='font-weight: bold;'></div>",
            });
            tab.on("dataFiltered", (filters, rows) => updateRowCount(rows.length));
            // 수정한 행만 저장한다
            tab.on("cellEdited", (cell) => {
                if (cell.getValue() !== cell.getOldValue()) cell.getRow().update({ dirty: true });
            });
        }

        function getTableHeight() {
            return window.innerWidth <= 767 ? "420px" : "600px";
        }


        document.addEventListener("DOMContentLoaded", function() {
            drawTable();
            tab.on("tableBuilt", listChild);
        });

        // 본인에게 등록된 학생 전체 — 과정/반 선택은 표 필터로 처리
        function listChild() {
            stepSelect.value = 'v0';
            request('SIdStudents', {}).then((res) => {
                tab.clearFilter();
                return tab.setData((res.success || []).map(r => ({ ...r, step: LEGACY_STEP[r.step] || r.step, saved: true })));
            }).then(() => {
                const count = tab.getDataCount();
                updateRowCount(count);
                setResultButtons(count > 0);
                showClass();
            }).catch((e) => mgrError(e, `${WORD.student} 목록을 불러오지 못했습니다.`));
        }

        function ChildDelete(row) {
            const data = row.getData();
            if (!confirm(`${data.name || data.id} ${WORD.obj} 삭제할까요?`)) {
                return;
            }
            const done = () => {
                row.delete();
                updateRowCount(tab.getDataCount("active"));
                setResultButtons(tab.getDataCount() > 0);
            };
            // 저장 전 행은 화면에서만 지운다
            if (!data.saved) {
                done();
                return;
            }
            request('STeacherStudentDelete', { id: data.id }).then(() => {
                CallToast(`${data.id} 삭제되었습니다.`, "success");
                done();
                showClass();
            }).catch((e) => mgrError(e, '삭제에 실패했습니다.'));
        }

        stepSelect.addEventListener("change", function() {
            if (this.value === 'v0') tab.clearFilter();
            else tab.setFilter("step", "=", this.options[this.selectedIndex].text);
        });

        document.getElementById("idClass").addEventListener("change", function() {
            const classText = this.options[this.selectedIndex].text;
            if (classText === '') tab.clearFilter();
            else tab.setFilter("classnm", "=", classText);
            $("#idClassname").val(classText);
        });

        function updateRowCount(cnt) {
            document.getElementById("table-header").innerText = `총 학생 수: ${cnt} 명`;
            const footerCount = document.getElementById("table-footer-count");
            if (footerCount) {
                footerCount.innerText = `총 학생 수: ${cnt} 명`;
            }
        }
        function setResultButtons(isEnabled) {
            qi("idSaveChildBtn").disabled = !isEnabled;
            qi("idPrintChildBtn").disabled = !isEnabled;
        }

        const input = document.getElementById('idNick');
        const originalPlaceholder = input.placeholder;

        input.addEventListener('input', function(e) {
            const koreanPattern = /[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/;

            if (koreanPattern.test(e.target.value)) {
                e.target.value = '';
                e.target.placeholder = '한글은 입력할 수 없습니다';
                e.target.classList.add('warning');
            } else {
                if (e.target.classList.contains('warning')) {
                    e.target.placeholder = originalPlaceholder;
                    e.target.classList.remove('warning');
                }
            }
        });

        input.addEventListener('click', function(e) {
            e.target.placeholder = originalPlaceholder;
            e.target.classList.remove('warning');
        });

        async function addChild() {
            const selectedValue = stepSelect.value === 'v0' ? '' : stepSelect.options[stepSelect.selectedIndex].text;
            const classname = $("#idClassname").val().trim();
            const nickname = $("#idNick").val().trim();
            const num = $("#idNumstudent").val().trim();
            let start = Number($("#idStartNum").val()) || 1;

            if (!canCreateStudents(selectedValue, classname, nickname, num)) {
                showCreateStudentAlerts(selectedValue, classname, nickname, num);
                return;
            }
            if (!/^[A-Za-z]{1,20}$/.test(nickname)) {
                alert('시작아이디는 영문으로 입력해 주세요.');
                return;
            }
            if (!(Number(num) > 0 && Number(num) <= 200)) {
                alert(`${WORD.student}수는 1~200 사이로 입력해 주세요.`);
                return;
            }

            let used;
            try {
                used = (await request('SIdStudentMaxNo', { id: nickname })).result;
            } catch (e) {
                mgrError(e, '아이디 확인에 실패했습니다.');
                return;
            }
            if (used > 0 && start <= used) {
                if (!confirmDuplicateId(nickname, used + 1)) {
                    $("#idStartNum").val('');
                    return;
                }
                start = used + 1;
                $("#idStartNum").val(String(start));
            }

            tab.clearFilter();
            tab.clearData();
            Array.from({
                length: Number(num)
            }).forEach((_, index) => {
                const no = start + index;
                tab.addRow({
                    tid: user,
                    classnm: classname,
                    id: nickname + no,
                    passwd: nickname + no,
                    step: selectedValue,
                    saved: false
                });
            });
            updateRowCount(tab.getDataCount());
            setResultButtons(true);
        }

        function confirmDuplicateId(nickname, start) {
            const msg = `*사용자 ID(${nickname})가 이미 있습니다. '확인' 버턴을 누르면 (${nickname}${start})번 부터 ID가 자동 부여됩니다. \n` +
                `-신입학생의 추가생성인 경우에도 해당됩니다. \n` +
                ` *1번 부터 부여 하실려면 '취소' 버턴을 눌러 ID를 변경하신 후, 생성하세요. `;
            return confirm(msg);
        }

        function canCreateStudents(step, classname, nickname, num) {
            return step != "" && classname != "" && nickname != "" && num != "";
        }

        function showCreateStudentAlerts(step, classname, nickname, num) {
            if (step == "") {
                alert(` 생성 할 학생의 정보를 입력하세요/ 과정 [${STEP_LIST.map(([, text]) => text).join(', ')}] 선택해주세요 !!.`);
            }
            if (classname == "") {
                alert(" 생성 할 학생의 정보를 입력하세요/  반을 입력 입력해 주세요 !!.");
            }
            if (nickname == "") {
                alert(" 생성 할 학생의 정보를 입력하세요/ 생성할 ID를 입력해 주세요 !!.");
            }
            if (num == "") {
                alert(` 생성 할 학생의 정보를 입력하세요/ 생성할 ${WORD.student} 총 수를 입력해주세요 !!.`);
            }
        }

        // 새로 만든 행은 생성, 수정한 행은 수정 (서버가 본인 학생인지 확인)
        function addClassMember() {
            const rows = tab.getData().filter(r => !r.saved || r.dirty);
            if (rows.length === 0) {
                alert(`새로 만들거나 수정한 ${WORD.student} 정보가 없습니다.`);
                return;
            }

            for (let i = 0; i < rows.length; i++) {
                const missingFields = [];
                if (!String(rows[i].name || '').trim()) missingFields.push(`${WORD.student}명`);
                if (!String(rows[i].id || '').trim()) missingFields.push('아이디');
                if (!String(rows[i].passwd || '').trim()) missingFields.push('비밀번호');
                if (!String(rows[i].step || '').trim()) missingFields.push('과정');
                if (missingFields.length > 0) {
                    const msg = `${rows[i].id || (i + 1) + '번째 행'}: ${missingFields.join(', ')}을(를) 입력해주세요.`;
                    alert(msg);
                    CallToast(msg, "error");
                    return;
                }
            }

            const items = rows.map(r => ({
                id: String(r.id).trim(),
                name: String(r.name).trim(),
                passwd: String(r.passwd).trim(),
                classnm: String(r.classnm || '').trim(),
                step: r.step,
            }));
            request('SIdStudentSave', { items }).then((resp) => {
                CallToast(`저장되었습니다. (생성 ${resp.created} / 수정 ${resp.updated})`, "success");
                listChild();
            }).catch((e) => mgrError(e, '저장에 실패했습니다.'));
        }

        // 반선택 목록 — 본인 학생의 반이름
        function showClass() {
            const select = document.getElementById('idClass');
            const names = [...new Set(tab.getData().map(r => r.classnm).filter(Boolean))].sort();
            select.replaceChildren(new Option('', ''), ...names.map(n => new Option(n, n)));
        }

        var page;

        const {
            PDFDocument,
            rgb
        } = PDFLib;
        const black = rgb(0, 0, 0);
        const white = rgb(1, 1, 1);


        async function printClassMember() {
            const pdfDoc = await PDFDocument.create();
            pdfDoc.registerFontkit(fontkit)
            const fontBytes = await fetch('NanumBarunGothic.ttf').then((res) => res
                .arrayBuffer());
            const customFont = await pdfDoc.embedFont(fontBytes);
            font = customFont;

            var item = tab.getData();
            var Arr = [];
            item.forEach(el => {

                var jarr = {
                    "step": el['step'],
                    "classnm": el['classnm'],
                    "name": el['name'],
                    "id": el['id'],
                    "passwd": el['passwd']
                }
                Arr.push(jarr)
            })

            let loop = 1;

            if (Arr.length > 24)
                loop = 2;
            let m = 0;
            for (var j = 0; j < loop; j++) {

                page = pdfDoc.addPage()

                const {
                    width,
                    height
                } = page.getSize()

                const fontSize = 14;

                page.setFont(customFont);
                page.setFontSize(fontSize);

                setOrigin(0.5, 27);
                pwidth = width / cm;
                half = pwidth / 2;
                let xm = 3.25;
                let ym = 1.5;
                let hcol = rgb(0.37, 0.66, 0.62); // header color
                let tcol = rgb(1.00, 0.97, 0.82); // row color

                let fs = 10;

                for (let k = 1; k < 9; k++) {
                    for (let i = 0; i < 3; i++) {
                        // if (m >= Arr.length) {
                        //     m = 0;
                        // }
                        if (m < Arr.length) {
                            drawRTextBox(0, 0, xm, ym, hcol, Arr[m]['classnm'], fs, white, "center");
                            drawRTextBox(xm, 0, xm, ym, hcol, Arr[m]['name'], fs, white, "center");

                            moveDown(ym)
                            drawRTextBox(0, 0, xm, ym, tcol, "ID: " + Arr[m]['id'], fs, black, "center");
                            drawRTextBox(xm, 0, xm, ym, tcol, "PW: " + Arr[m]['passwd'], fs, black, "center");
                            moveUp(ym)
                            moveRight(6.8);
                        }
                        m++;
                    }
                    moveLeft(6.8 * 3);
                    moveDown(ym * 2 + 0.5);
                }
            }

            const pdfBytes = await pdfDoc.save()
            const pdfBlob = new Blob([pdfBytes], {
                type: 'application/pdf'
            });

            // 생성된 PDF 다운로드
            const downloadLink = document.createElement('a');
            downloadLink.href = window.URL.createObjectURL(pdfBlob);
            downloadLink.download = 'output.pdf';
            downloadLink.click();

        }

    </script>
</body>

</html>
