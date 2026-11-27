<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}
$role = (int) ($_SESSION['role'] ?? 0);
if (!in_array($role, array(3, 9, 30), true)) {
    http_response_code(403);
    exit('방문수업 지사 또는 교사 계정으로 이용해 주세요.');
}
header('Cache-Control: no-store, private');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>방문수업 교재 주문현황</title>
    <?php require_once __DIR__ . '/../libs/include.php'; ?>
    <link rel="stylesheet" href="../role-nav.css?v=<?= filemtime(__DIR__ . '/../role-nav.css') ?>">
    <style>
        body { margin: 0; background: #f3f6fb; color: #1f2937; }
        main { max-width: 1560px; margin: auto; padding: 24px 16px; }
        h1 { font-size: 24px; font-weight: 800; }
        .hs-card { padding: 20px; background: white; border: 1px solid #d9e2ef; border-radius: 14px; margin-top: 18px; }
        .hs-toolbar { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin-bottom: 14px; }
        .hs-toolbar label { display: flex; gap: 6px; align-items: center; margin: 0; }
        .hs-toolbar select { padding: 6px; border: 1px solid #b8c5d6; border-radius: 6px; max-width: 220px; }
        .hs-table { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; font-size: 14px; }
        th, td { padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; cursor: pointer; user-select: none; }
        .ho-pending { color: #c2410c; font-weight: 700; }
        .ho-day { width: 64px; padding: 5px; border: 1px solid #b8c5d6; border-radius: 6px; }
        #windowInfo { color: #475569; }
    </style>
    <?php require_once __DIR__ . '/../libs/includescr.php'; ?>
    <script src="../js/common.js?v=<?= filemtime(__DIR__ . '/../js/common.js') ?>"></script>
    <script src="../js/roles.js?v=<?= filemtime(__DIR__ . '/../js/roles.js') ?>"></script>
    <script src="../js/role-nav.js?v=<?= filemtime(__DIR__ . '/../js/role-nav.js') ?>" defer></script>
    <script src="homeorderstatus.js?v=<?= filemtime(__DIR__ . '/homeorderstatus.js') ?>" defer></script>
</head>
<body data-role="<?= $role ?>">
    <header id="roleNav"></header>
    <main>
        <h1>방문수업 교재 주문현황</h1>
        <p>교사가 신청한 교재와 지사 주문완료 현황입니다. 표 제목을 누르면 정렬됩니다.</p>
        <section class="hs-card hs-toolbar" aria-label="교재 신청 기간">
            <strong>교재 신청 기간</strong>
            <?php if ($role === 9) { ?>
                <label>매월<input id="windowStart" type="number" min="1" max="31" class="ho-day">일</label>
                <label>~<input id="windowEnd" type="number" min="1" max="31" class="ho-day">일</label>
                <button id="saveWindow" type="button" class="btn btn-primary btn-sm">저장</button>
            <?php } ?>
            <span id="windowInfo"></span>
        </section>
        <section class="hs-card" aria-label="주문 목록">
            <div class="hs-toolbar">
                <label>월<select id="fMonth"></select></label>
                <?php if ($role === 9) { ?><label>지사<select id="fBranch"></select></label><?php } ?>
                <?php if ($role !== 30) { ?><label>교사<select id="fTeacher"></select></label><?php } ?>
                <label>학생<select id="fStudent"></select></label>
                <label>주문상태<select id="fStatus"><option value="">전체</option><option value="pending">신청 완료</option><option value="done">주문 완료</option></select></label>
                <button id="reloadOrders" type="button" class="btn btn-outline-primary">새로고침</button>
                <strong id="orderSummary"></strong>
            </div>
            <div class="hs-table">
                <table>
                    <thead><tr id="orderHead"></tr></thead>
                    <tbody id="orderRows"></tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
