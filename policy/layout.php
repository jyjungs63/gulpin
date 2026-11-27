<?php
/** 약관 페이지 공용 틀 — policy/terms.php, privacy.php, refund.php */
require_once __DIR__ . '/../Server/business_info.php';

function gpPolicyOpen(string $title): void
{
    ?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body{margin:0;background:#f7f8fa;color:#1f2a37;font-family:Arial,"Noto Sans KR",sans-serif;line-height:1.75}
        .policy{width:min(860px,calc(100% - 32px));margin:32px auto;background:#fff;border:1px solid #e3e6eb;border-radius:10px;padding:28px clamp(18px,4vw,40px)}
        .policy h1{font-size:1.6rem;margin:0 0 6px}
        .policy h2{font-size:1.1rem;margin:28px 0 8px}
        .policy table{width:100%;border-collapse:collapse;margin:8px 0;font-size:.92rem}
        .policy th,.policy td{border:1px solid #dfe3e8;padding:8px 10px;text-align:left;vertical-align:top}
        .policy th{background:#f3f5f8}
        .policy .meta{color:#667085;font-size:.9rem}
        .policy .back{display:inline-block;margin-bottom:16px;color:#1769aa;text-decoration:none}
        .policy .table-wrap{overflow-x:auto}
        .gp-biz-missing{color:#c0392b}
    </style>
</head>
<body>
<main class="policy">
    <a class="back" href="../index.php">← 처음으로</a>
    <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="meta">시행일: <?= gpBiz('effective_date') ?></p>
    <?php
}

function gpPolicyClose(): void
{
    ?>
</main>
<?php
    $gpFooterBase = '../';
    include __DIR__ . '/../libs/footer.php';
    ?>
</body>
</html>
<?php
}
