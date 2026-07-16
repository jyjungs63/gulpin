<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once dirname(__DIR__) . '/db.php';

function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    $db = review_db();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $includeHidden = ($_GET['admin'] ?? '') === '1';
        $sql = 'SELECT id, title, content, author, student_grade, rating, image_path, is_visible, created_at FROM reviews';
        if (!$includeHidden) {
            $sql .= ' WHERE is_visible = 1';
        }
        $sql .= ' ORDER BY id DESC';
        $reviews = $db->query($sql)->fetchAll();
        foreach ($reviews as &$review) {
            $review['image_url'] = $review['image_path'] ?: null;
        }
        unset($review);
        json_response(['success' => true, 'reviews' => $reviews]);
    }

    $payload = json_decode(file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    if ($method === 'POST') {
        $id = (int) ($payload['id'] ?? 0);
        $title = trim((string) ($payload['title'] ?? ''));
        $content = trim((string) ($payload['content'] ?? ''));
        $author = trim((string) ($payload['author'] ?? ''));
        $grade = trim((string) ($payload['student_grade'] ?? ''));
        $rating = (int) ($payload['rating'] ?? 5);

        if ($title === '' || $content === '' || $author === '' || $grade === '' || $rating < 1 || $rating > 5) {
            json_response(['success' => false, 'message' => '모든 항목을 올바르게 입력해 주세요.'], 422);
        }

        $existingImagePath = null;
        if ($id > 0) {
            $existingStatement = $db->prepare('SELECT image_path FROM reviews WHERE id = ?');
            $existingStatement->execute([$id]);
            $existingReview = $existingStatement->fetch();
            if (!$existingReview) {
                json_response(['success' => false, 'message' => '수정할 후기를 찾을 수 없습니다.'], 404);
            }
            $existingImagePath = $existingReview['image_path'];
        }

        $imagePath = $existingImagePath;
        $newImagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK || $_FILES['image']['size'] > 5 * 1024 * 1024) {
                json_response(['success' => false, 'message' => '5MB 이하 이미지를 선택해 주세요.'], 422);
            }
            $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['image']['tmp_name']);
            if (!isset($allowedTypes[$mime])) {
                json_response(['success' => false, 'message' => 'JPG, PNG, WebP, GIF 이미지만 등록할 수 있습니다.'], 422);
            }
            $uploadDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'reviews';
            if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
                throw new RuntimeException('이미지 저장 폴더를 만들 수 없습니다.');
            }
            $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDirectory . DIRECTORY_SEPARATOR . $filename)) {
                throw new RuntimeException('이미지를 저장할 수 없습니다.');
            }
            $imagePath = 'uploads/reviews/' . $filename;
            $newImagePath = $imagePath;
        }

        $values = [$title, $content, $author, $grade, $rating, $imagePath, !empty($payload['is_visible']) ? 1 : 0];
        if ($id > 0) {
            $statement = $db->prepare(
                'UPDATE reviews SET title = ?, content = ?, author = ?, student_grade = ?, rating = ?, image_path = ?, is_visible = ? WHERE id = ?'
            );
            $statement->execute([...$values, $id]);
            if ($newImagePath && $existingImagePath) {
                $fullPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $existingImagePath);
                $uploadRoot = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'reviews');
                $resolvedFile = realpath($fullPath);
                if ($uploadRoot && $resolvedFile && str_starts_with($resolvedFile, $uploadRoot . DIRECTORY_SEPARATOR)) {
                    unlink($resolvedFile);
                }
            }
            json_response(['success' => true, 'message' => '후기가 수정되었습니다.']);
        }

        $statement = $db->prepare(
            'INSERT INTO reviews (title, content, author, student_grade, rating, image_path, is_visible) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute($values);
        json_response(['success' => true, 'message' => '후기가 등록되었습니다.', 'id' => (int) $db->lastInsertId()], 201);
    }

    if ($method === 'DELETE') {
        $id = (int) ($payload['id'] ?? 0);
        if ($id < 1) {
            json_response(['success' => false, 'message' => '삭제할 후기를 확인해 주세요.'], 422);
        }
        $imageStatement = $db->prepare('SELECT image_path FROM reviews WHERE id = ?');
        $imageStatement->execute([$id]);
        $imagePath = $imageStatement->fetchColumn();
        $statement = $db->prepare('DELETE FROM reviews WHERE id = ?');
        $statement->execute([$id]);
        if ($imagePath) {
            $fullPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imagePath);
            $uploadRoot = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'reviews');
            $resolvedFile = realpath($fullPath);
            if ($uploadRoot && $resolvedFile && str_starts_with($resolvedFile, $uploadRoot . DIRECTORY_SEPARATOR)) {
                unlink($resolvedFile);
            }
        }
        json_response(['success' => true, 'message' => '후기가 삭제되었습니다.']);
    }

    json_response(['success' => false, 'message' => '지원하지 않는 요청입니다.'], 405);
} catch (Throwable $error) {
    json_response(['success' => false, 'message' => '후기 데이터를 처리하지 못했습니다.'], 500);
}
