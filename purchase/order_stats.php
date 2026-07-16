<!DOCTYPE html>
<html lang="ko">
<?php include "../libs/header.php"; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>주문 통계</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }

        .stat-card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }
        .stat-card .card-header {
            background-color: #38998580;
            font-weight: bold;
            font-size: 1rem;
        }

        /* 연간 합계 테이블 */
        #tbl-yearly th, #tbl-yearly td { text-align: center; vertical-align: middle; }
        #tbl-yearly tr.rollup-total { background-color: #fff3cd; font-weight: bold; }

        /* 월별 테이블 */
        #tbl-monthly th { text-align: center; vertical-align: middle; }
        #tbl-monthly td { vertical-align: middle; }
        #tbl-monthly td.num { text-align: right; }
        #tbl-monthly tr.row-month-sub  { background-color: #e8f4fd; font-weight: bold; }
        #tbl-monthly tr.row-grand-total { background-color: #fff3cd; font-weight: bold; }

        .filter-bar { background: #fff; padding: 14px 20px; border-radius: 8px;
                      box-shadow: 0 1px 4px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .month-label { display: inline-block; min-width: 60px; }
    </style>
</head>
<body>
<?php
/* ================================================================
   DB 연결 (싱글톤)
================================================================ */
require_once '../libs/db_config.php';
$db   = Database::getInstance()->getConnection();

/* ================================================================
   파라미터
================================================================ */
$year      = isset($_GET['year'])      ? (int)$_GET['year']      : (int)date('Y');
$date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$date_to   = isset($_GET['date_to'])   ? trim($_GET['date_to'])   : '';

$use_range = ($date_from && $date_to
    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from)
    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)
    && $date_from <= $date_to);

/* ================================================================
   MariaDB 10.2 호환: JSON_TABLE 없이 PHP에서 JSON 파싱 후 집계
================================================================ */
if ($use_range) {
    $sql = "
    SELECT YEAR(rdate) AS year_num, MONTH(rdate) AS month_num, por_list
    FROM   chaitalk_porlist
    WHERE  rdate >= :date_from AND rdate < DATE_ADD(:date_to, INTERVAL 1 DAY)
      AND  confirm = 1
      AND  por_list IS NOT NULL
      AND  por_list != ''
    ORDER  BY rdate
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute([':date_from' => $date_from, ':date_to' => $date_to]);
} else {
    $sql = "
    SELECT YEAR(rdate) AS year_num, MONTH(rdate) AS month_num, por_list
    FROM   chaitalk_porlist
    WHERE  YEAR(rdate) = :year
      AND  confirm = 1
      AND  por_list IS NOT NULL
      AND  por_list != ''
    ORDER  BY rdate
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute([':year' => $year]);
}
$rawRows = $stmt->fetchAll();

/* ── 단계(grade) 정렬 순서 ── */
$grade_order = ['4세', '5세', '6세', '7세', '교구'];

$grade_sort = function($a, $b) use ($grade_order) {
    $ia = array_search($a, $grade_order);
    $ib = array_search($b, $grade_order);
    $ia = ($ia === false) ? 999 : $ia;
    $ib = ($ib === false) ? 999 : $ib;
    return $ia - $ib;
};

/* ── PHP에서 집계 ── */
// 연간: grade => [title => count]
// 월별: (year*100+month) => [grade => count]
$yearly_map  = [];
$monthly_map = [];
$years_seen  = [];

foreach ($rawRows as $row) {
    $month     = (int)$row['month_num'];
    $year_num  = (int)$row['year_num'];
    $month_key = $year_num * 100 + $month;
    $items = json_decode($row['por_list'], true);
    if (!is_array($items)) continue;

    $years_seen[$year_num] = true;

    foreach ($items as $item) {
        $title = isset($item['title']) ? trim($item['title']) : '';
        $grade = isset($item['grade']) ? trim($item['grade']) : '기타';
        $cnt   = isset($item['count']) ? (int)$item['count'] : 0;

        if ($grade === '총금액' || $title === '' || $cnt <= 0) continue;

        $yearly_map[$grade][$title]      = ($yearly_map[$grade][$title] ?? 0) + $cnt;
        $monthly_map[$month_key][$grade] = ($monthly_map[$month_key][$grade] ?? 0) + $cnt;
    }
}
$multi_year   = count($years_seen) > 1;
$period_label = $use_range
    ? htmlspecialchars($date_from) . ' ~ ' . htmlspecialchars($date_to)
    : $year . '년';

/* 연간: grade 순서 정렬 → 각 grade 내 수량 내림차순 */
uksort($yearly_map, $grade_sort);
foreach ($yearly_map as &$titles) { arsort($titles); }
unset($titles);
$yearly_total = array_sum(array_map('array_sum', $yearly_map));

/* 월별: 월 오름차순 → 각 월 내 grade 순서 정렬 */
ksort($monthly_map);
foreach ($monthly_map as &$grades) { uksort($grades, $grade_sort); }
unset($grades);
?>

<div class="container-fluid p-4">

    <!-- ── 필터 바 ── -->
    <div class="filter-bar">
        <form method="GET" class="form-inline flex-wrap" id="frmFilter">
            <label class="mr-2 font-weight-bold">연도</label>
            <select name="year" id="selYear" class="form-control form-control-sm mr-2" style="width:100px"
                    <?= $use_range ? 'disabled' : '' ?>>
                <?php
                $curYear = (int)date('Y');
                for ($y = $curYear; $y >= $curYear - 4; $y--) {
                    $sel = ($y === $year) ? 'selected' : '';
                    echo "<option value='$y' $sel>{$y}년</option>";
                }
                ?>
            </select>

            <span class="text-muted mx-3">|</span>
            <label class="mr-2 font-weight-bold">기간</label>
            <input type="date" name="date_from" id="inpFrom"
                   class="form-control form-control-sm mr-1" style="width:145px"
                   value="<?= htmlspecialchars($date_from) ?>">
            <span class="mx-1 text-muted">~</span>
            <input type="date" name="date_to" id="inpTo"
                   class="form-control form-control-sm ml-1 mr-3" style="width:145px"
                   value="<?= htmlspecialchars($date_to) ?>">

            <button type="submit" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-search"></i> 조회
            </button>
            <?php if ($use_range): ?>
            <a href="?year=<?= $year ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times"></i> 기간 초기화
            </a>
                    <!-- <div class="mt-2 text-muted small">
            <b><?= $period_label ?></b> 확정 주문 통계
        </div> -->
            <?php endif; ?>
        </form>

    </div>

    <div class="row">
        <!-- ══════════════════════════════════════════
             왼쪽: 연간 품목별 합계
        ══════════════════════════════════════════ -->
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-header">
                    <i class="fas fa-box-open mr-1"></i> <?= $period_label ?> 품목별 합계
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered table-hover mb-0" id="tbl-yearly">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:70px">단계</th>
                                <th>품목명</th>
                                <th style="width:90px" class="text-right">수량</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($yearly_map)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">데이터 없음</td></tr>
                        <?php else: ?>
                            <?php foreach ($yearly_map as $grade => $titles):
                                $grade_total = array_sum($titles);
                                $rowspan     = count($titles) + 1;
                                $firstRow    = true;
                            ?>
                                <?php foreach ($titles as $title => $cnt): ?>
                                <tr>
                                    <?php if ($firstRow): $firstRow = false; ?>
                                    <td rowspan="<?= $rowspan ?>"
                                        class="text-center align-middle table-light font-weight-bold">
                                        <?= htmlspecialchars($grade) ?>
                                    </td>
                                    <?php endif; ?>
                                    <td class="pl-3"><?= htmlspecialchars($title) ?></td>
                                    <td class="text-right"><?= number_format($cnt) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="row-month-sub">
                                    <td><i class="fas fa-level-up-alt fa-rotate-90 mr-1 text-muted small"></i>소계</td>
                                    <td class="text-right"><?= number_format($grade_total) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="rollup-total">
                                <td colspan="2"><i class="fas fa-sigma mr-1"></i>전체합계</td>
                                <td class="text-right"><?= number_format($yearly_total) ?></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════
             오른쪽: 월별 품목별 합계
        ══════════════════════════════════════════ -->
        <div class="col-md-8">
            <div class="card stat-card">
                <div class="card-header">
                    <i class="fas fa-calendar-alt mr-1"></i> <?= $period_label ?> 월별 품목별 합계
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-bordered mb-0" id="tbl-monthly">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:130px">월 / 단계</th>
                                <th style="width:90px" class="text-right">수량</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($monthly_map)): ?>
                            <tr><td colspan="2" class="text-center text-muted py-3">데이터 없음</td></tr>
                        <?php else: ?>
                            <?php
                            $grand_total = 0;
                            foreach ($monthly_map as $month_key => $grades):
                                $sub_total   = array_sum($grades);
                                $grand_total += $sub_total;
                                $disp_m      = $month_key % 100;
                                $disp_y      = intdiv($month_key, 100);
                                $month_label = $multi_year ? ($disp_y . '년 ' . $disp_m . '월') : ($disp_m . '월');
                                $firstRow = true;
                            ?>
                                <?php foreach ($grades as $grade => $cnt): ?>
                                <tr>
                                    <?php if ($firstRow): $firstRow = false; ?>
                                    <td class="text-center align-middle table-light">
                                        <span class="font-weight-bold"><?= $month_label ?></span>
                                        <small class="text-muted mx-1">/</small><?= htmlspecialchars($grade) ?>
                                    </td>
                                    <?php else: ?>
                                    <td class="text-center"><?= htmlspecialchars($grade) ?></td>
                                    <?php endif; ?>
                                    <td class="num"><?= number_format($cnt) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <!-- 월 소계 행 -->
                                <tr class="row-month-sub">
                                    <td><i class="fas fa-level-up-alt fa-rotate-90 mr-1 text-muted small"></i>소계</td>
                                    <td class="num"><?= number_format($sub_total) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- 전체합계 행 -->
                            <tr class="row-grand-total">
                                <td><i class="fas fa-sigma mr-1"></i>전체합계</td>
                                <td class="num"><?= number_format($grand_total) ?></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- /.row -->
</div><!-- /.container-fluid -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js"></script>
<script src="../js/header.js"></script>
<script src="../js/common.js"></script>
<script>
(function () {
    const selYear = document.getElementById('selYear');
    const inpFrom = document.getElementById('inpFrom');
    const inpTo   = document.getElementById('inpTo');

    function syncYearState() {
        const hasRange = inpFrom.value || inpTo.value;
        selYear.disabled = !!hasRange;
        // disabled 요소는 form 전송에서 제외되므로 hidden으로 보존
        document.getElementById('hdnYear').value = selYear.value;
    }

    // year hidden input 추가 (disabled 시 값 전달용)
    const hdnYear = document.createElement('input');
    hdnYear.type  = 'hidden';
    hdnYear.name  = 'year';
    hdnYear.id    = 'hdnYear';
    hdnYear.value = selYear.value;
    document.getElementById('frmFilter').appendChild(hdnYear);
    // select에 name 중복 방지
    selYear.removeAttribute('name');

    function onDateChange() {
        syncYearState();
        // 시작일이 종료일보다 크면 종료일 자동 조정
        if (inpFrom.value && inpTo.value && inpFrom.value > inpTo.value) {
            inpTo.value = inpFrom.value;
        }
    }

    function onYearChange() {
        inpFrom.value = '';
        inpTo.value   = '';
        hdnYear.value = selYear.value;
        selYear.disabled = false;
    }

    inpFrom.addEventListener('change', onDateChange);
    inpTo.addEventListener('change', onDateChange);
    selYear.addEventListener('change', onYearChange);

    syncYearState();
})();
</script>
</body>
</html>
