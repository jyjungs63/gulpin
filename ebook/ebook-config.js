/**
 * ========================================
 * EPLAT Ebook - 설정 및 초기화 (displayMode DB 연동)
 * ========================================
 */

// ========================================
// 전역 변수
// ========================================
let images = [];
let pageIndicesMap = []; // 표시 페이지 → 실제 파일 번호 매핑
let currentPage = 0;
let totalPages = 1;
let book = "";
let dir = "";
let imgfomat;
let imgpath;
let sound;
let videoLinks = {};
let audio;
let bookInfo = null;

// ✨ displayMode는 데이터베이스에서 동적으로 로드됨
let displayMode = "single"; // 기본값

// ========================================
// URL에서 책 타입 추출 함수
// ========================================
function getBookTypeFromURL() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const bookParam = sessionStorage.getItem('ebookBook') || urlParams.get('book');
        
        if (!bookParam) {
            console.error('[getBookTypeFromURL] book 파라미터가 없습니다.');
            return null;
        }
        
        // 형식: phonics.step3.v1
        // 첫 번째 부분이 타입 코드
        const parts = bookParam.split('.');
        const typeCode = parts[0]; // phonics
        
        console.log('[getBookTypeFromURL] URL 파라미터:', bookParam);
        console.log('[getBookTypeFromURL] 추출된 타입 코드:', typeCode);
        
        return typeCode;
        
    } catch (error) {
        console.error('[getBookTypeFromURL] 오류:', error);
        return null;
    }
}

// ========================================
// 데이터베이스에서 displayMode 가져오기
// ========================================
async function loadDisplayModeFromDB(typeCode) {
    try {
        console.log('[loadDisplayModeFromDB] 타입 코드:', typeCode);
        
        const response = await fetch(`get_display_mode.php?type=${encodeURIComponent(typeCode)}`);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.data.display_mode) {
            displayMode = data.data.display_mode;
            console.log('✅ displayMode 로드 성공:', displayMode);
            console.log('📖 페이지 모드:', displayMode === 'single' ? '한 페이지씩' : '두 페이지');
            return displayMode;
        } else {
            console.warn('⚠️ displayMode를 찾을 수 없어 기본값 사용:', displayMode);
            return displayMode;
        }
        
    } catch (error) {
        console.error('[loadDisplayModeFromDB] 오류:', error);
        console.warn('⚠️ 기본값 사용:', displayMode);
        return displayMode;
    }
}

// ========================================
// URL에서 책 정보 가져오기 (에러 처리 강화)
// ========================================
function initializeBookFromURL() {
    console.log('[initializeBookFromURL] 시작');
    
    try {
        // BookConfig 존재 여부 확인
        if (typeof window.BookConfig === 'undefined') {
            console.error('[initializeBookFromURL] BookConfig가 로드되지 않았습니다!');
            alert('설정 파일을 로드할 수 없습니다. book-config.php 파일을 확인하세요.');
            return false;
        }
        
        // getBookDirectoryFromURL 함수 존재 확인
        if (typeof window.BookConfig.getBookDirectoryFromURL !== 'function') {
            console.error('[initializeBookFromURL] getBookDirectoryFromURL 함수가 없습니다!');
            return false;
        }
        
        const bookData = window.BookConfig.getBookDirectoryFromURL();
        console.log('[initializeBookFromURL] bookData:', bookData);
        
        // bookData 유효성 검사
        if (!bookData) {
            console.error('[initializeBookFromURL] bookData가 null입니다!');
            return false;
        }
        
        if (!bookData.directory) {
            console.error('[initializeBookFromURL] directory가 없습니다!');
            console.log('[initializeBookFromURL] bookData 상세:', JSON.stringify(bookData));
            return false;
        }
        
        if (!bookData.bookInfo) {
            console.error('[initializeBookFromURL] bookInfo가 없습니다!');
            return false;
        }
        
        // 값 할당
        dir = bookData.directory;
        bookInfo = bookData.bookInfo;
        book = bookInfo.fullName;
        
        console.log('[initializeBookFromURL] 설정 완료:', {
            dir: dir,
            book: book,
            bookInfo: bookInfo
        });
        
        // 한글 타입명 가져오기
        let typeName = bookInfo.type;
        if (typeof window.BookConfig.getBookTypeName === 'function') {
            typeName = window.BookConfig.getBookTypeName(bookInfo.type);
        }
        
        console.log("📚 책 로드 성공:", {
            이름: `${typeName} ${bookInfo.step} ${bookInfo.vol}권`,
            타입: bookInfo.type,
            경로: dir
        });
        
        return true;
        
    } catch (error) {
        console.error('[initializeBookFromURL] 예외 발생:', error);
        alert(`책 정보 로드 실패: ${error.message}`);
        return false;
    }
}

// ========================================
// DOM 요소 참조
// ========================================
const bookContainer = document.getElementById("book-container");
const flipbook = document.getElementById("flipbook");
const prevButton = document.getElementById("prev-button");
const nextButton = document.getElementById("next-button");
const pageIndicator = document.getElementById("page-indicator");
const colorPicker = document.getElementById("color-picker");
const sizeSlider = document.getElementById("size-slider");
const removeBlurButton = document.getElementById("remove-blur-button");
const pageInput = document.getElementById("page-input");
const goToPageButton = document.getElementById("go-to-page");

// ========================================
// JSON 데이터 로드 함수
// ========================================
async function loadJson(url) {
    try {
        console.log('[loadJson] 로딩:', url);
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        const data = await response.json();
        console.log('[loadJson] 성공:', url);
        return data;
    } catch (error) {
        console.error('[loadJson] 오류:', error);
        throw error;
    }
}

// ========================================
// 파일 개수 확인 함수
// ========================================
// 폴더 스캔 - 이미지·오디오 포맷 자동 감지
// ========================================
async function scanFolder(folderPath) {
    try {
        console.log('[scanFolder] 경로:', folderPath);
        const response = await fetch(
            `admin/scan_folder.php?path=${encodeURIComponent(folderPath)}`
        );
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();

        if (data.error) {
            // 서버 경로 해석 실패 → 클라이언트에서 직접 포맷 탐색
            console.warn('[scanFolder] 서버 경로 오류, 클라이언트 탐색으로 전환:', data.error);
            if (data._debug) console.debug('[scanFolder] 서버 디버그:', data._debug);
            return await detectFormatByFetch(folderPath);
        }

        console.log(`[scanFolder] 이미지: ${data.image_count}개 (${data.image_format}),`
            + ` 오디오: ${data.audio_format || '없음'}`);
        return data;
    } catch (error) {
        console.warn('[scanFolder] 오류, 클라이언트 탐색으로 전환:', error.message);
        return await detectFormatByFetch(folderPath);
    }
}

/**
 * PHP 스캔 실패 시 클라이언트에서 직접 HTTP HEAD 요청으로 포맷 탐색
 * (imgpath가 전체 URL일 때 사용)
 */
async function detectFormatByFetch(folderPath) {
    const IMG_EXTS   = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
    const AUDIO_EXTS = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
    const base = folderPath.replace(/\/?$/, '/');

    // 이미지 포맷 탐색: 1.{ext} 존재 여부 HEAD 요청
    let imageFormat = null;
    for (const ext of IMG_EXTS) {
        try {
            const res = await fetch(`${base}1.${ext}`, { method: 'HEAD' });
            if (res.ok) { imageFormat = ext; break; }
        } catch (_) {}
    }

    // 이미지 개수 추정: 존재하는 가장 큰 번호 탐색 (최대 200까지 이진 탐색)
    let imageCount = 0;
    if (imageFormat) {
        imageCount = await estimatePageCount(base, imageFormat, 200);
    }

    // 오디오 포맷 탐색: 1.{ext} 존재 여부 HEAD 요청
    let audioFormat = null;
    for (const ext of AUDIO_EXTS) {
        try {
            const res = await fetch(`${base}1.${ext}`, { method: 'HEAD' });
            if (res.ok) { audioFormat = ext; break; }
        } catch (_) {}
    }

    console.log(`[detectByFetch] 이미지: ${imageCount}개 (${imageFormat}), 오디오: ${audioFormat || '없음'}`);
    return { image_count: imageCount, image_format: imageFormat, audio_format: audioFormat };
}

/**
 * 이진 탐색으로 페이지 수 추정 (1~maxPages)
 */
async function estimatePageCount(base, ext, maxPages) {
    // 먼저 maxPages 존재 확인
    try {
        const res = await fetch(`${base}${maxPages}.${ext}`, { method: 'HEAD' });
        if (res.ok) return maxPages;
    } catch (_) {}

    let lo = 1, hi = maxPages;
    while (lo < hi) {
        const mid = Math.ceil((lo + hi) / 2);
        try {
            const res = await fetch(`${base}${mid}.${ext}`, { method: 'HEAD' });
            if (res.ok) lo = mid; else hi = mid - 1;
        } catch (_) { hi = mid - 1; }
    }
    return lo;
}

// ========================================
// 초기화 함수 (displayMode DB 연동)
// ========================================
async function init() {
    console.log('[init] 초기화 시작');
    
    try {
        // ========================================
        // ✨ 1. URL에서 책 타입 추출 및 displayMode 로드
        // ========================================
        const typeCode = getBookTypeFromURL();
        
        if (typeCode) {
            await loadDisplayModeFromDB(typeCode);
            console.log('✅ displayMode 설정 완료:', displayMode);
        } else {
            console.warn('⚠️ 타입 코드를 찾을 수 없어 기본 displayMode 사용:', displayMode);
        }
        
        // ========================================
        // 2. URL에서 책 정보 가져오기
        // ========================================
        const success = initializeBookFromURL();
        
        if (!success || !dir) {
            throw new Error('책 정보를 불러올 수 없습니다. URL 파라미터를 확인하세요.');
        }
        
        console.log('[init] 책 정보 로드 완료');
        console.log('[init] 디렉토리:', dir);
        
        // ========================================
        // 3. 기본 경로/포맷 초기화
        // ========================================
        images  = [];
        imgpath = dir;
        videoLinks = {};

        // ========================================
        // 4. 폴더 스캔 - 이미지·오디오 포맷 자동 감지
        // ========================================
        const scanResult = await scanFolder(imgpath);

        // 이미지 포맷: 자동 감지 → BookConfig 설정값 → 'png' 순으로 폴백
        imgfomat = scanResult.image_format
            || window.BookConfig.init.imgformat
            || 'png';

        // 오디오 포맷: 자동 감지 → BookConfig 설정값 → null 순으로 폴백
        sound = scanResult.audio_format
            || window.BookConfig.init.sound
            || null;

        console.log('[init] 자동 감지 결과:', {
            imgfomat,
            sound: sound || '(오디오 없음)',
            imgpath,
            displayMode
        });

        let numbook = scanResult.image_count;

        // 스캔은 성공했지만 이미지 개수 0 → 클라이언트 탐색으로 재시도
        if (!numbook || numbook === 0) {
            console.warn('[init] 스캔 결과 이미지 없음, 클라이언트 탐색 재시도...');
            const fallback = await detectFormatByFetch(imgpath);
            if (fallback.image_count > 0) {
                numbook         = fallback.image_count;
                imgfomat        = fallback.image_format || imgfomat;
                sound           = fallback.audio_format || sound;
                console.log('[init] 클라이언트 탐색 결과:', fallback);
            } else {
                throw new Error(`이미지 파일을 찾을 수 없습니다. 경로: ${imgpath}`);
            }
        }

        const num = Number(parseInt(numbook));
        
        // ========================================
        // 5. 이미지 경로 배열 생성 (page 파라미터 지원)
        //
        //   &page=5       → 5번 페이지만
        //   &page=5,7,9   → 5, 7, 9번 페이지만
        //   &page=5-9     → 5~9번 범위
        //   (없음)         → 전체 페이지
        // ========================================
        const urlParams = new URLSearchParams(window.location.search);
        const pageParam = (urlParams.get('page') || '').trim();

        let pageIndices = [];

        if (!pageParam) {
            // 전체 페이지
            for (let i = 1; i <= num; i++) pageIndices.push(i);
            console.log(`[init] 전체 페이지: 1 ~ ${num}`);

        } else if (pageParam.includes('-')) {
            // 범위 지정: page=5-9
            const parts = pageParam.split('-');
            const s = parseInt(parts[0].trim());
            const e = parseInt(parts[1].trim());
            if (!isNaN(s) && !isNaN(e) && s >= 1 && e >= s && e <= num) {
                for (let i = s; i <= e; i++) pageIndices.push(i);
                console.log(`[init] page 범위: ${s} ~ ${e}`);
            } else {
                console.warn('[init] page 범위 값이 유효하지 않아 전체 페이지 사용');
                for (let i = 1; i <= num; i++) pageIndices.push(i);
            }

        } else if (pageParam.includes(',')) {
            // 개별 지정: page=5,7,9
            const parsed = pageParam.split(',')
                .map(v => parseInt(v.trim()))
                .filter(v => !isNaN(v) && v >= 1 && v <= num);
            if (parsed.length > 0) {
                pageIndices = [...new Set(parsed)].sort((a, b) => a - b);
                console.log(`[init] page 개별 지정: ${pageIndices.join(', ')}`);
            } else {
                console.warn('[init] page 개별 값이 유효하지 않아 전체 페이지 사용');
                for (let i = 1; i <= num; i++) pageIndices.push(i);
            }

        } else {
            // 단일 페이지: page=5
            const single = parseInt(pageParam);
            if (!isNaN(single) && single >= 1 && single <= num) {
                pageIndices.push(single);
                console.log(`[init] 단일 페이지: ${single}`);
            } else {
                console.warn('[init] page 단일 값이 유효하지 않아 전체 페이지 사용');
                for (let i = 1; i <= num; i++) pageIndices.push(i);
            }
        }

        for (const idx of pageIndices) {
            images.push(`${imgpath}${idx}.${imgfomat}`);
        }
        pageIndicesMap = pageIndices; // 표시 페이지(0-based index) → 실제 파일 번호

        console.log(`[init] 총 ${images.length}페이지 로드 완료`);
        console.log('[init] 첫 번째 이미지:', images[0]);

        // ========================================
        // 6. 페이지 설정 (displayMode 사용)
        // ========================================
        if (typeof setupPages === 'function') {
            setupPages();  // ✨ 여기서 displayMode가 사용됨
        } else {
            console.error('[init] setupPages 함수가 없습니다!');
            throw new Error('setupPages 함수를 찾을 수 없습니다.');
        }

        if (typeof updateNavButtons === 'function') {
            updateNavButtons();
        }

        // ========================================
        // 7. 페이지 제목 업데이트
        // ========================================
        updatePageTitle();
        
        console.log('[init] 초기화 완료');
        console.log('📖 최종 페이지 모드:', displayMode);
        
    } catch (error) {
        console.error('[init] 초기화 오류:', error);
        videoLinks = {};
        
        // 사용자에게 알림
        const errorMsg = `
            책을 불러오는 중 오류가 발생했습니다.
            
            오류: ${error.message}
            
            다음을 확인하세요:
            1. URL 파라미터가 올바른지 (예: ?book=phonics.step3.v1)
            2. 데이터베이스가 실행 중인지
            3. get_display_mode.php 파일이 있는지
            4. 이미지 파일이 올바른 경로에 있는지
            
            콘솔(F12)에서 자세한 정보를 확인하세요.
        `;
        
        alert(errorMsg);
        
        // 에러 정보 표시
        if (bookContainer) {
            bookContainer.innerHTML = `
                <div style="padding: 40px; text-align: center; color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; margin: 20px;">
                    <h2>⚠️ 오류 발생</h2>
                    <p>${error.message}</p>
                    <p style="margin-top: 20px; font-size: 14px;">
                        <a href="?book=phonics.step3.v1" style="color: #004085;">기본 책으로 이동</a>
                    </p>
                </div>
            `;
        }
    }
}

// ========================================
// 페이지 제목 업데이트
// ========================================
function updatePageTitle() {
    if (!bookInfo) return;
    
    try {
        let typeName = bookInfo.type;
        if (typeof window.BookConfig !== 'undefined' && 
            typeof window.BookConfig.getBookTypeName === 'function') {
            typeName = window.BookConfig.getBookTypeName(bookInfo.type);
        }
        document.title = `${typeName} ${bookInfo.step} ${bookInfo.vol}권 - Eplat Ebook`;
    } catch (error) {
        console.error('[updatePageTitle] 오류:', error);
    }
}

// ========================================
// 책 정보 표시 함수
// ========================================
function displayBookInfo() {
    if (!bookInfo) {
        console.log('책 정보가 없습니다.');
        return;
    }
    
    try {
        let typeName = bookInfo.type;
        if (typeof window.BookConfig !== 'undefined' && 
            typeof window.BookConfig.getBookTypeName === 'function') {
            typeName = window.BookConfig.getBookTypeName(bookInfo.type);
        }
        
        console.log("==========================================");
        console.log("📚 현재 책 정보");
        console.log("==========================================");
        console.log(`책 이름: ${typeName} ${bookInfo.step} ${bookInfo.vol}권`);
        console.log(`타입: ${bookInfo.type}`);
        console.log(`단계: ${bookInfo.step}`);
        console.log(`권수: ${bookInfo.vol}`);
        console.log(`경로: ${dir}`);
        console.log(`총 페이지: ${images.length}페이지`);
        console.log(`페이지 모드: ${displayMode}`);  // ✨ displayMode 추가
        console.log("==========================================");
    } catch (error) {
        console.error('[displayBookInfo] 오류:', error);
    }
}

// ========================================
// 다른 책으로 변경 함수
// ========================================
function changeBook(type, step, vol) {
    const newBookParam = `${type}.${step}.${vol}`;
    const newUrl = `${window.location.pathname}?book=${newBookParam}`;
    
    console.log(`📚 책 변경: ${newBookParam}`);
    window.location.href = newUrl;
}

// ========================================
// displayMode 가져오기 함수 추가
// ========================================
function getDisplayMode() {
    return displayMode;
}

// ========================================
// 전역 함수로 노출
// ========================================
window.EbookConfig = {
    init: init,
    displayBookInfo: displayBookInfo,
    changeBook: changeBook,
    getBookInfo: () => bookInfo,
    getCurrentDirectory: () => dir,
    getImages: () => images,
    getDisplayMode: getDisplayMode  // ✨ displayMode 접근 함수 추가
};

// ========================================
// DOMContentLoaded 이벤트
// ========================================
document.addEventListener("DOMContentLoaded", function() {
    console.log('[DOMContentLoaded] 이벤트 발생');
    
    audio = document.getElementById("audio");
    
    // 약간의 딜레이 후 초기화 (book-config.php 로드 대기)
    setTimeout(() => {
        init();
    }, 100);
    
    console.log("💡 책 정보를 확인하려면 콘솔에서 'EbookConfig.displayBookInfo()' 입력");
    console.log("💡 페이지 모드를 확인하려면 콘솔에서 'EbookConfig.getDisplayMode()' 입력");
});
