/**
 * ========================================
 * EPLAT Ebook - 자동 생성된 설정 파일
 * 생성 시간: 2026-04-08 23:14:35
 * ========================================
 */

// ========================================
// 시스템 설정
// ========================================
const ROOT_PATH = "https://www.eplat.co.kr/ebook_new_eplat/";
const DEFAULT_BOOK = "story.basic.v1";
const IMG_FORMAT = "png";
const SOUND_FORMAT = "mp3";

// ========================================
// 책 타입별 설정
// ========================================

// 예비초등학생 영어문장 학습용
var BOOK_TYPES = BOOK_TYPES || {};
BOOK_TYPES['sentence'] = {
    name: '예비초등학생 영어문장 학습용',
    path: ROOT_PATH + 'bookshelf-3/',
    displayMode: 'double'  // ✨ 페이지 표시 모드 (single/double)
};

// 스토리북
var BOOK_TYPES = BOOK_TYPES || {};
BOOK_TYPES['story'] = {
    name: '스토리북',
    path: ROOT_PATH + 'bookshelf-1/1-1_story/',
    displayMode: 'double'  // ✨ 페이지 표시 모드 (single/double)
};

// 파닉스
var BOOK_TYPES = BOOK_TYPES || {};
BOOK_TYPES['phonics'] = {
    name: '파닉스',
    path: ROOT_PATH + 'bookshelf-1/2-1_phonics/',
    displayMode: 'double'  // ✨ 페이지 표시 모드 (single/double)
};

// 파닉스 문장
var BOOK_TYPES = BOOK_TYPES || {};
BOOK_TYPES['ph_sentence'] = {
    name: '파닉스 문장',
    path: ROOT_PATH + 'bookshelf-1/2-2_phonics_sentence/',
    displayMode: 'single'  // ✨ 페이지 표시 모드 (single/double)
};

// 스토리 문장
var BOOK_TYPES = BOOK_TYPES || {};
BOOK_TYPES['st_sentence'] = {
    name: '스토리 문장',
    path: ROOT_PATH + 'bookshelf-1/1-2_story_sentence/',
    displayMode: 'single'  // ✨ 페이지 표시 모드 (single/double)
};

// 블록
var BOOK_TYPES = BOOK_TYPES || {};
BOOK_TYPES['block'] = {
    name: '블록',
    path: ROOT_PATH + 'bookshelf-2/1-1_blocks/',
    displayMode: 'double'  // ✨ 페이지 표시 모드 (single/double)
};

// eplat 초등부 문장 sentence
var BOOK_TYPES = BOOK_TYPES || {};
BOOK_TYPES['eplatp_sentence'] = {
    name: 'eplat 초등부 문장 sentence',
    path: ROOT_PATH + 'bookshelf-2/pres/sentence/',
    displayMode: 'single'  // ✨ 페이지 표시 모드 (single/double)
};

// eplat 초등부 문장 phonice
var BOOK_TYPES = BOOK_TYPES || {};
BOOK_TYPES['eplatp_phonics'] = {
    name: 'eplat 초등부 문장 phonice',
    path: ROOT_PATH + 'bookshelf-2/pres/phonics/',
    displayMode: 'single'  // ✨ 페이지 표시 모드 (single/double)
};

// ========================================
// ebook-config.js 생성 함수
// ========================================

/**
 * 특정 책 타입의 ebook-config.js 내용 생성
 * @param {string} typeCode - 책 타입 코드
 * @param {string} level - 레벨 (예: basic, level1)
 * @param {string} version - 버전 (예: v1, v2)
 */
function generateEbookConfig(typeCode, level, version) {
    const bookType = BOOK_TYPES[typeCode];
    if (!bookType) {
        console.error('책 타입을 찾을 수 없습니다:', typeCode);
        return;
    }

    const config = `
/**
 * EPLAT Ebook - ${bookType.name} 설정
 */

// ✨ 페이지 표시 모드 (데이터베이스에서 로드)
const displayMode = "${bookType.displayMode}";

// 이미지 경로
const imgpath = "${bookType.path}${level}/${version}/";

// 오디오 파일 확장자
const sound = "mp3";

// 이미지 목록 (자동 생성 필요)
const images = [
    // 여기에 이미지 파일 경로를 추가하세요
];

// 동영상 링크 (선택사항)
const videoLinks = {};
`;

    return config;
}

// ========================================
// 사용 예제
// ========================================
/*
// 예비초등학생 영어문장 학습용 설정 생성
const sentenceConfig = generateEbookConfig('sentence', 'basic', 'v1');
console.log(sentenceConfig);

// 스토리북 설정 생성
const storyConfig = generateEbookConfig('story', 'basic', 'v1');
console.log(storyConfig);

// 파닉스 설정 생성
const phonicsConfig = generateEbookConfig('phonics', 'basic', 'v1');
console.log(phonicsConfig);

*/
