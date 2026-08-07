<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chaitalk Ebook</title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="ebook.css?v=<?= filemtime('ebook.css') ?>" />
</head>

<body>
    <!-- 화면 회전 안내 -->
    <div class="rotation-notice">
        <p>Chaitalk EBOOK을 보려면 <br /><br />기기를 가로로 돌려주세요 📱</p>
    </div>

    <!-- 상단 도구 모음 -->
    <div class="toolbar center-box">
        <!-- 드로잉 도구 그룹 -->
        <div class="toolbar-group">
            <button id="pen-tool" class="tool-button active">
                <span class="button-icon">✏️</span>Pen
            </button>
            <button id="eraser-tool" class="tool-button">
                <span class="button-icon">🧽</span>Eraser
            </button>
            
            <!-- 페이지 네비게이션 -->
            <div class="page-navigation">
                <input
                    type="number"
                    id="page-input"
                    min="1"
                    placeholder="Page #"
                    style="width: 70px; text-align: center"
                />
                <button id="go-to-page">Go</button>
            </div>
        </div>

        <!-- 색상 선택 그룹 -->
        <div class="toolbar-group color-picker-container">
            <label for="color-picker">Color:</label>
            <input
                type="color"
                id="color-picker"
                class="color-picker"
                value="#000000"
            />
        </div>

        <!-- 펜 크기 조절 그룹 -->
        <div class="toolbar-group size-slider-container">
            <label for="size-slider">Size:</label>
            <input
                type="range"
                id="size-slider"
                class="size-slider"
                min="1"
                max="20"
                value="3"
            />
        </div>

        <!-- 기능 버튼 그룹 -->
        <div class="toolbar-group">
            <button id="clear-canvas">
                <span class="button-icon">🗑️</span>Clear
            </button>
            <button id="remove-blur-button">
                <span class="button-icon">👁️</span>Remove
            </button>
            <button id="fullscreen-button">
                <span class="button-icon">⛶</span>Fullscreen
            </button>
            <button id="print-button" hidden>
                <span class="button-icon">🖨️</span>Print
            </button>
        </div>

        <!-- 오디오 플레이어 -->
        <div class="toolbar-group audio-player disabled">
            <button class="play-button" id="playBtn">▶</button>
            <span class="time-display">
                <span id="currentTime">0:00</span> / <span id="duration">0:00</span>
            </span>
            <span class="progress-container" id="progressContainer">
                <span class="progress-bar" id="progressBar"></span>
                <span class="progress-handle" id="progressHandle"></span>
            </span>
            <button class="volume-button" id="volumeBtn">🔊</button>
            
            <!-- 오디오 태그 -->
            <audio 
                id="audio" 
                src="sounds/test.mp3"
                preload="metadata"
                crossorigin="anonymous"
            ></audio>
        </div>
        
        <button id="home-button">
            <span class="button-icon"></span>차이톡-북틀
        </button>
    </div>

    <!-- 책 컨테이너 -->
    <div class="book-container" id="book-container">
        <div id="flipbook"></div>
        <button class="nav-button prev-button" id="prev-button"><</button>
        <button class="nav-button next-button" id="next-button">></button>
        <div class="page-indicator" id="page-indicator">1 / 1</div>
    </div>

    <!-- External Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/3/turn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <!-- Application Scripts -->
    <!-- 중요: book-config.js를 가장 먼저 로드 local config config.js 정의 사용시 -->
    <!-- <script src="config.js"></script>
    <script src="book-config.js"></script> -->

    <!-- 1. 먼저 book-config 로드 from DB -->
    <script>
        (function restoreEbookParameters() {
            const url = new URL(window.location.href);
            const storedValue = sessionStorage.getItem('ebookBook');

            // 기존 URL 방식이 전달된 경우 해당 book을 기준값으로 유지한다.
            if (url.searchParams.has('book')) {
                sessionStorage.setItem('ebookBook', url.searchParams.get('book'));
                sessionStorage.setItem('ebookQueryString', url.search);
                return;
            }

            if (!storedValue) return;

            // assignbook()은 "?book=...&pdf=..." 형태를 저장할 수 있고,
            // 이전 호출부는 book 값만 저장할 수도 있으므로 두 형식을 모두 지원한다.
            if (storedValue.startsWith('?') || storedValue.includes('=')) {
                const storedParams = new URLSearchParams(storedValue.replace(/^\?/, ''));

                storedParams.forEach(function(value, key) {
                    if (!url.searchParams.has(key)) {
                        url.searchParams.set(key, value);
                    }
                });
            } else {
                url.searchParams.set('book', storedValue);
            }

            const book = url.searchParams.get('book');
            if (book) {
                sessionStorage.setItem('ebookBook', book);
                sessionStorage.setItem('ebookQueryString', url.search);
                console.log(url.search);
            }
        })();
    </script>
    <script src="book-config.php"></script>


    <!-- 2. 그 다음 ebook-config -->
    <script src="ebook-config.js?v=<?= filemtime('ebook-config.js') ?>"></script>
    <script src="audioPlayer.js?v=<?= filemtime('audioPlayer.js') ?>"></script>
    <script src="ebook-draw.js?v=<?= filemtime('ebook-draw.js') ?>"></script>
    <script src="ebook.js?v=<?= filemtime('ebook.js') ?>"></script>
</body>
</html>
