/**
 * ========================================
 * EPLAT Ebook - Single/Double 페이지 모드 옵션
 * ========================================
 */

// ========================================
// 오디오 캐시
// ========================================
const audioCache = new Map();
let nextPageAudioPreloaded = false;

// ========================================
// 페이지 모드 설정 (옵션)
// ========================================
// displayMode: "single" 또는 "double" (기본값: "double")
// ebook-config.js에서 설정 가능
let pageDisplayMode = typeof displayMode !== 'undefined' ? displayMode : "double";

//console.log(`📖 페이지 모드: ${pageDisplayMode}`);

// ========================================
// 페이지 설정
// ========================================
function setupPages() {
    flipbook.innerHTML = "";
    let pageDisplayMode = typeof displayMode !== 'undefined' ? displayMode : "double";
    // 그리기 데이터 초기화
    drawingData = Array(images.length).fill().map(() => []);
    
    // ========================================
    // double 모드일 때만 더미 페이지 추가
    // ========================================
    let useDummyPage = false;
    
    if (pageDisplayMode === "double") {
        const dummyPage = document.createElement("div");
        dummyPage.className = "page";
        dummyPage.style.backgroundColor = "transparent";
        flipbook.appendChild(dummyPage);
        useDummyPage = true;
        console.log("✅ 더미 페이지 추가 (double 모드)");
    }
    
    // 페이지 생성
    images.forEach((imgSrc, index) => {
        const page = document.createElement("div");
        
        // ========================================
        // 페이지 클래스 설정 (모드에 따라 다르게)
        // ========================================
        if (pageDisplayMode === "double") {
            // double 모드: left/right 구분
            page.className = (index + 1) % 2 === 0 ? "page right" : "page left";
        } else {
            // single 모드: 모두 동일
            page.className = "page";
        }
        
        // 이미지 생성
        const img = document.createElement("img");
        img.src = imgSrc;
        img.alt = `Page ${index + 1}`;
        
        // 캔버스 생성
        const canvas = document.createElement("canvas");
        canvas.className = "drawing-canvas";
        canvas.dataset.pageIndex = index;
        
        page.appendChild(img);
        page.appendChild(canvas);
        
        // 동영상 링크 버튼 추가
        if (videoLinks[index]) {
            const videoLink = document.createElement("a");
            videoLink.href = videoLinks[index];
            videoLink.target = "_blank";
            videoLink.className = "video-link-button";
            videoLink.innerText = "▶ 동영상 보기";
            page.appendChild(videoLink);
        }
        
        flipbook.appendChild(page);
        
        // 이미지 로드 후 캔버스 크기 조정
        img.onload = () => resizeCanvas(canvas, img);
    });
    
    // ========================================
    // turn.js 초기화 (모드에 따라 다른 설정)
    // ========================================
    const turnOptions = {
        width: bookContainer.clientWidth * 0.9,
        height: bookContainer.clientHeight * 0.9,
        autoCenter: true,
        display: pageDisplayMode,  // ✨ "single" 또는 "double"
        acceleration: true,
        gradients: true,
        page: useDummyPage ? 2 : 1,  // ✨ double이면 2, single이면 1
        when: {
            turning: function(event, page, view) {
                console.log(`════════════════════════════════════`);
                console.log(`[TURNING] page: ${page}, view: [${view}]`);
                
                let actualPage;
                
                // ========================================
                // 모드에 따라 다른 페이지 계산
                // ========================================
                if (pageDisplayMode === "single") {
                    // single 모드: page 값이 실제 페이지 번호
                    actualPage = page;
                    console.log(`[single 모드] actualPage: ${actualPage}`);
                } else {
                    // double 모드: view 배열에서 최소값 계산
                    actualPage = currentPage;
                    
                    if (view && view.length > 0) {
                        const realPages = view.filter(p => p > 1).map(p => p - 1);
                        console.log(`view에서 실제 페이지들: [${realPages}]`);
                        
                        if (realPages.length > 0) {
                            actualPage = Math.min(...realPages);
                        }
                    }
                    console.log(`[double 모드] actualPage: ${actualPage}`);
                }
                
                console.log(`════════════════════════════════════`);
                
                currentPage = actualPage;
                updatePageIndicator();
                updateNavButtons();
                setupCanvasEventListeners();
                
                // 다음 페이지 오디오 프리로드
                preloadNextAudio(currentPage);
            },
            turned: function(event, page, view) {
                console.log(`[TURNED] page: ${page}, view: [${view}]`);
                
                let actualPage;
                
                // ========================================
                // 모드에 따라 다른 페이지 계산
                // ========================================
                if (pageDisplayMode === "single") {
                    actualPage = page;
                } else {
                    actualPage = currentPage;
                    
                    if (view && view.length > 0) {
                        const realPages = view.filter(p => p > 1).map(p => p - 1);
                        if (realPages.length > 0) {
                            actualPage = Math.min(...realPages);
                        }
                    }
                }
                
                // turning에서 놓친 경우를 위한 백업
                if (currentPage !== actualPage) {
                    console.log(`⚠️ currentPage 불일치! ${currentPage} → ${actualPage}`);
                    currentPage = actualPage;
                    updatePageIndicator();
                    updateNavButtons();
                }
                
                document.querySelectorAll(".drawing-canvas").forEach((canvas) => {
                    const img = canvas.previousElementSibling;
                    resizeCanvas(canvas, img);
                });
            }
        }
    };
    
    $("#flipbook").turn(turnOptions);
    
    setupEventListeners();
    setupCanvasEventListeners();
    addKeyboardNavigation();
    setupPageNavigation();
    setupTouchEvents();
    
    // 초기 페이지 설정
    currentPage = 1;
    updatePageIndicator();
    updateNavButtons();
    
    // 첫 페이지 및 다음 페이지 오디오 프리로드
    preloadAudioForPage(1);
    preloadAudioForPage(2);
}

// ========================================
// 오디오 프리로딩 함수
// ========================================

/**
 * 특정 페이지의 오디오 프리로드
 */
function preloadAudioForPage(pageNumber) {
    if (!imgpath || !sound) {
        return;
    }
    
    const audioSrc = `${imgpath}${pageNumber}.${sound}`;
    
    // 이미 캐시되어 있으면 스킵
    if (audioCache.has(audioSrc)) {
        return;
    }
    
    const tempAudio = new Audio();
    tempAudio.preload = "auto";
    tempAudio.src = audioSrc;
    tempAudio.load();
    
    // 캐시에 저장
    audioCache.set(audioSrc, tempAudio);
    
    console.log(`✅ 오디오 프리로드: ${audioSrc}`);
}

/**
 * 다음 페이지 오디오 프리로드 (양방향)
 */
function preloadNextAudio(currentPageNumber) {
    const totalPages = images.length;
    
    // 다음 페이지 프리로드
    const nextPage = currentPageNumber + 1;
    if (nextPage <= totalPages) {
        preloadAudioForPage(nextPage);
    }
    
    // 이전 페이지도 프리로드 (뒤로가기 대비)
    const prevPage = currentPageNumber - 1;
    if (prevPage >= 1) {
        preloadAudioForPage(prevPage);
    }
}

// ========================================
// 오디오 플레이어 활성/비활성 헬퍼
// ========================================
function enableAudioPlayer() {
    const player = document.querySelector('.toolbar-group.audio-player');
    if (player) player.classList.remove('disabled');
}

function disableAudioPlayer() {
    const player = document.querySelector('.toolbar-group.audio-player');
    if (player) player.classList.add('disabled');
    if (typeof audio !== 'undefined' && audio) {
        if (!audio.paused) {
            audio.pause();
        }
        // src를 완전히 제거 → canplay/loadedmetadata stale 이벤트 차단
        // audioPlayer2.js의 emptied/loadstart 핸들러가 UI를 0:00으로 초기화함
        audio.removeAttribute('src');
        audio.load();
    }
}

/**
 * 지원 오디오 확장자 목록 (우선순위 순)
 */
const AUDIO_EXTENSIONS = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac'];

/**
 * 표시 페이지 번호를 실제 파일 번호로 변환
 * page=5-9 사용 시: 표시 1→파일5, 표시 2→파일6, ...
 */
function getActualPageNumber(displayPage) {
    if (pageIndicesMap && pageIndicesMap.length > 0 && displayPage >= 1 && displayPage <= pageIndicesMap.length) {
        return pageIndicesMap[displayPage - 1]; // 0-based index
    }
    return displayPage; // 매핑 없으면 그대로
}

/**
 * 페이지 번호에 대한 오디오 경로 후보 배열 반환
 * sound 가 감지되어 있으면 그 포맷만, 없으면 모든 포맷 시도
 */
function getAudioCandidates(pageNumber) {
    const actualPage = getActualPageNumber(pageNumber);
    const exts = (typeof sound === 'string' && sound)
        ? [sound]          // 자동 감지된 포맷 1가지
        : AUDIO_EXTENSIONS; // 미감지시 전체 포맷 순차 시도
    return exts.map(ext => `${imgpath}${actualPage}.${ext}`);
}

/**
 * 오디오 소스 변경 - 현재·다음 페이지, 모든 지원 포맷 순서대로 시도
 */
function changeAudioSource(pageNumber) {
    console.log(`\n🔊 changeAudioSource: 페이지 ${pageNumber}`);

    if (!imgpath) {
        console.warn('❌ imgpath 없음 → 오디오 비활성화');
        disableAudioPlayer();
        return;
    }

    // 시도할 후보 목록: 현재 페이지의 지원 포맷만
    const candidates = getAudioCandidates(pageNumber);

    disableAudioPlayer();

    // 후보를 순서대로 시도해 처음 로드되는 파일을 사용
    function tryNext(index) {
        if (index >= candidates.length) {
            console.log('ℹ️ 모든 후보에서 오디오 파일 없음 → 플레이어 비활성화');
            return;
        }
        const src = candidates[index];
        const t = new Audio();
        t.src = src;
        t.addEventListener('loadedmetadata', function () {
            console.log(`✅ 오디오 발견: ${src}`);
            enableAudioPlayer();
            loadAudio(src, pageNumber);
        }, { once: true });
        t.addEventListener('error', function () {
            tryNext(index + 1);
        }, { once: true });
    }

    setTimeout(() => tryNext(0), 50);
}

/**
 * 실제 오디오 로드
 */
function loadAudio(audioSrc, actualPage) {
    // 기존 오디오 정지
    if (audio) {
        audio.pause();
        audio.currentTime = 0;
    }
    
    // 오디오 소스 변경
    audio.src = audioSrc;
    
    // 캐시에 없으면 프리로드
    if (!audioCache.has(audioSrc)) {
        const tempAudio = new Audio();
        tempAudio.preload = "auto";
        tempAudio.src = audioSrc;
        tempAudio.load();
        audioCache.set(audioSrc, tempAudio);
    }
    
    // 메타데이터 로드
    audio.load();
    
    // 로드 완료 확인
    audio.addEventListener('loadedmetadata', function onLoaded() {
        console.log(`✅ 오디오 로드 완료: ${audioSrc} (${audio.duration}초)`);
        audio.removeEventListener('loadedmetadata', onLoaded);
    });
    
    audio.addEventListener('error', function onError(e) {
        console.error(`❌ 오디오 로드 실패: ${audioSrc}`);
        audio.removeEventListener('error', onError);
    });
}

// ========================================
// 캔버스 크기 조정
// ========================================
function resizeCanvas(canvas, img) {
    const page = canvas.parentElement;
    canvas.width = page.clientWidth;
    canvas.height = page.clientHeight;
    
    const pageIndex = parseInt(canvas.dataset.pageIndex);
    if (drawingData[pageIndex] && drawingData[pageIndex].length > 0) {
        redrawCanvas(canvas, drawingData[pageIndex]);
    }
}

// ========================================
// 이벤트 리스너 설정
// ========================================
function setupEventListeners() {
    // 페이지 네비게이션 버튼
    prevButton.addEventListener("click", previousPage);
    nextButton.addEventListener("click", nextPage);
    
    // 도구 버튼
    document.querySelectorAll(".tool-button").forEach((button) => {
        button.addEventListener("click", function() {
            document.querySelectorAll(".tool-button").forEach((btn) => {
                btn.classList.remove("active");
            });
            this.classList.add("active");
            currentTool = this.id.replace("-tool", "");
        });
    });
    
    // 기능 버튼
    document.getElementById("clear-canvas").addEventListener("click", clearCurrentCanvas);
    removeBlurButton.addEventListener("click", removeAllBlur);
    document.getElementById("fullscreen-button").addEventListener("click", toggleFullScreen);
    const printButton = document.getElementById("print-button");
    if (PRINT_ENABLED) {
        printButton.hidden = false;
        printButton.addEventListener("click", printAllPages);
    }
    
    // 색상 및 크기 조절
    colorPicker.addEventListener("input", function() {
        currentColor = this.value;
    });
    
    sizeSlider.addEventListener("input", function() {
        currentSize = parseInt(this.value);
    });
    
    // 창 크기 변경
    window.addEventListener("resize", function() {
        $("#flipbook").turn(
            "size",
            bookContainer.clientWidth * 0.9,
            bookContainer.clientHeight * 0.9
        );
        
        document.querySelectorAll(".drawing-canvas").forEach((canvas) => {
            const img = canvas.previousElementSibling;
            resizeCanvas(canvas, img);
        });
    });
}

// ========================================
// 페이지 네비게이션 설정
// ========================================
function setupPageNavigation() {
    goToPageButton.addEventListener("click", navigateToPage);
    
    pageInput.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            navigateToPage();
        }
    });
}

// ========================================
// 특정 페이지로 이동
// ========================================
function navigateToPage() {
    const pageNumber = parseInt(pageInput.value);
    totalPages = images.length;
    
    if (pageNumber && pageNumber >= 1 && pageNumber <= totalPages) {
        // ========================================
        // 모드에 따라 다른 페이지 번호 계산
        // ========================================
        let turnPageNumber;
        
        if (pageDisplayMode === "double") {
            // double 모드: 더미 페이지 고려
            turnPageNumber = pageNumber + 1;
            console.log(`📌 [double] 페이지 이동: 입력 ${pageNumber} → turn.js ${turnPageNumber}`);
        } else {
            // single 모드: 페이지 번호 그대로
            turnPageNumber = pageNumber;
            console.log(`📌 [single] 페이지 이동: ${pageNumber}`);
        }
        
        $("#flipbook").turn("page", turnPageNumber);
        pageInput.value = "";
        
        // 캔버스 크기 조정 및 이벤트 리스너 설정
        setTimeout(() => {
            document.querySelectorAll(".drawing-canvas").forEach((canvas) => {
                const img = canvas.previousElementSibling;
                resizeCanvas(canvas, img);
            });
            setupCanvasEventListeners();
        }, 100);
    } else {
        alert(`Please enter a valid page number between 1 and ${totalPages}`);
    }
}

// ========================================
// 페이지 이동 함수
// ========================================
function nextPage() {
    console.log(`\n▶️ 다음 페이지 버튼 - 현재: ${currentPage}`);
    $("#flipbook").turn("next");
}

function previousPage() {
    console.log(`\n◀️ 이전 페이지 버튼 - 현재: ${currentPage}`);
    $("#flipbook").turn("previous");
}

// ========================================
// 페이지 인디케이터 업데이트
// ========================================
function updatePageIndicator() {
    totalPages = images.length;
    pageIndicator.textContent = `${currentPage} / ${totalPages}`;
    
    console.log(`📄 페이지: ${currentPage} / ${totalPages}`);
    
    // 오디오 소스 변경
    changeAudioSource(currentPage);
}

// ========================================
// 네비게이션 버튼 업데이트
// ========================================
function updateNavButtons() {
    totalPages = images.length;
    
    prevButton.style.visibility = currentPage > 1 ? "visible" : "hidden";
    nextButton.style.visibility = currentPage < totalPages ? "visible" : "hidden";
}

// ========================================
// 터치 이벤트 설정
// ========================================
function setupTouchEvents() {
    // 터치 시작
    document.addEventListener("touchstart", function(e) {
        if (e.target.classList.contains("drawing-canvas")) {
            if (e.touches.length === 1) {
                e.preventDefault();
            }
            
            // 2개 손가락 스와이프 감지
            if (e.touches.length === 2) {
                isTwoFingerSwipe = true;
                touchStartX = (e.touches[0].screenX + e.touches[1].screenX) / 2;
            }
        }
    }, { passive: false });
    
    // 터치 이동
    document.addEventListener("touchmove", function(e) {
        if (
            e.target.classList.contains("drawing-canvas") &&
            isTwoFingerSwipe &&
            e.touches.length === 2
        ) {
            e.preventDefault();
        }
    }, { passive: false });
    
    // 터치 종료
    document.addEventListener("touchend", function(e) {
        if (
            e.target.classList.contains("drawing-canvas") &&
            isTwoFingerSwipe &&
            e.changedTouches.length > 0
        ) {
            touchEndX = e.changedTouches[0].clientX;
            handleTwoFingerSwipe();
            isTwoFingerSwipe = false;
        }
    }, { passive: false });
}

// ========================================
// 2개 손가락 스와이프 처리
// ========================================
function handleTwoFingerSwipe() {
    const swipeThreshold = 50;
    
    if (touchEndX < touchStartX - swipeThreshold) {
        nextPage();
    }
    if (touchEndX > touchStartX + swipeThreshold) {
        previousPage();
    }
}

// ========================================
// 키보드 네비게이션
// ========================================
function addKeyboardNavigation() {
    document.addEventListener("keydown", function(e) {
        // 왼쪽 화살표
        if (e.key === "ArrowLeft" || e.keyCode === 37) {
            e.preventDefault();
            previousPage();
        }
        
        // 오른쪽 화살표
        if (e.key === "ArrowRight" || e.keyCode === 39) {
            e.preventDefault();
            nextPage();
        }
    });
    
    console.log("✅ 키보드 방향키 활성화");
}

// ========================================
// 전체 화면 토글
// ========================================
function toggleFullScreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch((err) => {
            alert(`Error attempting to enable fullscreen: ${err.message}`);
        });
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}

// ========================================
// 전체 페이지 인쇄
// ========================================
function printAllPages() {
    if (!PRINT_ENABLED || images.length === 0) return;

    const printWindow = window.open("", "_blank");
    if (!printWindow) {
        alert("인쇄 창을 열 수 없습니다. 팝업 차단을 해제해 주세요.");
        return;
    }

    printWindow.document.title = "Ebook Print";
    const style = printWindow.document.createElement("style");
    style.textContent = `
        @page { margin: 0; }
        body { margin: 0; }
        .print-page { break-after: page; page-break-after: always; }
        .print-page:last-child { break-after: auto; page-break-after: auto; }
        img { display: block; width: 100%; height: auto; }
    `;
    printWindow.document.head.appendChild(style);

    const loading = images.map((src) => new Promise((resolve) => {
        const page = printWindow.document.createElement("div");
        page.className = "print-page";
        const image = printWindow.document.createElement("img");
        image.onload = resolve;
        image.onerror = resolve;
        image.src = new URL(src, window.location.href).href;
        page.appendChild(image);
        printWindow.document.body.appendChild(page);
    }));

    Promise.all(loading).then(() => {
        printWindow.focus();
        printWindow.print();
    });
}

// ========================================
// 오디오 캐시 정리 (메모리 관리)
// ========================================
function clearOldAudioCache() {
    const keepRange = 3;
    
    audioCache.forEach((audio, src) => {
        const pageMatch = src.match(/\/(\d+)\./);
        if (pageMatch) {
            const pageNum = parseInt(pageMatch[1]);
            if (Math.abs(pageNum - currentPage) > keepRange) {
                audioCache.delete(src);
            }
        }
    });
}

// 주기적으로 캐시 정리
setInterval(clearOldAudioCache, 30000);
