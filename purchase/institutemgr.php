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
    <script src="../js/common.js"></script>
    <?php
    include '../libs/includescr.php';
    ?>
    <script src="../js/header.js"></script>
    <title>학생관리</title>
    <link rel="stylesheet" href="../role-nav.css">
    <script src="../js/roles.js?v=<?= filemtime(__DIR__ . '/../js/roles.js') ?>"></script>
    <script src="../js/role-nav.js" defer></script>
    <style>
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
        }

        .kgarden-card {
            border: 1px solid var(--kg-line);
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            background: var(--kg-panel);
        }

        .kgarden-toolbar {
            background: linear-gradient(135deg, #ffffff 0%, #eef6ff 100%);
            border-bottom: 1px solid var(--kg-line);
            padding: 18px;
        }

        /* 소속(학원/지사)은 맨 앞에 */
        .affiliation {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .affiliation span {
            padding: 6px 12px;
            border-radius: 999px;
            background: #1e3a8a;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
        }

        .affiliation span.sub {
            background: #dbeafe;
            color: #1e3a8a;
        }

        .kgarden-form {
            display: grid;
            /* 학생명 연락처 학교명 단계 수업시작일 수업종료일 학습상태 [등록] */
            grid-template-columns: 130px 140px minmax(120px, 1fr) 190px 150px 150px 90px 72px;
            gap: 8px;
            align-items: end;
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
            padding: 7px 8px;
            font-size: 13px;
            background-color: #fff;
        }

        .kgarden-btn {
            height: 38px;
            border-radius: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .kgarden-body {
            padding: 18px;
        }

        .kgarden-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        #table-header {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: 8px 12px;
            border: 1px solid var(--kg-line);
            border-radius: 10px;
            background: #f8fafc;
            font-weight: 800;
        }

        .kgarden-table-wrap {
            overflow-x: auto;
            border: 1px solid var(--kg-line);
            border-radius: 12px;
        }

        .kgarden-table-wrap .tabulator {
            border: 0;
            font-size: 14px;
        }

        .kgarden-table-wrap .tabulator-row.row-dirty {
            background: #fef9c3 !important;
        }

        .status-on,
        .status-off {
            display: inline-block;
            min-width: 48px;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }

        .status-on {
            background: #dcfce7;
            color: #166534;
        }

        .status-off {
            background: #fee2e2;
            color: #991b1b;
        }

        .step-cell {
            color: var(--kg-primary);
            font-weight: 700;
            cursor: pointer;
        }

        .student-delete-btn {
            padding: 4px 10px;
            border: 0;
            border-radius: 7px;
            background: #dc3545;
            color: #fff;
            font-size: 12px;
            font-weight: 800;
        }

        @media (max-width: 1199px) {
            .kgarden-form {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .kgarden-shell {
                padding: 12px;
            }

            .kgarden-form {
                grid-template-columns: 1fr;
            }

            .kgarden-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>

<body class="kgarden-page">
    <header id="roleNav"></header>

    <div class="kgarden-shell">
        <section class="kgarden-heading mb-3">
            <div class="row">
                <div class="col-sm-6">
                    <h5 id="idHead">학생관리</h5>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb justify-content-end">
                        <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:window.history.back();">이전</a></li>
                    </ol>
                </div>
            </div>
        </section>
        <section>
            <div class="kgarden-card">
                <div class="kgarden-toolbar">
                    <div class="affiliation" id="idAffiliation"></div>
                    <div class="kgarden-form">
                        <div class="kgarden-field">
                            <label for="idName">학생명</label>
                            <input id="idName" type="text" placeholder="학생명" maxlength="20">
                        </div>
                        <div class="kgarden-field">
                            <label for="idMobile">연락처</label>
                            <input id="idMobile" type="text" placeholder="010-0000-0000" maxlength="20">
                        </div>
                        <div class="kgarden-field">
                            <label for="idSchool">학교명</label>
                            <input id="idSchool" type="text" placeholder="학교명" maxlength="45">
                        </div>
                        <div class="kgarden-field">
                            <label for="idStep">과정 / 단계</label>
                            <select id="idStep"></select>
                        </div>
                        <div class="kgarden-field">
                            <label for="idSdate">수업시작일</label>
                            <input id="idSdate" type="date">
                        </div>
                        <div class="kgarden-field">
                            <label for="idEdate">수업종료일</label>
                            <input id="idEdate" type="date">
                        </div>
                        <div class="kgarden-field">
                            <label for="idStatus">학습상태</label>
                            <select id="idStatus">
                                <option value="1" selected>등원</option>
                                <option value="0">퇴원</option>
                            </select>
                        </div>
                        <button class="btn btn-success kgarden-btn" type="button" onclick="addStudent()">등록</button>
                    </div>
                </div>
                <div class="kgarden-body">
                    <div class="kgarden-actions">
                        <div id="table-header">총 학생 수: 0명</div>
                        <button id="idSaveBtn" class="btn btn-primary kgarden-btn" type="button" onclick="saveStudents()" disabled>
                            <i class="fa-solid fa-floppy-disk"></i> 변경사항 저장
                        </button>
                    </div>
                    <div class="kgarden-table-wrap">
                        <div id="idTable"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // 과정 => 교재 [{code, title, step}] — 서버가 선생님 그룹 가격표(Server/price_catalog.json)에서 내려준다
        let COURSES = {};
        let ITEMS = {};   // 교재 코드 => {family, title}
        const courseOf = (code) => GP_ROLE.courseLabel((ITEMS[code] || {}).family);
        const titleOf = (code) => (ITEMS[code] || {}).title || code || '';

        function setCourses(courses) {
            COURSES = courses || {};
            ITEMS = {};
            Object.entries(COURSES).forEach(([family, items]) => items.forEach(it => ITEMS[it.code] = { family, title: it.title }));
        }
        const STATUS = { 1: '등원', 0: '퇴원' };

        var table;
        var affiliation = '';
        const dirtyIds = new Set();

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

        // 단계 option 메뉴 — 교재군(optgroup) / 교재(title), 값은 교재 코드
        const stepGroups = () => Object.entries(COURSES).map(([family, items]) => ({
            label: GP_ROLE.courseLabel(family),
            options: items.map(it => ({ label: it.title, value: it.code })),
        }));

        function fillStepSelect(select) {
            const keep = select.value;
            select.replaceChildren(new Option('단계 선택', ''), ...stepGroups().map(group => {
                const g = document.createElement('optgroup');
                g.label = group.label;
                group.options.forEach(o => g.appendChild(new Option(o.label, o.value)));
                return g;
            }));
            select.value = ITEMS[keep] ? keep : '';
        }

        function markDirty(row) {
            dirtyIds.add(row.getData().id);
            row.reformat();
            const btn = document.getElementById('idSaveBtn');
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-floppy-disk"></i> 변경사항 저장 (${dirtyIds.size})`;
        }

        function updateRowCount() {
            const data = table.getData();
            const on = data.filter(r => Number(r.status) === 1).length;
            document.getElementById('table-header').textContent = `총 학생 수: ${data.length}명 (등원 ${on} / 퇴원 ${data.length - on})`;
        }

        function showAffiliation(resp) {
            const chips = [];
            if (resp.branch) chips.push(['', `지사 : ${resp.branch}`]);
            if (resp.academy) chips.push(['', `학원 : ${resp.academy}`]);
            chips.push(['sub', `교사 : ${resp.teacher || user}`]);
            document.getElementById('idAffiliation').replaceChildren(...chips.map(([cls, text]) => {
                const s = document.createElement('span');
                s.className = cls;
                s.textContent = text;
                return s;
            }));
            affiliation = [resp.branch, resp.academy].filter(Boolean).join(' · ');
            document.getElementById('idHead').textContent = `[${affiliation || user}] 학생관리`;
        }

        function listStudents() {
            CallAjax("SMethods.php", "POST", { functionName: 'STeacherStudents', otherData: {} }, (resp) => {
                showAffiliation(resp);
                setCourses(resp.courses);
                fillStepSelect(document.getElementById('idStep'));
                dirtyIds.clear();
                const btn = document.getElementById('idSaveBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> 변경사항 저장';
                table.setData((resp.success || []).map(r => ({
                    ...r,
                    status: Number(r.status),
                    course: courseOf(r.step),
                    sdate: r.sdate || '',
                    edate: r.edate || '',
                    rdate: String(r.rdate || '').slice(0, 10),
                }))).then(updateRowCount);
            }, (e) => mgrError(e, '학생 목록을 불러오지 못했습니다.'));
        }

        function studentItem(r) {
            return {
                id: r.id || '',
                name: (r.name || '').trim(),
                mobile: (r.mobile || '').trim(),
                classnm: (r.classnm || '').trim(),
                step: r.step || '',
                sdate: r.sdate || '',
                edate: r.edate || '',
                status: Number(r.status) === 0 ? 0 : 1,
            };
        }

        function checkItem(item) {
            if (!item.name) return '학생명을 입력해 주세요.';
            if (!item.step) return `${item.name}: 단계를 선택해 주세요.`;
            if (item.sdate && item.edate && item.edate < item.sdate) return `${item.name}: 수업종료일이 수업시작일보다 빠릅니다.`;
            return '';
        }

        // 아이디/암호는 서버가 무작위로 만들고 로그인할 수 없게 저장한다
        function addStudent() {
            const item = studentItem({
                name: $('#idName').val(),
                mobile: $('#idMobile').val(),
                classnm: $('#idSchool').val(),
                step: $('#idStep').val(),
                sdate: $('#idSdate').val(),
                edate: $('#idEdate').val(),
                status: $('#idStatus').val(),
            });
            const error = checkItem(item);
            if (error) {
                alert(error);
                return;
            }
            CallAjax("SMethods.php", "POST", { functionName: 'STeacherStudentSave', otherData: { items: [item] } }, () => {
                CallToast(`${item.name} 학생이 등록되었습니다.`, 'success');
                // 같은 단계·기간 학생을 이어서 입력하기 쉽도록 단계/날짜/상태는 남긴다
                $('#idName, #idMobile, #idSchool').val('');
                $('#idName').focus();
                listStudents();
            }, (e) => mgrError(e, '등록에 실패했습니다.'));
        }

        function saveStudents() {
            const items = table.getData().filter(r => dirtyIds.has(r.id)).map(studentItem);
            const error = items.map(checkItem).find(Boolean);
            if (error) {
                alert(error);
                return;
            }
            CallAjax("SMethods.php", "POST", { functionName: 'STeacherStudentSave', otherData: { items } }, (resp) => {
                CallToast(`${resp.updated}명 수정되었습니다.`, 'success');
                listStudents();
            }, (e) => mgrError(e, '저장에 실패했습니다.'));
        }

        function deleteStudent(row) {
            const data = row.getData();
            if (!confirm(`${data.name} 학생을 삭제할까요?`)) return;
            CallAjax("SMethods.php", "POST", { functionName: 'STeacherStudentDelete', otherData: { id: data.id } }, () => {
                dirtyIds.delete(data.id);
                row.delete();
                updateRowCount();
                CallToast('삭제되었습니다.', 'success');
            }, (e) => mgrError(e, '삭제에 실패했습니다.'));
        }

        table = new Tabulator("#idTable", {
            height: "640px",
            layout: "fitColumns",
            placeholder: "등록된 학생이 없습니다.",
            index: "id",
            rowFormatter: function(row) {
                row.getElement().classList.toggle('row-dirty', dirtyIds.has(row.getData().id));
            },
            columns: [
                { title: "No", formatter: "rownum", hozAlign: "center", width: 60, headerSort: false },
                { title: "소속", width: 160, formatter: () => affiliation, headerSort: false },
                {
                    title: "과정", field: "course", width: 110, hozAlign: "center",
                    headerFilter: "list",
                    headerFilterParams: { valuesLookup: true, clearable: true },
                },
                {
                    title: "단계", field: "step", width: 150, hozAlign: "center",
                    formatter: (cell) => {
                        const span = document.createElement('span');
                        span.className = 'step-cell';
                        span.textContent = titleOf(cell.getValue()) || '선택';
                        return span;
                    },
                    editor: "list",
                    editorParams: () => ({ values: stepGroups() }),
                },
                { title: "학생명", field: "name", minWidth: 100, editor: "input", headerFilter: "input" },
                { title: "연락처", field: "mobile", minWidth: 120, editor: "input" },
                { title: "학교명", field: "classnm", minWidth: 120, editor: "input", headerFilter: "input" },
                { title: "수업시작일", field: "sdate", width: 130, hozAlign: "center", editor: "date" },
                { title: "수업종료일", field: "edate", width: 130, hozAlign: "center", editor: "date" },
                {
                    title: "학습상태", field: "status", width: 100, hozAlign: "center",
                    editor: "list",
                    editorParams: { values: [{ label: "등원", value: 1 }, { label: "퇴원", value: 0 }] },
                    formatter: (cell) => `<span class="${cell.getValue() === 1 ? 'status-on' : 'status-off'}">${STATUS[cell.getValue()] || ''}</span>`,
                    headerFilter: "list",
                    headerFilterParams: { values: [{ label: '전체', value: '' }, { label: '등원', value: '1' }, { label: '퇴원', value: '0' }] },
                    headerFilterFunc: (value, status) => value === '' || Number(value) === status,
                },
                { title: "등록일", field: "rdate", width: 110, hozAlign: "center" },
                {
                    title: "삭제", width: 72, hozAlign: "center", headerSort: false,
                    formatter: () => "<button type='button' class='student-delete-btn'>삭제</button>",
                    cellClick: (e, cell) => deleteStudent(cell.getRow()),
                },
            ],
        });

        table.on("cellEdited", (cell) => {
            if (cell.getValue() !== cell.getOldValue()) {
                if (cell.getField() === 'step') cell.getRow().update({ course: courseOf(cell.getValue()) });
                markDirty(cell.getRow());
                if (cell.getField() === 'status') updateRowCount();
            }
        });

        if (GP_ROLE.isTeacher(role)) {
            table.on("tableBuilt", listStudents);
        } else {
            CallToast("교사 계정으로 로그인하세요", "error");
            document.querySelectorAll('.kgarden-toolbar input, .kgarden-toolbar select, .kgarden-toolbar button').forEach(el => el.disabled = true);
        }

        window.addEventListener('beforeunload', (e) => {
            if (dirtyIds.size > 0) e.preventDefault();
        });
    </script>
</body>

</html>
