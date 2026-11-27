<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}
if ((int) ($_SESSION['role'] ?? 0) !== 30) {
    http_response_code(403);
    exit('방문수업 교사 계정으로 이용해 주세요.');
}
header('Cache-Control: no-store, private');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>방문수업 학생관리</title>
    <?php require_once __DIR__ . '/../libs/include.php'; ?>
    <link rel="stylesheet" href="../role-nav.css?v=<?= filemtime(__DIR__ . '/../role-nav.css') ?>">
    <style>
        body { margin: 0; background: #f3f6fb; color: #1f2937; }
        main { max-width: 1560px; margin: auto; padding: 24px 16px; }
        h1 { font-size: 24px; font-weight: 800; }
        .hs-card { padding: 20px; background: white; border: 1px solid #d9e2ef; border-radius: 14px; margin-top: 18px; }
        #studentForm { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 16px; }
        #studentForm label { display: flex; flex-direction: column; gap: 6px; font-size: 14px; }
        #studentForm input, #studentForm select { min-width: 0; width: 100%; padding: 9px; border: 1px solid #b8c5d6; border-radius: 6px; }
        .hs-actions { grid-column: 1 / -1; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .hs-toolbar { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin-bottom: 14px; }
        .hs-toolbar input { max-width: 350px; }
        .hs-table { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; font-size: 14px; }
        th, td { padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
        #studentRows tr[data-id] { cursor: pointer; }
        #studentRows tr[data-id]:hover { background: #f8fafc; }
        #studentRows input[type=date] { padding: 5px; border: 1px solid #b8c5d6; border-radius: 6px; }
        #studentRows select { max-width: 260px; vertical-align: middle; padding: 6px; border: 1px solid #b8c5d6; border-radius: 6px; }
        #studentRows select.hs-ended { background: #fff3a3; border-color: #e0b800; }
        #orderWindowInfo { font-size: 14px; color: #166534; font-weight: 700; }
        #orderWindowInfo.closed { color: #b45309; }
        #studentRows .hs-check, #checkAll { width: 16px; height: 16px; cursor: pointer; }
        #notice { color: #b91c1c; min-height: 24px; margin-top: 12px; white-space: pre-line; }
        #notice.success { color: #166534; }
        @media (max-width: 1100px) { #studentForm { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 540px) { #studentForm { grid-template-columns: 1fr; } .hs-card { padding: 14px; } }
    </style>
    <?php require_once __DIR__ . '/../libs/includescr.php'; ?>
    <script src="../js/common.js?v=<?= filemtime(__DIR__ . '/../js/common.js') ?>"></script>
    <script src="../js/roles.js?v=<?= filemtime(__DIR__ . '/../js/roles.js') ?>"></script>
    <script src="../js/role-nav.js?v=<?= filemtime(__DIR__ . '/../js/role-nav.js') ?>" defer></script>
    <script src="homeschoolstudents.js?v=<?= filemtime(__DIR__ . '/homeschoolstudents.js') ?>" defer></script>
</head>
<body>
    <header id="roleNav"></header>
    <main>
        <h1 id="pageTitle">방문수업 학생관리</h1>
        <p>학생별 과정과 교재, 수업 기간을 등록·수정합니다.</p>
        <section class="hs-card" aria-labelledby="formHeading">
            <h2 id="formHeading" class="h5">학생 등록</h2>
            <p>수업 종료일을 제외한 9개 항목은 필수 입력입니다. 수업종료로 바꾸면 그날이 수업 종료일로 입력되고, 수업중으로 바꾸면 수업 종료일이 비워집니다.</p>
            <form id="studentForm">
                <label>과정<select name="course" required disabled></select></label>
                <label>학생명<input name="name" maxlength="20" required></label>
                <label>아이디<input name="id" maxlength="30" pattern="[A-Za-z0-9]{2,30}" required autocomplete="off"></label>
                <label>비번<input name="passwd" type="password" maxlength="30" pattern="[A-Za-z0-9]{4,30}" required autocomplete="new-password"></label>
                <label>출생년도<input name="birth_year" type="number" min="1900" step="1" placeholder="예: 2018" required></label>
                <label>학교명<input name="classnm" maxlength="45" required></label>
                <label>부모님 연락처<input name="p_mobile" type="tel" maxlength="20" required></label>
                <label>단계(교재명)<select name="material_code" required disabled></select></label>
                <label>수업 시작일<input name="sdate" type="date" min="1900-01-01" required></label>
                <label>수업 종료일<input name="edate" type="date" min="1900-01-01" disabled></label>
                <div class="hs-actions">
                    <button id="saveStudent" type="submit" class="btn btn-primary" disabled>학생 등록</button>
                    <button id="resetStudent" type="button" class="btn btn-outline-secondary">입력 초기화</button>
                    <label style="display:block;margin:0"><input id="showPassword" type="checkbox" style="width:auto"> 비번 표시</label>
                </div>
            </form>
            <div id="notice" role="status" aria-live="polite"></div>
        </section>
        <section class="hs-card" aria-label="학생 목록">
            <div class="hs-toolbar">
                <strong id="studentCount">총 학생 수: 0명</strong>
                <input id="studentSearch" class="form-control" aria-label="학생 검색" placeholder="학생명·아이디·학교명 검색">
                <button id="reloadStudents" type="button" class="btn btn-outline-primary">새로고침</button>
                <span id="orderWindowInfo"></span>
                <button id="bulkOrder" type="button" class="btn btn-primary ml-auto" disabled>일괄신청</button>
            </div>
            <div class="hs-table">
                <table>
                    <thead><tr><th><input id="checkAll" type="checkbox" aria-label="신청 가능한 학생 전체 선택"></th><th>과정</th><th>학생명</th><th>아이디</th><th>비번</th><th>출생년도</th><th>학교명</th><th>부모님 연락처</th><th>단계(교재명)</th><th>수업 시작일</th><th>수업 종료일</th><th>관리</th></tr></thead>
                    <tbody id="studentRows"></tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
