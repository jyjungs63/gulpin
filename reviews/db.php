<?php
declare(strict_types=1);

function review_db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dataDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'data';
    if (!is_dir($dataDirectory) && !mkdir($dataDirectory, 0775, true) && !is_dir($dataDirectory)) {
        throw new RuntimeException('데이터 저장 폴더를 만들 수 없습니다.');
    }

    $pdo = new PDO('sqlite:' . $dataDirectory . DIRECTORY_SEPARATOR . 'reviews.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            author TEXT NOT NULL,
            student_grade TEXT NOT NULL,
            rating INTEGER NOT NULL DEFAULT 5 CHECK (rating BETWEEN 1 AND 5),
            image_path TEXT DEFAULT NULL,
            is_visible INTEGER NOT NULL DEFAULT 1 CHECK (is_visible IN (0, 1)),
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    $columns = $pdo->query('PRAGMA table_info(reviews)')->fetchAll();
    if (!in_array('image_path', array_column($columns, 'name'), true)) {
        $pdo->exec('ALTER TABLE reviews ADD COLUMN image_path TEXT DEFAULT NULL');
    }

    $reviewCount = (int) $pdo->query('SELECT COUNT(*) FROM reviews')->fetchColumn();
    if ($reviewCount === 0) {
        $samples = [
            ['혼자 읽는 힘이 생겼어요', '책 읽기를 어려워하던 아이가 글핀 수업 후 스스로 문장을 읽고 뜻을 설명하기 시작했어요.', '김○○ 학부모', '초등 2학년'],
            ['어휘 자신감이 좋아졌어요', '그림과 이야기로 한자를 배우니 새 단어를 만날 때 뜻을 유추하는 재미가 생겼습니다.', '박○○ 학부모', '초등 3학년'],
            ['수업 시간이 기다려진대요', '아이 눈높이에 맞춘 활동이 많아서 집중도 잘하고 매주 수업을 기다립니다.', '이○○ 학부모', '초등 1학년'],
            ['학교 공부에도 도움이 돼요', '교과서 속 어려운 낱말을 이해하는 속도가 빨라지고 발표할 때도 자신감이 붙었어요.', '최○○ 학부모', '초등 4학년'],
            ['꼼꼼한 피드백이 만족스러워요', '아이의 강점과 보완할 부분을 구체적으로 알려주셔서 집에서도 학습을 이어가기 좋습니다.', '정○○ 학부모', '초등 5학년'],
        ];
        $insert = $pdo->prepare(
            'INSERT INTO reviews (title, content, author, student_grade, rating) VALUES (?, ?, ?, ?, 5)'
        );
        foreach ($samples as $sample) {
            $insert->execute($sample);
        }
    }

    return $pdo;
}
