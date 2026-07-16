<?php
ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
/**
 * preview_excel.php
 * ─────────────────────────────────────────────────────
 * export_excel.php와 동일한 조회 로직으로
 * 프론트엔드 미리보기용 JSON을 반환한다.
 *
 * 반환 형태 (배열):
 *   [
 *     {
 *       "date"         : "1월 9일",
 *       "addr"         : "가지사",
 *       "order"        : "나유치원",
 *       "title_0"      : "",          // 0단계 title
 *       "count_0"      : "",          // 0단계 수량
 *       "title_A"      : "한자뚝A-01",
 *       "count_A"      : "12",
 *       "title_B"      : "한자뚝B-02",
 *       "count_B"      : "5",
 *       "total"        : 40500,       // 총금액
 *       "confirm_date" : "1월 9일",   // confirm=2이면 날짜, 아니면 빈칸
 *       "invoice"      : "4307567890",
 *       "out_date"     : "1월 9일",
 *       "refund"       : ""
 *     },
 *     …
 *   ]
 * ─────────────────────────────────────────────────────
 */

// ═══ DB 설정 ═══════════════════════════════════════════
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

// ═══ DB 연결 ═══════════════════════════════════════════
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

// ═══ 조회 ══════════════════════════════════════════════
function fetchOrders(string $from, string $to, bool $all): array
{
    $pdo    = getDB();
    $where  = [];
    $params = [];

    if (!$all) {
        if ($from !== '') { $where[]  = 'p.rdate >= ?'; $params[] = $from . ' 00:00:00'; }
        if ($to   !== '') { $where[]  = 'p.rdate <= ?'; $params[] = $to   . ' 23:59:59'; }
    }

    $sql  = "SELECT p.*, p.addr AS owner, p.addr AS user_addr FROM chaitalk_porlist p"
          . (empty($where) ? '' : ' WHERE ' . implode(' AND ', $where))
          . " ORDER BY p.rdate ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// ═══ JSON 파싱 ═════════════════════════════════════════
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
        '0단계'  => ['title' => '', 'count' => 0],
        'A단계'  => ['title' => '', 'count' => 0],
        'B단계'  => ['title' => '', 'count' => 0],
        '총금액' => 0,
    ];
    foreach (json_decode($json, true) ?: [] as $row) {
        $grade = $row['grade'] ?? '';
        $total = (int) str_replace(',', '', (string)($row['total'] ?? 0));
        $title = $row['title'] ?? '';
        $count = (int)($row['count'] ?? 0);

        $key = null;
        if (strpos($grade, '한자뚝0') !== false) $key = '0단계';
        elseif (strpos($grade, '한자뚝A') !== false) $key = 'A단계';
        elseif (strpos($grade, '한자뚝B') !== false) $key = 'B단계';
        elseif ($grade === '총금액') { $out['총금액'] = $total; continue; }

        if ($key !== null) {
            // 제목(수량) 형태로 연결: 한자뚝A-04(3), 한자뚝A-05(5)
            $item = $title . '(' . $count . ')';
            if ($out[$key]['title'] === '') {
                $out[$key]['title'] = $item;
            } else {
                $out[$key]['title'] .= ', ' . $item;
            }
            // 수량: 합산
            $out[$key]['count'] += $count;
        }
    }

    // count를 문자열로 변환 (0이면 빈문자열)
    foreach (['0단계', 'A단계', 'B단계'] as $k) {
        $out[$k]['count'] = $out[$k]['count'] > 0 ? (string)$out[$k]['count'] : '';
    }
    return $out;
}
// ═══ 진입점 ════════════════════════════════════════════
header('Content-Type: application/json; charset=utf-8');

try {
    $orders = fetchOrders(
        $_GET['from'] ?? '',
        $_GET['to']   ?? '',
        ($_GET['all'] ?? '') === '1'
    );

    $result = [];
    foreach ($orders as $order) {
        $por  = parseJson($order['por_list']);
        $date = date('n월 j일', strtotime($order['rdate']));

        $result[] = [
            'date'         => $date,
            'id'           => $order['id']    ?? '',
            'name'         => $order['name']  ?? '',
            'owner'        => ($order['owner'] ?? '') !== '' ? $order['owner'] : ($order['name'] ?? ''),
            'order'        => $order['order']     ?? '',
            'addr'         => $order['user_addr'] ?? '',
            'title_0'      => $por['0단계']['title'],
            'count_0'      => $por['0단계']['count'],
            'title_A'      => $por['A단계']['title'],
            'count_A'      => $por['A단계']['count'],
            'title_B'      => $por['B단계']['title'],
            'count_B'      => $por['B단계']['count'],
            'total_count'  => (int)$por['0단계']['count'] + (int)$por['A단계']['count'] + (int)$por['B단계']['count'],
            'total'        => $por['총금액'],
            'confirm_date' => (int)($order['confirm'] ?? 0) === 2 ? $date : '',
            'invoice'      => $order['invoice'] ?? '',
            'out_date'     => $date,
            'refund'       => $order['refund']  ?? '',
        ];
    }

    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'status' => 'error',
        'message' => '데이터 조회 실패: ' . $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
