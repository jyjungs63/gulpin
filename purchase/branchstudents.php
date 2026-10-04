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
    <script src="../js/roles.js"></script>
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
        @media (max-width: 767px) { .kgarden-shell { padding: 12px; } }
    </style>
</head>

<body class="kgarden-page">
    <header id="roleNav"></header>

    <div class="kgarden-shell">
        <section class="kgarden-heading">
            <h5 id="idHead">학생관리</h5>
            <p>소속 선생님이 등록한 학생 목록입니다. 학생 등록·수정은 각 선생님의 학생관리 화면에서 합니다.</p>
        </section>
        <section class="kgarden-card">
            <div id="table-header">총 학생 수: 0명</div>
            <div class="kgarden-table-wrap">
                <div id="idTable"></div>
            </div>
        </section>
    </div>

    <script>
        // 본사: 전체 학생, 지사: 본인이 등록한 선생의 학생 (서버 SShowBranchStudents 가 범위 결정)
        document.getElementById("idHead").textContent = `[${user}] 학생관리`;
        const escapeHtml = (value) => String(value ?? "").replace(/[&<>'"]/g, (ch) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", "'": "&#39;", '"': "&quot;" }[ch]));

        const table = new Tabulator("#idTable", {
            height: "700px",
            layout: "fitColumns",
            placeholder: "등록된 학생이 없습니다.",
            columns: [
                { title: "소속", field: "owner", headerFilter: "input", minWidth: 120 },
                { title: "담당 선생", field: "teacher", headerFilter: "input", minWidth: 110,
                  formatter: (cell) => {
                      const row = cell.getRow().getData();
                      return escapeHtml(row.teacher ? `${row.teacher} (${row.tid})` : row.tid);
                  } },
                { title: "구분", field: "role", width: 110, formatter: (cell) => GP_ROLE.name(cell.getValue()) },
                { title: "아이디", field: "id", headerFilter: "input", minWidth: 110 },
                { title: "이름", field: "name", headerFilter: "input", minWidth: 100 },
                { title: "학교/반", field: "classnm", headerFilter: "input", minWidth: 110 },
                { title: "단계", field: "step", width: 90 },
                { title: "상태", field: "status", width: 80, formatter: (cell) => Number(cell.getValue()) === 1 ? "재원" : "퇴원" },
                { title: "등록일", field: "rdate", width: 110 },
            ],
        });

        table.on("tableBuilt", () => {
            CallAjax("SMethods.php", "POST", { functionName: "SShowBranchStudents", otherData: {} }, (resp) => {
                const rows = resp.success || [];
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
        });
    </script>
</body>

</html>
