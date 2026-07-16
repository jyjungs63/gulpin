<?php
// api.php
header('Content-Type: application/json');
require_once '../libs/db_config.php'; // DB 설정 파일 포함

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB 접속 실패']);
    exit;
}

// 2. 요청 모드 판별
$mode = $_GET['mode'] ?? '';

// --- 모드별 처리 ---

// A-0. role=2 사용자를 chaitalk_online_ma에 동기화 (미등록자만 INSERT)
if ($mode === 'sync_users') {
    try {
        $sql = "INSERT INTO chaitalk_online_ma (id, name, status, mon, created_at)
                SELECT u.id, u.name, 'all', NULL, CURRENT_TIMESTAMP
                FROM chaitalk_user u
                WHERE u.role = 2
                  AND NOT EXISTS (
                      SELECT 1 FROM chaitalk_online_ma m WHERE m.id = u.id
                  )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'inserted' => $stmt->rowCount()]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// A. 사용자 목록 조회
if ($mode === 'list') {
// 필터 파라미터가 있으면 해당 상태만 조회, 없으면 전체 조회
    $filter = $_GET['filter'] ?? 'all_list'; 
    if ($filter === 'all_list') {
        $stmt = $pdo->query("SELECT id, name, status FROM chaitalk_online_ma ORDER BY name ASC");
    } else {
        $stmt = $pdo->prepare("SELECT id, name, status FROM chaitalk_online_ma WHERE status = ? ORDER BY name ASC");
        $stmt->execute([$filter]);
    }
    echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
} 
elseif ($mode === 'list_with_volume') {
    // 한 번의 쿼리로 모든 유치원 + mon 데이터를 한꺼번에 가져옴
    $sql = "SELECT id, name, status, mon 
            FROM chaitalk_online_ma 
            ORDER BY name ASC";
    $stmt = $pdo->query($sql);
    echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
}

// B. 특정 사용자 상세 조회
elseif ($mode === 'get' && isset($_GET['id'])) {
    $target_id = $_GET['id'];

    try {
        // 1. 먼저 본인의 설정이 chaitalk_online_ma에 있는지 확인
        $stmt = $pdo->prepare("SELECT status, mon FROM chaitalk_online_ma WHERE id = ?");
        $stmt->execute([$target_id]);
        $config = $stmt->fetch();

        // 2. 본인 설정이 없다면 chaitalk_user에서 tid(관리자 ID)를 찾아 그 설정값을 확인
        if (!$config) {
            $sql = "SELECT m.status, m.mon
                    FROM chaitalk_user u
                    JOIN chaitalk_online_ma m ON u.tid = m.id
                    WHERE u.id = ? AND u.tid IS NOT NULL AND u.tid != ''";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$target_id]);
            $config = $stmt->fetch();
        }

        // 2-1. tid로도 못 찾으면 mid(지사 ID)를 통해 설정값 확인
        if (!$config) {
            $sql = "SELECT m.status, m.mon
                    FROM chaitalk_user u
                    JOIN chaitalk_online_ma m ON u.id = m.id
                    WHERE u.id = ? ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$target_id]);
            $config = $stmt->fetch();
        }

        // 3. 둘 다 없다면(본인 설정도 없고, 소속된 tid의 설정도 없는 경우) 기본값 반환
        if (!$config) {
            echo json_encode([
                'status' => 'success', 
                'data' => ['status' => 'all', 'mon' => '', 'source' => 'default']
            ]);
        } else {
            // 어디서 가져온 데이터인지 구분하기 위해 source 필드 추가 가능 (선택사항)
            echo json_encode([
                'status' => 'success', 
                'data' => $config
            ]);
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// C. 이번 달 학습 기록 카운트 조회 (study_record)
elseif ($mode === 'record_count' && isset($_GET['id'])) {
    $id = trim($_GET['id']);
    try {
        $stmt = $pdo->prepare(
            "SELECT uid, COUNT(*) AS cnt FROM study_record
             WHERE id = ?
               AND YEAR(rdate)  = YEAR(CURDATE())
               AND MONTH(rdate) = MONTH(CURDATE())
             GROUP BY uid"
        );
        $stmt->execute([$id]);
        $rows   = $stmt->fetchAll();
        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['uid']] = (int)$row['cnt'];
        }
        echo json_encode(['status' => 'success', 'data' => $counts]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// C-2. 학습 기록 저장 (study_record)
elseif ($mode === 'record' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) { echo json_encode(['status' => 'error', 'message' => '입력 오류']); exit; }

    $id     = trim($input['id']     ?? '');
    $step   = strtoupper(trim($input['step']   ?? ''));
    $volume = strtolower(trim($input['volume'] ?? ''));
    $uid    = trim($input['uid']    ?? '');

    $valid_steps   = ['0', 'A', 'B', 'T'];
    $valid_volumes = ['v1','v2','v3','v4','v5','v6','v7','v8','v9','v10','v11','v12'];

    if (!$id || !$uid || mb_strlen($id) > 20
        || !in_array($step,   $valid_steps,   true)
        || !in_array($volume, $valid_volumes, true)
        || mb_strlen($uid) > 10
    ) {
        echo json_encode(['status' => 'error', 'message' => '필수 값 누락 또는 유효하지 않은 값']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO study_record (id, step, volume, uid, rdate) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$id, $step, $volume, $uid]);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// D. 그룹별 학습 통계 조회
elseif ($mode === 'study_stats') {
    // 월 필터: ?month=2025-03 형식, 없으면 전체
    $month   = $_GET['month'] ?? '';
    $req_id  = trim($_GET['id']   ?? '');
    $req_role = (int)($_GET['role'] ?? 9);

    try {
        // id/role 필터 파라미터를 먼저 구성 (순서 중요: 앞에 배치)
        $idWhere  = '';
        $idParams = [];
        if ($req_id !== '') {
            if ($req_role === 2 || $req_role === 7) {
                $idWhere  = 'AND u.tid = ?';
                $idParams = [$req_id];
            } elseif ($req_role === 3 || $req_role === 30) {
                $idWhere  = 'AND (u.tid = ? OR u.tid IN (SELECT id FROM chaitalk_user WHERE tid = ? AND role IN (2,7)))';
                $idParams = [$req_id, $req_id];
            }
        }

        // 날짜 필터 파라미터 (뒤에 배치)
        $dateWhere = '';
        $dateParams = [];
        if (preg_match('/^\d{4}-\d{2}$/', $month)) {
            $dateWhere  = 'AND YEAR(s.rdate) = ? AND MONTH(s.rdate) = ?';
            [$y, $m]    = explode('-', $month);
            $dateParams = [(int)$y, (int)$m];
        }

        $params = array_merge($idParams, $dateParams);

        // study_record를 기준으로 시작해 누락 없이 전체 집계
        // tid 없는 사용자는 본인 id를 그룹 키로 사용
        $sql = "
            SELECT
                COALESCE(u.tid, s.id)        AS tid,
                COALESCE(t.name, s.id)       AS teacher_name,
                COALESCE(mg.owner, '')       AS branch_name,
                COUNT(s.no)                  AS total_study_count,
                COUNT(DISTINCT s.id)         AS student_count
            FROM study_record s
            LEFT JOIN  chaitalk_user u  ON s.id = u.id
            LEFT JOIN  chaitalk_user t  ON u.tid = t.id
            LEFT JOIN  chaitalk_user mg ON t.mid = mg.id
            WHERE 1=1 $idWhere $dateWhere
            GROUP BY COALESCE(u.tid, s.id), COALESCE(t.name, s.id), COALESCE(mg.owner, '')
            ORDER BY total_study_count DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// E. 학생별 학습 통계 상세 조회 (원 클릭 시)
elseif ($mode === 'study_stats_detail') {
    $tid   = trim($_GET['tid'] ?? '');
    $month = $_GET['month'] ?? '';

    if ($tid === '') {
        echo json_encode(['status' => 'error', 'message' => 'tid 필수']);
        exit;
    }

    try {
        $dateWhere  = '';
        $dateParams = [];
        if (preg_match('/^\d{4}-\d{2}$/', $month)) {
            $dateWhere  = 'AND YEAR(s.rdate) = ? AND MONTH(s.rdate) = ?';
            [$y, $m]    = explode('-', $month);
            $dateParams = [(int)$y, (int)$m];
        }

        $params = array_merge([$tid], $dateParams);

        $sql = "
            SELECT
                s.id                          AS student_id,
                COALESCE(u.name, s.id)        AS student_name,
                COALESCE(u.owner, '')         AS class_name,
                COUNT(s.no)                   AS study_count,
                MAX(s.rdate)                  AS last_study
            FROM study_record s
            LEFT JOIN chaitalk_user u ON s.id = u.id
            WHERE u.tid = ? $dateWhere
            GROUP BY s.id, COALESCE(u.name, s.id), COALESCE(u.owner, '')
            ORDER BY study_count DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// E-2. 학생 개인 일별 학습 차트 데이터
elseif ($mode === 'study_stats_student_chart') {
    $studentId = trim($_GET['student_id'] ?? '');
    $month     = $_GET['month'] ?? '';

    if ($studentId === '') {
        echo json_encode(['status' => 'error', 'message' => 'student_id 필수']);
        exit;
    }

    try {
        $dateWhere  = '';
        $dateParams = [];
        if (preg_match('/^\d{4}-\d{2}$/', $month)) {
            $dateWhere  = 'AND YEAR(s.rdate) = ? AND MONTH(s.rdate) = ?';
            [$y, $m]    = explode('-', $month);
            $dateParams = [(int)$y, (int)$m];
        }

        $params = array_merge([$studentId], $dateParams);

        // 일별 학습 횟수
        $sql = "
            SELECT DATE(s.rdate) AS study_date, COUNT(s.no) AS cnt
            FROM study_record s
            WHERE s.id = ? $dateWhere
            GROUP BY DATE(s.rdate)
            ORDER BY study_date ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $daily = $stmt->fetchAll();

        // step별 학습 횟수
        $sql2 = "
            SELECT s.step, COUNT(s.no) AS cnt
            FROM study_record s
            WHERE s.id = ? $dateWhere
            GROUP BY s.step
            ORDER BY s.step ASC
        ";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute($params);
        $byStep = $stmt2->fetchAll();

        echo json_encode(['status' => 'success', 'daily' => $daily, 'byStep' => $byStep]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// F. 업데이트 처리 (POST)
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) exit;

    // months 키 존재 여부 + partial 상태 검증
    $mon = null;
    if ($input['status'] === 'partial' && !empty($input['months'])) {
        $mon = $input['months']; // "5,6,7,12"
    }

    // 디버그용 (확인 후 제거)
    error_log("저장 요청 - id:{$input['id']} status:{$input['status']} mon:{$mon}");

    try {
        $sql = "UPDATE chaitalk_online_ma SET status = :status, mon = :mon WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'status' => $input['status'],
            'mon'    => $mon,
            'id'     => $input['id']
        ]);

        // 실제로 업데이트 됐는지 확인
        if ($stmt->rowCount() === 0) {
            echo json_encode(['status' => 'error', 'message' => '해당 ID가 없거나 변경사항 없음']);
        } else {
            echo json_encode(['status' => 'success', 'message' => '저장 완료']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>