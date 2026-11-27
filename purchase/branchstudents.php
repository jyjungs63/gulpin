<?php
include "../libs/header.php";
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../libs/include.php'; ?>
    <script src="../js/common.js"></script>
    <?php include '../libs/includescr.php'; ?>
    <script src="../js/header.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
    <title>학생관리</title>
    <link rel="stylesheet" href="../role-nav.css">
    <script src="../js/roles.js?v=<?= filemtime(__DIR__ . '/../js/roles.js') ?>"></script>
    <script src="../js/role-nav.js" defer></script>
    <style>
        body.kgarden-page { background: #f3f6fb !important; color: #1f2937; }
        .kgarden-shell { max-width: 1560px; margin: 0 auto; padding: 18px; }
        .kgarden-heading h5 { margin: 0; font-size: 20px; font-weight: 800; color: #111827; }
        .kgarden-heading p { margin: 4px 0 0; color: #6b7280; font-size: 13px; }
        .kgarden-card { margin-top: 14px; padding: 18px; border: 1px solid #d9e2ef; border-radius: 16px; background: #fff; box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08); }
        #table-header { display: inline-flex; margin-bottom: 14px; padding: 8px 12px; border: 1px solid #d9e2ef; border-radius: 10px; background: #f8fafc; font-weight: 800; }
        .kgarden-table-wrap { overflow-x: auto; border: 1px solid #d9e2ef; border-radius: 12px; }
        .kgarden-table-wrap .tabulator { border: 0; font-size: 14px; }
        #studentDialog { width: min(540px, 92vw); border: 0; border-radius: 14px; padding: 24px; }
        #studentDialog::backdrop { background: #0007; }
        #studentForm { display: grid; gap: 12px; }
        #studentForm label { display: grid; gap: 5px; margin: 0; }
        #studentForm input, #studentForm select { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .student-actions { display: flex; gap: 10px; justify-content: flex-end; }
        #studentError { color: #b91c1c; margin: 0; }
        @media (max-width: 767px) { .kgarden-shell { padding: 12px; } }
    </style>
</head>

<body class="kgarden-page">
    <header id="roleNav"></header>

    <div class="kgarden-shell">
        <section class="kgarden-heading">
            <h5 id="idHead">학생관리</h5>
            <p>수정 버튼으로 담당교사와 학생 정보를 관리할 수 있습니다. 신규 학생 등록은 각 교사의 학생관리 화면에서 합니다.</p>
        </section>
        <section class="kgarden-card">
            <div id="table-header">총 학생 수: 0명</div>
            <div class="kgarden-table-wrap">
                <div id="idTable"></div>
            </div>
        </section>
    </div>

    <dialog id="studentDialog" aria-labelledby="studentTitle">
        <form id="studentForm">
            <h5 id="studentTitle">학생 정보 수정</h5>
            <label>아이디<input name="id" readonly></label>
            <label>담당교사<select name="tid"></select></label>
            <label>이름<input name="name" maxlength="20" required></label>
            <label>학교/반<input name="classnm" maxlength="45"></label>
            <label>생년월일<input name="birthday" type="date" min="1900-01-01"></label>
            <label>연락처<input name="mobile" type="tel" maxlength="20"></label>
            <label>단계<input name="step" maxlength="20"></label>
            <label>상태<select name="status"><option value="1">재원</option><option value="0">퇴원</option></select></label>
            <p id="studentError" role="alert"></p>
            <div class="student-actions">
                <button type="button" class="btn btn-secondary" id="studentCancel">취소</button>
                <button type="submit" class="btn btn-primary" id="studentSave">저장</button>
            </div>
        </form>
    </dialog>
    <script>
        // 본사: 전체 학생, 지사: 본인이 등록한 선생의 학생 (서버 SShowBranchStudents 가 범위 결정)
        document.getElementById("idHead").textContent = `[${user}] 학생관리`;
        const escapeHtml = (value) => String(value ?? "").replace(/[&<>'"]/g, (ch) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", "'": "&#39;", '"': "&quot;" }[ch]));

        let teachers = [];
        const dialog = document.getElementById('studentDialog');
        const form = document.getElementById('studentForm');
        const saveButton = document.getElementById('studentSave');
        const cancelButton = document.getElementById('studentCancel');
        const errorBox = document.getElementById('studentError');
        form.elements.birthday.max = new Date().toLocaleDateString('sv-SE');
        cancelButton.addEventListener('click', () => dialog.close());
        dialog.addEventListener('cancel', (event) => { if (saveButton.disabled) event.preventDefault(); });

        function editStudent(row) {
            const data = row.getData();
            errorBox.textContent = '';
            for (const key of ['id', 'name', 'classnm', 'birthday', 'mobile', 'step', 'status']) {
                form.elements[key].value = data[key] ?? '';
            }
            form.elements.step.value = GP_ROLE.courseLabel(data.step);
            form.dataset.step = data.step ?? '';
            const choices = teachers.filter((teacher) => Number(teacher.role) * 10 === Number(data.role));
            if (!choices.some((teacher) => teacher.id === data.tid)) {
                choices.unshift({ id: data.tid || '', name: data.teacher || '미지정', owner: data.owner });
            }
            form.elements.tid.replaceChildren(...choices.map((teacher) => {
                const option = document.createElement('option');
                option.value = teacher.id;
                option.textContent = `${teacher.owner || ''} / ${teacher.name || teacher.id} (${teacher.id})`;
                return option;
            }));
            form.elements.tid.value = data.tid || '';
            dialog.showModal();
        }

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (saveButton.disabled) return;
            saveButton.disabled = cancelButton.disabled = true;
            errorBox.textContent = '';
            const otherData = Object.fromEntries(new FormData(form));
            if (otherData.step === GP_ROLE.courseLabel(form.dataset.step)) otherData.step = form.dataset.step;   // 안 고쳤으면 저장 값 그대로
            CallAjax('SMethods.php', 'POST', { functionName: 'SSaveBranchStudent', otherData }, () => {
                saveButton.disabled = cancelButton.disabled = false;
                dialog.close();
                CallToast('학생 정보가 저장되었습니다.', 'success');
                loadStudents();
            }, (error) => {
                saveButton.disabled = cancelButton.disabled = false;
                errorBox.textContent = error?.responseJSON?.error || '저장에 실패했습니다.';
            });
        });

        const table = new Tabulator("#idTable", {
            height: "700px",
            layout: "fitColumns",
            placeholder: "등록된 학생이 없습니다.",
            columns: [
                { title: "소속", field: "owner", headerFilter: "input", minWidth: 120, formatter: (cell) => escapeHtml(cell.getValue()) },
                { title: "담당교사", field: "teacher", headerFilter: "input", minWidth: 150,
                  formatter: (cell) => {
                      const row = cell.getRow().getData();
                      return escapeHtml(row.teacher ? `${row.teacher} (${row.tid})` : row.tid);
                  } },
                { title: "구분", field: "role", width: 110, formatter: (cell) => GP_ROLE.name(cell.getValue()) },
                { title: "아이디", field: "id", headerFilter: "input", minWidth: 110, formatter: (cell) => escapeHtml(cell.getValue()) },
                { title: "이름", field: "name", headerFilter: "input", minWidth: 100, formatter: (cell) => escapeHtml(cell.getValue()) },
                { title: "생년월일", field: "birthday", headerFilter: "input", minWidth: 125, formatter: (cell) => escapeHtml(cell.getValue()) },
                { title: "연락처", field: "mobile", headerFilter: "input", minWidth: 135, formatter: (cell) => escapeHtml(cell.getValue()) },
                { title: "학교/반", field: "classnm", headerFilter: "input", minWidth: 110, formatter: (cell) => escapeHtml(cell.getValue()) },
                { title: "단계", field: "step", width: 90, formatter: (cell) => escapeHtml(GP_ROLE.courseLabel(cell.getValue())) },
                { title: "상태", field: "status", width: 80, formatter: (cell) => Number(cell.getValue()) === 1 ? "재원" : "퇴원" },
                { title: "등록일", field: "rdate", width: 110 },
                { title: "수정", width: 75, headerSort: false, formatter: () => '<button type="button" class="btn btn-sm btn-outline-primary">수정</button>',
                  cellClick: (e, cell) => editStudent(cell.getRow()) },
                { title: "삭제", width: 70, hozAlign: "center", headerHozAlign: "center", headerSort: false,
                  formatter: () => "<i class='fa fa-trash' style='color:#dc2626; cursor:pointer'></i>",
                  cellClick: (e, cell) => deleteStudent(cell.getRow()) },
            ],
        });

        const showCount = () => {
            document.getElementById("table-header").textContent = `총 학생 수: ${table.getDataCount()}명`;
        };

        // 삭제 범위는 서버(SDeleteBranchStudent)가 목록과 같은 기준으로 다시 확인
        function deleteStudent(row) {
            const data = row.getData();
            if (!confirm(`${data.name || data.id} (${data.id}) 학생을 삭제할까요?\n삭제하면 되돌릴 수 없습니다.`)) {
                return;
            }
            CallAjax("SMethods.php", "POST", { functionName: "SDeleteBranchStudent", otherData: { id: data.id } }, () => {
                row.delete();
                showCount();
                CallToast(`${data.id} 삭제되었습니다.`, "success");
            }, (e) => {
                CallToast((e && e.responseJSON && e.responseJSON.error) || "삭제에 실패했습니다.", "error");
            });
        }

        function loadStudents() {
            CallAjax("SMethods.php", "POST", { functionName: "SShowBranchStudents", otherData: {} }, (resp) => {
                const rows = resp.success || [];
                teachers = resp.teachers || [];
                table.setData(rows);
                document.getElementById("table-header").textContent = `총 학생 수: ${rows.length}명`;
            }, (e) => {
                const message = (e && e.responseJSON && e.responseJSON.error) || "학생 목록을 불러오지 못했습니다.";
                if (e && e.status === 401) {
                    alert(message);
                    localStorage.removeItem("infochaitalk");
                    window.location.href = "../index.php";
                    return;
                }
                CallToast(message, "error");
            });
        }
        table.on("tableBuilt", loadStudents);
    </script>
</body>

</html>
