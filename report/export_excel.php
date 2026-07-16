<?php
/**
 * export_excel.php
 * ─────────────────────────────────────────────────────
 * chaitalk_porlist 테이블을 조회하여 엑셀(xlsx)로 변환·다운로드
 *
 * 의존성 : PhpSpreadsheet (Composer)
 *   composer require phpoffice/phpspreadsheet
 *
 * 호출방법
 *   GET  export_excel.php?from=2025-01-01&to=2025-01-31
 *   GET  export_excel.php?all=1          (날짜 필터 없이 전체)
 * ─────────────────────────────────────────────────────
 */
$fullURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
          . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


$isLocal  = (strpos($fullURL, "localhost") !== false)
         || (strpos($fullURL, "127.0.0.1") !== false)
         || (strpos($fullURL, "192.168.") !== false)
         || (strpos($fullURL, "10.") !== false);


    define('DB_HOST',    'localhost');
    define('DB_NAME',    'happyzip1');
    define('DB_CHARSET', 'utf8mb4');

if ($isLocal) {
    define('DB_USER',    'root');
    define('DB_PASS',    'manager');
}
else
{
    define('DB_USER',    'happyzip1');
    define('DB_PASS',    'skl329350281@');
}

// ═══ DB 설정 (본인 환경에 맞게 수정) ═══════════════════


// ═══ PhpSpreadsheet 오토로드 ═══════════════════════════
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

// ═══════════════════════════════════════════════════════
// 1) DB 연결 (싱글턴)
// ═══════════════════════════════════════════════════════
function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

// ═══════════════════════════════════════════════════════
// 2) 주문 목록 조회
// ═══════════════════════════════════════════════════════
function fetchOrders(string $from, string $to, bool $all): array
{
    try {
        $pdo    = getDB();
    } catch (PDOException $e) {
        die('DB 연결 실패: ' . $e->getMessage());
    }

    $where  = [];
    $params = [];

    if (!$all) {
        if ($from !== '') { $where[]  = 'rdate >= ?'; $params[] = $from . ' 00:00:00'; }
        if ($to   !== '') { $where[]  = 'rdate <= ?'; $params[] = $to   . ' 23:59:59'; }
    }

    $sql  = "SELECT * FROM chaitalk_porlist"
          . (empty($where) ? '' : ' WHERE ' . implode(' AND ', $where))
          . " ORDER BY rdate ASC";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        die('SQL 실행 실패: ' . $e->getMessage() . "\nSQL: " . $sql);
    }
}

// ═══════════════════════════════════════════════════════
// 3) por_list JSON → 단계별 구조로 파싱
// ═══════════════════════════════════════════════════════
// function parseJson(string $json): array
// {
//     $out = [
//         '0단계'  => ['title' => '', 'count' => ''],
//         'A단계'  => ['title' => '', 'count' => ''],
//         'B단계'  => ['title' => '', 'count' => ''],
//         '총금액' => 0,
//     ];

//     foreach (json_decode($json, true) ?: [] as $row) {
//         $grade = $row['grade'] ?? '';
//         $total = (int) str_replace(',', '', (string)($row['total'] ?? 0));

//         match ($grade) {
//             '한자뚝0' => $out['0단계'] = ['title' => $row['title'] ?? '', 'count' => $row['count'] ?? ''],
//             '한자뚝A' => $out['A단계'] = ['title' => $row['title'] ?? '', 'count' => $row['count'] ?? ''],
//             '한자뚝B' => $out['B단계'] = ['title' => $row['title'] ?? '', 'count' => $row['count'] ?? ''],
//             '총금액'  => $out['총금액'] = $total,
//             default   => null,
//         };
//     }
//     return $out;
// }
function parseJson(string $json): array
{
    $out = [
        '0단계'  => ['title' => '', 'count' => ''],
        'A단계'  => ['title' => '', 'count' => ''],
        'B단계'  => ['title' => '', 'count' => ''],
        '총금액' => 0,
    ];

    foreach (json_decode($json, true) ?: [] as $row) {
        $grade = $row['grade'] ?? '';
        $total = (int) str_replace(',', '', (string)($row['total'] ?? 0));

        if ($grade === '한자뚝0') {
            $out['0단계'] = ['title' => $row['title'] ?? '', 'count' => $row['count'] ?? ''];
        } elseif ($grade === '한자뚝A') {
            $out['A단계'] = ['title' => $row['title'] ?? '', 'count' => $row['count'] ?? ''];
        } elseif ($grade === '한자뚝B') {
            $out['B단계'] = ['title' => $row['title'] ?? '', 'count' => $row['count'] ?? ''];
        } elseif ($grade === '총금액') {
            $out['총금액'] = $total;
        }
    }
    return $out;
}
// ═══════════════════════════════════════════════════════
// 4) confirm=2(입금완료)이면 날짜 반환, 아니면 빈칸
// ═══════════════════════════════════════════════════════
function confirmDate(array $order): string
{
    return (int)($order['confirm'] ?? 0) === 2
         ? date('n월 j일', strtotime($order['rdate']))
         : '';
}

// ═══════════════════════════════════════════════════════
// 5) 공통 테두리 스타일 적용 헬퍼
// ═══════════════════════════════════════════════════════
function applyBorder(\PhpOffice\PhpSpreadsheet\Style\Style $style): void
{
    $style->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
    $style->getBorders()->getTop()   ->setBorderStyle(Border::BORDER_THIN);
    $style->getBorders()->getLeft()  ->setBorderStyle(Border::BORDER_THIN);
    $style->getBorders()->getRight() ->setBorderStyle(Border::BORDER_THIN);
}

// ═══════════════════════════════════════════════════════
// 6) 엑셀 생성 & 다운로드
// ═══════════════════════════════════════════════════════
function generateExcel(array $orders): void
{
    $sp = new Spreadsheet();
    $ws = $sp->getActiveSheet();
    $ws->setTitle('주문현황');

    // ── 컬럼 폭 ──────────────────────────────────────
    foreach (['A'=>11,'B'=>11,'C'=>13,'D'=>13,'E'=>7,'F'=>13,'G'=>7,
              'H'=>13,'I'=>7,'J'=>13,'K'=>11,'L'=>17,'M'=>11,'N'=>9,'O'=>11] as $c => $w)
        $ws->getColumnDimension($c)->setWidth($w);

    // ── 헤더 ──────────────────────────────────────────
    $headers = [
        'A'=>'날짜','B'=>'지사명','C'=>'유치원명',
        'D'=>'0단계','E'=>'수량',
        'F'=>'A단계','G'=>'수량',
        'H'=>'B단계','I'=>'수량',
        'J'=>'합계금액','K'=>'입금완료',
        'L'=>'송장번호','M'=>'출고날짜',
        'N'=>'반품','O'=>'비고'
    ];

    foreach ($headers as $col => $text) {
        $cell = $ws->getCell($col . '1');
        $cell->setValue($text);

        $s = $cell->getStyle();
        $s->getFont()->setBold(true)->setSize(11)->setName('맑은 고딕');
        $s->getFont()->getColor()->setARGB('FFFFFFFF');
        $s->getFill()->setFillType(Fill::FILL_SOLID);
        $s->getFill()->getStartColor()->setARGB('FFFFA63B');
        $s->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                          ->setVertical(Alignment::VERTICAL_CENTER);
        applyBorder($s);
    }
    $ws->getRowDimension(1)->setRowHeight(22);

    // ── 데이터 행 ─────────────────────────────────────
    $row = 2;
    foreach ($orders as $order) {
        $por  = parseJson($order['por_list']);
        $date = date('n월 j일', strtotime($order['rdate']));

        // A~C : 날짜 / 지사 / 유치원
        $ws->getCell('A' . $row)->setValue($date);
        $ws->getCell('B' . $row)->setValue($order['addr']  ?? '');
        $ws->getCell('C' . $row)->setValue($order['order'] ?? '');

        // D~E : 0단계
        $ws->getCell('D' . $row)->setValue($por['0단계']['title']);
        if ($por['0단계']['count'] !== '')
            $ws->getCell('E' . $row)->setValue((int)$por['0단계']['count']);

        // F~G : A단계
        $ws->getCell('F' . $row)->setValue($por['A단계']['title']);
        if ($por['A단계']['count'] !== '')
            $ws->getCell('G' . $row)->setValue((int)$por['A단계']['count']);

        // H~I : B단계
        $ws->getCell('H' . $row)->setValue($por['B단계']['title']);
        if ($por['B단계']['count'] !== '')
            $ws->getCell('I' . $row)->setValue((int)$por['B단계']['count']);

        // J : 합계금액 (쉼표 포맷)
        $ws->getCell('J' . $row)->setValue($por['총금액']);
        $ws->getCell('J' . $row)->getStyle()->getNumberFormat()->setFormatCode('#,##0');

        // K : 입금완료
        $ws->getCell('K' . $row)->setValue(confirmDate($order));

        // L : 송장번호
        $ws->getCell('L' . $row)->setValue($order['invoice'] ?? '');

        // M : 출고날짜
        $ws->getCell('M' . $row)->setValue($date);

        // N : 반품
        $ws->getCell('N' . $row)->setValue($order['refund'] ?? '');

        // O : 비고 (빈칸)
        $ws->getCell('O' . $row)->setValue('');

        // ── 행 테두리 + 폰트 + 가운데 정렬 ──────────────
        foreach (str_split('ABCDEFGHIJKLMNO') as $col) {
            $s = $ws->getCell($col . $row)->getStyle();
            applyBorder($s);
            $s->getFont()->setName('맑은 고딕')->setSize(11);
        }
        // 날짜·수량·금액·입금완료·출고날짜 → 가운데
        foreach (['A','E','G','I','J','K','M'] as $col)
            $ws->getCell($col . $row)->getStyle()
               ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
    }

    // ── 자동필터 ───────────────────────────────────────
    if ($row > 2) $ws->setAutoFilter('A1:O' . ($row - 1));

    // ── 파일 다운로드 ─────────────────────────────────
    $fname = '주문현황_' . date('Y-m-d') . '.xlsx';
    $tmp   = sys_get_temp_dir() . '/chaitalk_porlist_export.xlsx';
    (new Xlsx($sp))->save($tmp);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename=' . urlencode($fname));
    header('Cache-Control: max-age=0');
    readfile($tmp);
    unlink($tmp);
    exit;
}

// ═══════════════════════════════════════════════════════
// 진입점
// ═══════════════════════════════════════════════════════
$orders = fetchOrders(
    $_GET['from'] ?? '',
    $_GET['to']   ?? '',
    ($_GET['all'] ?? '') === '1'
);
generateExcel($orders);