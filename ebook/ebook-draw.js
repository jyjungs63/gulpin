/**
 * ========================================
 * EPLAT Ebook - 그리기 기능
 * ========================================
 */

// ========================================
// 그리기 관련 전역 변수
// ========================================
let isDrawing = false;
let currentTool = "pen";
let drawingData = [];
let currentColor = "#000000";
let currentSize = 3;
let lastX = 0;
let lastY = 0;

// 터치 관련 변수
let touchStartX = 0;
let touchEndX = 0;
let isTwoFingerSwipe = false;

// ========================================
// 캔버스 이벤트 리스너 설정
// ========================================
function setupCanvasEventListeners() {
    document.querySelectorAll(".drawing-canvas").forEach((canvas) => {
        // 기존 이벤트 리스너 제거 (중복 방지)
        removeCanvasEventListeners(canvas);
        
        // 기기에 맞는 이벤트 리스너 추가
        if (window.PointerEvent) {
            // 포인터 이벤트 지원 (모던 브라우저)
            canvas.addEventListener("pointerdown", startDrawing);
            canvas.addEventListener("pointermove", draw);
            canvas.addEventListener("pointerup", stopDrawing);
            canvas.addEventListener("pointerout", stopDrawing);
        } else {
            // 포인터 이벤트 미지원 시 마우스 + 터치 이벤트 사용
            canvas.addEventListener("mousedown", startDrawing);
            canvas.addEventListener("mousemove", draw);
            canvas.addEventListener("mouseup", stopDrawing);
            canvas.addEventListener("mouseout", stopDrawing);
            
            canvas.addEventListener("touchstart", startDrawing, {passive: false});
            canvas.addEventListener("touchmove", draw, {passive: false});
            canvas.addEventListener("touchend", stopDrawing);
            canvas.addEventListener("touchcancel", stopDrawing);
        }
    });
}

// ========================================
// 캔버스 이벤트 리스너 제거
// ========================================
function removeCanvasEventListeners(canvas) {
    // 포인터 이벤트 제거
    canvas.removeEventListener("pointerdown", startDrawing);
    canvas.removeEventListener("pointermove", draw);
    canvas.removeEventListener("pointerup", stopDrawing);
    canvas.removeEventListener("pointerout", stopDrawing);
    
    // 마우스 이벤트 제거
    canvas.removeEventListener("mousedown", startDrawing);
    canvas.removeEventListener("mousemove", draw);
    canvas.removeEventListener("mouseup", stopDrawing);
    canvas.removeEventListener("mouseout", stopDrawing);
    
    // 터치 이벤트 제거
    canvas.removeEventListener("touchstart", startDrawing);
    canvas.removeEventListener("touchmove", draw);
    canvas.removeEventListener("touchend", stopDrawing);
    canvas.removeEventListener("touchcancel", stopDrawing);
}

// ========================================
// 그리기 시작
// ========================================
function startDrawing(e) {
    let clientX, clientY;
    
    // 터치 이벤트 처리
    if (e.type === 'touchstart' || e.pointerType === 'touch') {
        if (e.touches && e.touches.length > 1) {
            return; // 멀티 터치 방지
        }
        clientX = e.touches ? e.touches[0].clientX : e.clientX;
        clientY = e.touches ? e.touches[0].clientY : e.clientY;
    } else {
        // 마우스 이벤트 처리
        clientX = e.clientX;
        clientY = e.clientY;
    }
    
    isDrawing = true;
    
    const rect = e.target.getBoundingClientRect();
    lastX = clientX - rect.left;
    lastY = clientY - rect.top;
    
    // 캔버스 설정
    const canvas = e.target;
    const ctx = canvas.getContext("2d");
    ctx.lineJoin = "round";
    ctx.lineCap = "round";
    ctx.lineWidth = currentSize;
    
    // 도구 설정
    if (currentTool === "pen") {
        ctx.strokeStyle = currentColor;
        ctx.globalCompositeOperation = "source-over";
    } else if (currentTool === "eraser") {
        ctx.strokeStyle = "#FFFFFF";
        ctx.globalCompositeOperation = "destination-out";
    }
    
    // 초기 점 그리기
    ctx.beginPath();
    ctx.arc(lastX, lastY, currentSize / 2, 0, Math.PI * 2);
    ctx.fill();
    
    // 데이터 저장
    const pageIndex = parseInt(canvas.dataset.pageIndex);
    if (!drawingData[pageIndex]) {
        drawingData[pageIndex] = [];
    }
    
    drawingData[pageIndex].push({
        tool: currentTool,
        color: currentColor,
        size: currentSize,
        startX: lastX,
        startY: lastY,
        endX: lastX,
        endY: lastY,
        isDot: true
    });
}

// ========================================
// 그리기
// ========================================
function draw(e) {
    if (!isDrawing) return;
    
    let clientX, clientY;
    
    // 터치 이벤트 처리
    if (e.type === 'touchmove' || e.pointerType === 'touch') {
        e.preventDefault();
        clientX = e.touches ? e.touches[0].clientX : e.clientX;
        clientY = e.touches ? e.touches[0].clientY : e.clientY;
    } else {
        // 마우스 이벤트 처리
        clientX = e.clientX;
        clientY = e.clientY;
    }
    
    const canvas = e.target;
    const ctx = canvas.getContext("2d");
    const rect = canvas.getBoundingClientRect();
    
    const x = clientX - rect.left;
    const y = clientY - rect.top;
    
    // 너무 작은 움직임은 무시 (떨림 방지)
    const dx = Math.abs(x - lastX);
    const dy = Math.abs(y - lastY);
    if (dx + dy < 2) {
        return;
    }
    
    const pageIndex = parseInt(canvas.dataset.pageIndex);
    
    if (!drawingData[pageIndex]) {
        drawingData[pageIndex] = [];
    }
    
    // 캔버스 설정
    ctx.lineJoin = "round";
    ctx.lineCap = "round";
    ctx.lineWidth = currentSize;
    
    if (currentTool === "pen") {
        ctx.strokeStyle = currentColor;
        ctx.globalCompositeOperation = "source-over";
    } else if (currentTool === "eraser") {
        ctx.strokeStyle = "#FFFFFF";
        ctx.globalCompositeOperation = "destination-out";
    }
    
    // 선 그리기
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(x, y);
    ctx.stroke();
    
    // 데이터 저장
    drawingData[pageIndex].push({
        tool: currentTool,
        color: currentColor,
        size: currentSize,
        startX: lastX,
        startY: lastY,
        endX: x,
        endY: y,
        isDot: false
    });
    
    lastX = x;
    lastY = y;
}

// ========================================
// 그리기 종료
// ========================================
function stopDrawing(e) {
    if (isDrawing) {
        isDrawing = false;
        lastX = 0;
        lastY = 0;
    }
}

// ========================================
// 캔버스 다시 그리기
// ========================================
function redrawCanvas(canvas, pageData) {
    const ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    pageData.forEach((stroke) => {
        ctx.lineJoin = "round";
        ctx.lineCap = "round";
        ctx.lineWidth = stroke.size;
        
        if (stroke.tool === "pen") {
            ctx.strokeStyle = stroke.color;
            ctx.globalCompositeOperation = "source-over";
        } else if (stroke.tool === "eraser") {
            ctx.strokeStyle = "#FFFFFF";
            ctx.globalCompositeOperation = "destination-out";
        }
        
        if (stroke.isDot) {
            // 점 그리기
            ctx.beginPath();
            ctx.arc(stroke.startX, stroke.startY, stroke.size / 2, 0, Math.PI * 2);
            ctx.fill();
        } else {
            // 선 그리기
            ctx.beginPath();
            ctx.moveTo(stroke.startX, stroke.startY);
            ctx.lineTo(stroke.endX, stroke.endY);
            ctx.stroke();
        }
    });
}

// ========================================
// 현재 캔버스 지우기
// ========================================
function clearCurrentCanvas() {
    const visiblePages = $("#flipbook").turn("view");
    
    visiblePages.forEach((pageNum) => {
        if (pageNum > 0 && pageNum <= images.length) {
            const index = pageNum - 1;
            const canvas = document.querySelector(
                `.drawing-canvas[data-page-index="${index}"]`
            );
            
            if (canvas) {
                const ctx = canvas.getContext("2d");
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                drawingData[index] = [];
            }
        }
    });
}

// ========================================
// 모든 블러 효과 제거
// ========================================
function removeAllBlur() {
    // 1. 모든 이미지 선택
    const allImages = document.querySelectorAll(
        "#flipbook img, .turn-page img, .page img, .odd img, .even img, .p img, div[style*='z-index'] img"
    );
    
    // 2. 블러 클래스 및 스타일 제거
    allImages.forEach((img) => {
        img.classList.remove("blurred");
        img.style.filter = "none";
    });
    
    // 3. 모든 페이지 순회
    const totalPages = images.length;
    for (let i = 0; i < totalPages; i++) {
        try {
            const page = $("#flipbook").turn("pages")[i];
            if (page) {
                const pageImgs = page.querySelectorAll("img");
                pageImgs.forEach(img => {
                    img.classList.remove("blurred");
                    img.style.filter = "none";
                });
            }
        } catch (e) {
            // 오류 무시하고 계속 진행
        }
    }
    
    // 4. DOM 재귀 순회
    function removeBlurFromAllImages(element) {
        if (element.tagName && element.tagName.toLowerCase() === 'img') {
            element.classList.remove("blurred");
            element.style.filter = "none";
        }
        
        if (element.childNodes && element.childNodes.length > 0) {
            element.childNodes.forEach(child => {
                removeBlurFromAllImages(child);
            });
        }
    }
    
    removeBlurFromAllImages(document.getElementById("flipbook"));
    
    // 5. CSS 전역 해결책
    const style = document.createElement('style');
    style.innerHTML = `
        #flipbook img, .turn-page img, .page img {
            filter: none !important;
            -webkit-filter: none !important;
        }
        .blurred {
            filter: none !important;
            -webkit-filter: none !important;
        }
    `;
    document.head.appendChild(style);
    
    console.log("블러 제거 완료");
}
