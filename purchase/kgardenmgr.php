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
</head>

<body class="kgarden-page">
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
                                    <label for="idStudent">Step 선택</label>
                                    <select class="form-select form-control-sm" id="idStudent" data-placeholder="Choose Items">
                                        <option value="v0"></option>
                                        <!-- <option val="va">전체</option> -->
                                        <option value="sb">한자뚝0</option>
                                        <option value="s1">한자뚝A</option>
                                        <option value="s2">한자뚝B</option>
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
                                    <button id="idSaveChildBtn" class="btn btn-outline-primary kgarden-btn" type="button" data-toggle="tooltip" title="원아 추가 하기" onclick="addClassMember(1)" disabled>
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

        const STEP_OPTIONS = {
            '0': ['sb'],
            'A': ['s1'],
            'B': ['s2'],
            '0A': ['sb', 's1'],
            '0B': ['sb', 's2'],
            'AB': ['s1', 's2'],
        };

        applyStepOptions(clas);
        document.getElementById("idHead").innerHTML = "[ " + user + " ]" + " 학생등록 관리리스트";

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
                        title: "스텝",
                        field: "step",
                        width: "10%",
                        minWidth: 100,
                        editor: "input",
                        headerHozAlign: "center",
                        editorParams: editorParams
                    },
                    {
                        title: "유치원",
                        field: "tid",
                        width: "10%",
                        minWidth: 120,
                        headerHozAlign: "center",
                        editor: "input",
                        editorParams: editorParams,
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
                        title: "원아명",
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
                        editor: false,
                        headerHozAlign: "center",
                        editorParams: editorParams,
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
        }

        function getTableHeight() {
            return window.innerWidth <= 767 ? "420px" : "600px";
        }


        document.addEventListener("DOMContentLoaded", function() {
            drawTable();
            showClass(user);
        });

        function listChild() {
            listStudent("전체", 1);
        }

        function ChildDelete(cell) {
            const result = confirm("Are you sure to delete ??");
            const id = cell._row.data['id'];
            const dispList = () => {
                CallToast('원아 삭제 successfully!!', "success")
                cell.delete();
            };
            const dispErr = () => {
                CallToast('원아 삭제 Error!!', "error")
            };

            const options = {
                functionName: 'SRemoveChild',
                otherData: {
                    id: id
                }
            };

            if (result) {
                CallAjax("SMethods.php", "POST", options, dispList, dispErr);
            } else {
                CallToast("원아 삭제 취소 !!", "error");
            }
        }

        document.getElementById("idStudent").addEventListener("change", function() {
            listStudent(this.options[this.selectedIndex].text, 1);
        });

        document.getElementById("idClass").addEventListener("change", function() {
            const classText = this.options[this.selectedIndex].text;

            listStudent(classText, 2);
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

        function listStudent(step, classText) {
            const dispList = (res) => {
                tab.setData(res['json']);
                const count = tab.getDataCount();
                updateRowCount(count);
                setResultButtons(count > 0);
                CallToast('Student list successfully!!', "success")
            };
            const dispErr = () => {
                CallToast('Student list Error!!', "error")
            };

            const options = {
                functionName: 'SShowStudentList',
                otherData: {
                    id: user,
                    step: step,
                    sel: classText,
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);

        }

        function checkExistID(id) {
            let js;
            const dispList = (res) => {
                js = res['result'];
            };
            const dispErr = () => {
                CallToast('Student list Error!!', "error")
            };
            const options = {
                functionName: 'SCheckStudentID',
                otherData: {
                    id: id,
                }
            };
            CallAjax2("SMethods.php", "POST", options, dispList, dispErr);
            return js;
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

        function addChild() {
            tab.clearData();

            const select = document.getElementById('idStudent');
            const selectedValue = select.options[select.selectedIndex].text;
            const classname = $("#idClassname").val();
            const nickname = $("#idNick").val();
            const num = $("#idNumstudent").val();
            let start = Number($("#idStartNum").val());

            const isexist = checkExistID(nickname);
            if (isexist > 0) {
                start = Number(isexist) + 1;
                $("#idStartNum").val((start.toString()));
            }

            if (isexist > 0 && !confirmDuplicateId(nickname, start)) {
                alert(`${nickname} 은 다른 원에서 사용중입니다!  다른 아이디를 입력해서 원아 아이디를 생성해주세요`);
                $("#idStartNum").val('');
                return;
            }

            if (!canCreateStudents(selectedValue, classname, nickname, num)) {
                showCreateStudentAlerts(selectedValue, classname, nickname, num);
                return;
            }

            Array.from({
                length: Number(num)
            }).forEach((_, index) => {
                const no = start + index;
                tab.addRow({
                    tid: user,
                    classnm: classname,
                    id: nickname + no,
                    passwd: nickname + no,
                    step: selectedValue
                });
            });
        }

        function confirmDuplicateId(nickname, start) {
            const msg = `*사용자 ID(${nickname})가 이미 있습니다. '확인' 버턴을 누르면 (${nickname}${start})번 부터 ID가 자동 부여됩니다. \n` +
                `-신입학생의 추가생성인 경우에도 해당됩니다. \n` +
                ` *1번 부터 부여 하실려면 '취소' 버턴을 눌러 ID를 변경하신 후, 생성하세요. `;
            return confirm(msg);
        }

        function canCreateStudents(step, classname, nickname, num) {
            return step != "전체" && step != "" && classname != "" && nickname != "" && num != "";
        }

        function showCreateStudentAlerts(step, classname, nickname, num) {
            if (step == "") {
                alert(" 생성 할 학생의 정보를 입력하세요/ Step [4세-Basic, 5세-Step1, 6세-Step2, 7세-Step3]  선택해주세요 !!.");
            }
            if (classname == "") {
                alert(" 생성 할 학생의 정보를 입력하세요/  반을 입력 입력해 주세요 !!.");
            }
            if (nickname == "") {
                alert(" 생성 할 학생의 정보를 입력하세요/ 생성할 ID를 입력해 주세요 !!.");
            }
            if (num == "") {
                alert(" 생성 할 학생의 정보를 입력하세요/ 생성할 원아 총 수를 입력해주세요 !!.");
            }
        }

        function addClassMember(chos) {
            const fun = chos == 2 ? "SupdateStudent" : "SinsertStudent";
            const item = tab.getRows();
            const items = [];

            if (item.length === 0) {
                alert("저장할 원아 정보를 먼저 입력해주세요.");
                CallToast("저장할 원아 정보를 먼저 입력해주세요.", "error");
                return;
            }

            for (let i = 0; i < item.length; i++) {
                const rowData = item[i].getData ? item[i].getData() : item[i]._row.data;
                const missingFields = [];

                if (!String(rowData['name'] || '').trim()) missingFields.push('원아명');
                if (!String(rowData['id'] || '').trim()) missingFields.push('아이디');
                if (!String(rowData['passwd'] || '').trim()) missingFields.push('비밀번호');

                if (missingFields.length > 0) {
                    const msg = `${i + 1}번째 행의 ${missingFields.join(', ')}을(를) 입력해주세요.`;
                    alert(msg);
                    CallToast(msg, "error");
                    return;
                }

                items.push({
                    "id": rowData['id'],
                    "name": rowData['name'],
                    "passwd": rowData['passwd'],
                    "tid": user,
                    "role": "0",
                    "classnm": rowData['classnm'],
                    "step": rowData['step'],
                    "p_mobile": '',
                    "mobile": '',
                    "addr": '',
                    "status": '1',
                    "etc": '',
                });
            }

            const dispList = () => {
                showClass(user);
                if (chos == 1) {
                    CallToast('New Student added successfully!!', "success")
                } else {
                    CallToast('New Student Update successfully!!', "success")
                }
            };
            const dispErr = () => {
                if (chos == 1) {
                    alert(' 아이디가 이미 사용중입니다.')
                    CallToast('아이디가 이미 사용중입니다!!', "error")
                } else {
                    CallToast('New Student  Update falure !!', "error")
                }
            };

            const options = {
                functionName: fun,
                otherData: {
                    items
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);

        }

        function showClass(tid) {
            const dispList = (resp) => {
                $("#idClass").empty();
                const select = document.getElementById('idClass');
                const option = document.createElement('option');
                option.text = "";
                option.value = "";
                select.add(option);

                resp['success'].forEach(el => {
                    const option = document.createElement('option');
                    option.text = el['classnm'];
                    option.value = el['classnm'];
                    select.add(option);
                })
            };

            const dispErr = () => {};

            const options = {
                functionName: 'SShowClassList',
                otherData: {
                    id: tid,
                    kgarden: ""
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);
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
