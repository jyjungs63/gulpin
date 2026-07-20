<?php
/**
 * ========================================
 * EPLAT Ebook - 동적 책 설정 (category ROOT_PATH_MAP 지원)
 * ========================================
 */

header('Content-Type: application/javascript; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

try {
    if (!defined('DB_CONFIG_THROW_ON_CONNECT_ERROR')) {
        define('DB_CONFIG_THROW_ON_CONNECT_ERROR', true);
    }
    require_once 'admin/db_config.php';
    $db = Database::getInstance()->getConnection();

    // ── 시스템 설정 (전체) ──
    $stmt = $db->query("SELECT config_key, config_value FROM system_config");
    $config = [];
    while ($row = $stmt->fetch()) {
        $config[$row['config_key']] = $row['config_value'];
    }

    // ── 카테고리별 root_path 맵 ──
    $stmt = $db->query("SELECT category_code, root_path, is_default FROM system_config_categories");
    $categoryRootPaths = [];
    $defaultRootPath   = $config['ROOT_PATH'] ?? '../ebook_new_eplat/';
    while ($row = $stmt->fetch()) {
        $categoryRootPaths[$row['category_code']] = $row['root_path'] ?: $defaultRootPath;
        if ($row['is_default']) {
            $defaultRootPath = $row['root_path'] ?: $defaultRootPath;
        }
    }

    // ── 책 타입 목록 ──
    $stmt = $db->query("
        SELECT type_code, type_name, directory_path, category_code
        FROM book_types
        WHERE is_active = 1
        ORDER BY display_order
    ");
    $bookTypes = $stmt->fetchAll();

    // ── 타입별 ROOT_PATH_MAP 계산 ──
    // book_type.category_code 가 NULL이면 기본 카테고리 사용
    $rootPathMap = [];
    foreach ($bookTypes as $type) {
        $catCode = $type['category_code'] ?? null;
        if ($catCode && isset($categoryRootPaths[$catCode])) {
            $rootPathMap[$type['type_code']] = $categoryRootPaths[$catCode];
        } else {
            $rootPathMap[$type['type_code']] = $defaultRootPath;
        }
    }

    $defaultBook = $config['DEFAULT_BOOK'] ?? 'story.basic.v1';
?>
/**
 * EPLAT Ebook - 책 설정 (자동 생성, category ROOT_PATH_MAP 포함)
 * 생성: <?php echo date('Y-m-d H:i:s'); ?>
 */
const BOOK_INIT = {
    timestamp: <?php echo json_encode(date('Y-m-d H:i:s')); ?>,
    imgformat: <?php echo json_encode($config['IMG_FORMAT'] ?? $config['IMAGE_FORMAT'] ?? 'png'); ?>,
    sound: <?php echo json_encode($config['SOUND_FORMAT'] ?? 'mp3'); ?>
};

const BOOK_CONFIG = {
    ROOT_PATH: <?php echo json_encode($defaultRootPath, JSON_UNESCAPED_SLASHES); ?>,

    // 타입별 루트 경로 (카테고리 기반)
    ROOT_PATH_MAP: <?php echo json_encode($rootPathMap, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>,

    DIRECTORY_MAP: {
<?php
    $count = count($bookTypes);
    foreach ($bookTypes as $i => $type) {
        $comma = ($i < $count - 1) ? ',' : '';
        echo "        " . json_encode($type['type_code']) . ": " .
             json_encode($type['directory_path'], JSON_UNESCAPED_SLASHES) . "$comma\n";
    }
?>
    },

    VALID_TYPES: <?php echo json_encode(array_column($bookTypes, 'type_code')); ?>,

    DEFAULT_BOOK: <?php echo json_encode($defaultBook); ?>
};

const BOOK_TYPE_NAMES = {
<?php
    $count = count($bookTypes);
    foreach ($bookTypes as $i => $type) {
        $comma = ($i < $count - 1) ? ',' : '';
        echo "    " . json_encode($type['type_code']) . ": " .
             json_encode($type['type_name'], JSON_UNESCAPED_UNICODE) . "$comma\n";
    }
?>
};

function joinBookPath(rootPath, directoryPath) {
    const root = rootPath.replace(/\/+$/, '');
    let directory = directoryPath.replace(/^\/+/, '');
    const rootLastPart = root.split('/').pop();

    if (rootLastPart && directory.startsWith(`${rootLastPart}/`)) {
        directory = directory.substring(rootLastPart.length + 1);
    }

    return `${root}/${directory}`;
}

/**
 * 책 파라미터 파싱 (ROOT_PATH_MAP 사용)
 *
 *  파라미터 형식별 동작:
 *   2항목  type.xxx            → directory = ROOT/DIRECTORY_MAP[type]xxx/           (step="", vol="")
 *   3항목  type.step.vol       → directory = ROOT/DIRECTORY_MAP[type]step/vol/      (기존 동작)
 *   4항목+ type.a.b.c.d.e      → directory = ROOT/DIRECTORY_MAP[type]a/b/c/d/e/    (vol="")
 */
function parseBookParameter(bookParam) {
    console.log('[parseBookParameter] 입력:', bookParam);

    if (!bookParam || bookParam === '') {
        return { success: false, directory: "", type: "", step: "", vol: "", error: "책 파라미터가 없습니다." };
    }

    const parts = bookParam.split(".");
    if (parts.length < 2) {
        return { success: false, directory: "", type: "", step: "", vol: "",
                 error: `잘못된 형식: ${bookParam}. 최소 형식: type.path (예: story.v1)` };
    }

    const type = parts[0];

    if (!BOOK_CONFIG.VALID_TYPES.includes(type)) {
        return { success: false, directory: "", type, step: "", vol: "",
                 error: `알 수 없는 책 타입: ${type}` };
    }

    // 타입별 ROOT_PATH 사용 (카테고리 기반), 없으면 기본 ROOT_PATH 사용
    const rootPath = (BOOK_CONFIG.ROOT_PATH_MAP && BOOK_CONFIG.ROOT_PATH_MAP[type])
        ? BOOK_CONFIG.ROOT_PATH_MAP[type]
        : BOOK_CONFIG.ROOT_PATH;
    const basePath = joinBookPath(rootPath, BOOK_CONFIG.DIRECTORY_MAP[type]);

    let directory, step, vol;

    if (parts.length === 2) {
        // type.xxx → 단일 경로
        step      = "";
        vol       = "";
        directory = `${basePath}${parts[1]}/`;
    } else if (parts.length === 3) {
        // type.step.vol → 기존 동작
        step      = parts[1];
        vol       = parts[2];
        directory = `${basePath}${step}/${vol}/`;
    } else {
        // type.a.b.c... → 나머지 항목을 /로 결합
        step      = parts[1];
        vol       = "";
        const subPath = parts.slice(1).join('/');
        directory = `${basePath}${subPath}/`;
    }

    console.log('[parseBookParameter] 생성된 경로:', directory, '(rootPath:', rootPath, ')');
    return { success: true, directory, type, step, vol, error: "" };
}

/**
 * URL에서 책 디렉토리 가져오기
 */
function getBookDirectoryFromURL() {
    console.log('[getBookDirectoryFromURL] 시작');

    try {
        const url = new URL(window.location.href);
        let bookParam = sessionStorage.getItem("ebookBook");

        if (!bookParam && url.searchParams.has("book")) {
            bookParam = url.searchParams.get("book");
        }

        if (!bookParam) {
            bookParam = BOOK_CONFIG.DEFAULT_BOOK;
        }

        const result = parseBookParameter(bookParam);

        if (result.success) {
            return {
                success: true,
                directory: result.directory,
                bookInfo: { type: result.type, step: result.step, vol: result.vol, fullName: bookParam }
            };
        }

        // 실패 시 기본 책으로 재시도
        const defaultResult = parseBookParameter(BOOK_CONFIG.DEFAULT_BOOK);
        if (defaultResult.success) {
            return {
                success: true,
                directory: defaultResult.directory,
                bookInfo: { type: defaultResult.type, step: defaultResult.step,
                            vol: defaultResult.vol, fullName: BOOK_CONFIG.DEFAULT_BOOK }
            };
        }

        return {
            success: false,
            directory: BOOK_CONFIG.ROOT_PATH || "../ebook_new_eplat/",
            bookInfo: { type: "phonics", step: "v1", vol: "1", fullName: "phonics.v1.1" },
            error: result.error
        };
    } catch (error) {
        console.error('[getBookDirectoryFromURL] 예외 발생:', error);
        return {
            success: false,
            directory: BOOK_CONFIG.ROOT_PATH || "../ebook_new_eplat/",
            bookInfo: { type: "phonics", step: "v1", vol: "1", fullName: "phonics.v1.1" },
            error: error.message
        };
    }
}

function getBookTypeName(type)    { return BOOK_TYPE_NAMES[type] || type; }
function createBookDirectory(type, step, vol) {
    if (!type || !step || !vol) return "";
    if (!BOOK_CONFIG.VALID_TYPES.includes(type)) return "";
    const rootPath = (BOOK_CONFIG.ROOT_PATH_MAP && BOOK_CONFIG.ROOT_PATH_MAP[type])
        ? BOOK_CONFIG.ROOT_PATH_MAP[type] : BOOK_CONFIG.ROOT_PATH;
    return `${joinBookPath(rootPath, BOOK_CONFIG.DIRECTORY_MAP[type])}${step}/${vol}/`;
}
function getAvailableBookTypes() {
    return BOOK_CONFIG.VALID_TYPES.map(type => ({
        value: type, name: getBookTypeName(type), path: BOOK_CONFIG.DIRECTORY_MAP[type]
    }));
}

window.BookConfig = {
    init: BOOK_INIT,
    config: BOOK_CONFIG,
    typeNames: BOOK_TYPE_NAMES,
    parseBookParameter,
    getBookDirectoryFromURL,
    getBookTypeName,
    createBookDirectory,
    getAvailableBookTypes
};

console.log("✅ 책 설정 로드 완료 (카테고리 ROOT_PATH_MAP 포함)");
console.log("📚 ROOT_PATH_MAP:", BOOK_CONFIG.ROOT_PATH_MAP);

<?php
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    $staticConfigPath = __DIR__ . '/admin/book-config.js';

    echo "console.error('❌ DB 오류:', " . json_encode($errorMessage, JSON_UNESCAPED_UNICODE) . ");\n\n";

    if (is_readable($staticConfigPath)) {
        echo "/* DB 실패 fallback: admin/book-config.js 참조 */\n";
        echo file_get_contents($staticConfigPath);
        echo "\n\n";
        echo "window.__BOOK_CONFIG_DB_ERROR__ = " . json_encode($errorMessage, JSON_UNESCAPED_UNICODE) . ";\n";
?>
(function () {
    const fallbackRootPath = (typeof ROOT_PATH !== 'undefined' && ROOT_PATH) ? ROOT_PATH : '../ebook_new_eplat/';
    const fallbackDefaultBook = (typeof DEFAULT_BOOK !== 'undefined' && DEFAULT_BOOK) ? DEFAULT_BOOK : 'phonics.v1.1';
    const fallbackBookTypes = (typeof BOOK_TYPES !== 'undefined' && BOOK_TYPES) ? BOOK_TYPES : {};
    const fallbackDirectoryMap = {};
    const fallbackRootPathMap = {};
    const fallbackTypeNames = {};

    Object.keys(fallbackBookTypes).forEach(function (type) {
        const item = fallbackBookTypes[type] || {};
        const fullPath = item.path || '';

        fallbackTypeNames[type] = item.name || type;
        fallbackRootPathMap[type] = fallbackRootPath;
        fallbackDirectoryMap[type] = fullPath.indexOf(fallbackRootPath) === 0
            ? fullPath.slice(fallbackRootPath.length)
            : fullPath;
    });

    const BOOK_CONFIG = {
        ROOT_PATH: fallbackRootPath,
        ROOT_PATH_MAP: fallbackRootPathMap,
        DIRECTORY_MAP: fallbackDirectoryMap,
        VALID_TYPES: Object.keys(fallbackDirectoryMap),
        DEFAULT_BOOK: fallbackDefaultBook
    };
    const BOOK_TYPE_NAMES = fallbackTypeNames;
    const BOOK_INIT = {
        imgformat: (typeof IMG_FORMAT !== 'undefined' && IMG_FORMAT) ? IMG_FORMAT : 'png',
        sound: (typeof SOUND_FORMAT !== 'undefined' && SOUND_FORMAT) ? SOUND_FORMAT : 'mp3'
    };

    function parseBookParameter(bookParam) {
        if (!bookParam || bookParam === '') {
            return { success: false, directory: "", type: "", step: "", vol: "", error: "책 파라미터가 없습니다." };
        }

        const parts = bookParam.split(".");
        if (parts.length < 2) {
            return { success: false, directory: "", type: "", step: "", vol: "", error: "형식오류" };
        }

        const type = parts[0];
        if (!BOOK_CONFIG.VALID_TYPES.includes(type)) {
            return { success: false, directory: "", type, step: "", vol: "", error: "알수없는타입" };
        }

        const rootPath = (BOOK_CONFIG.ROOT_PATH_MAP && BOOK_CONFIG.ROOT_PATH_MAP[type])
            ? BOOK_CONFIG.ROOT_PATH_MAP[type]
            : BOOK_CONFIG.ROOT_PATH;
        const base = rootPath + BOOK_CONFIG.DIRECTORY_MAP[type];

        if (parts.length === 2) {
            return { success: true, directory: base + parts[1] + '/', type, step: "", vol: "", error: "" };
        }
        if (parts.length === 3) {
            return { success: true, directory: base + parts[1] + '/' + parts[2] + '/', type, step: parts[1], vol: parts[2], error: "" };
        }
        return { success: true, directory: base + parts.slice(1).join('/') + '/', type, step: parts[1], vol: "", error: "" };
    }

    function getBookDirectoryFromURL() {
        const url = new URL(window.location.href);
        const bookParam = sessionStorage.getItem('ebookBook') || url.searchParams.get('book') || BOOK_CONFIG.DEFAULT_BOOK;
        const result = parseBookParameter(bookParam);

        if (result.success) {
            return {
                success: true,
                directory: result.directory,
                bookInfo: { type: result.type, step: result.step, vol: result.vol, fullName: bookParam }
            };
        }

        const defaultResult = parseBookParameter(BOOK_CONFIG.DEFAULT_BOOK);
        if (defaultResult.success) {
            return {
                success: true,
                directory: defaultResult.directory,
                bookInfo: { type: defaultResult.type, step: defaultResult.step, vol: defaultResult.vol, fullName: BOOK_CONFIG.DEFAULT_BOOK }
            };
        }

        return {
            success: false,
            directory: BOOK_CONFIG.ROOT_PATH,
            bookInfo: { type: 'phonics', step: 'v1', vol: '1', fullName: 'phonics.v1.1' },
            error: result.error
        };
    }

    function getBookTypeName(type) { return BOOK_TYPE_NAMES[type] || type; }
    function createBookDirectory(type, step, vol) {
        if (!type || !step || !vol || !BOOK_CONFIG.VALID_TYPES.includes(type)) return "";
        const rootPath = (BOOK_CONFIG.ROOT_PATH_MAP && BOOK_CONFIG.ROOT_PATH_MAP[type])
            ? BOOK_CONFIG.ROOT_PATH_MAP[type]
            : BOOK_CONFIG.ROOT_PATH;
        return rootPath + BOOK_CONFIG.DIRECTORY_MAP[type] + step + '/' + vol + '/';
    }
    function getAvailableBookTypes() {
        return BOOK_CONFIG.VALID_TYPES.map(function (type) {
            return { value: type, name: getBookTypeName(type), path: BOOK_CONFIG.DIRECTORY_MAP[type] };
        });
    }

    window.BookConfig = {
        init: BOOK_INIT,
        config: BOOK_CONFIG,
        typeNames: BOOK_TYPE_NAMES,
        parseBookParameter,
        getBookDirectoryFromURL,
        getBookTypeName,
        createBookDirectory,
        getAvailableBookTypes,
        error: true,
        errorMessage: window.__BOOK_CONFIG_DB_ERROR__
    };

    console.log("✅ 책 설정 fallback 로드 완료 (admin/book-config.js)");
})();
<?php
    } else {
        echo "const BOOK_CONFIG = {\n";
        echo "    ROOT_PATH: '../ebook_new_eplat/',\n";
        echo "    ROOT_PATH_MAP: {},\n";
        echo "    DIRECTORY_MAP: {\n";
        echo "        'story': 'bookshelf-1/1-1_story/',\n";
        echo "        'phonics': 'bookshelf-1/2-1_phonics/',\n";
        echo "        'ph_sentence': 'bookshelf-1/2-2_phonics_sentence/',\n";
        echo "        'st_sentence': 'bookshelf-1/1-2_story_sentence/',\n";
        echo "        'block': 'bookshelf-2/1-1_blocks/'\n";
        echo "    },\n";
        echo "    VALID_TYPES: ['story','phonics','ph_sentence','st_sentence','block'],\n";
        echo "    DEFAULT_BOOK: 'phonics.v1.1'\n";
        echo "};\n";
        echo "const BOOK_TYPE_NAMES = { 'story':'스토리북','phonics':'파닉스','ph_sentence':'파닉스 문장','st_sentence':'스토리 문장','block':'블록' };\n";
        echo "const BOOK_INIT = { imgformat:'png', sound:'mp3' };\n\n";
        echo "function parseBookParameter(p) {\n";
        echo "    const pts=p.split('.');if(pts.length<2)return{success:false,error:'형식오류'};\n";
        echo "    const t=pts[0];if(!BOOK_CONFIG.VALID_TYPES.includes(t))return{success:false,error:'알수없는타입'};\n";
        echo "    const r=(BOOK_CONFIG.ROOT_PATH_MAP&&BOOK_CONFIG.ROOT_PATH_MAP[t])||BOOK_CONFIG.ROOT_PATH;\n";
        echo "    const base=r+BOOK_CONFIG.DIRECTORY_MAP[t];\n";
        echo "    if(pts.length===2)return{success:true,directory:base+pts[1]+'/',type:t,step:'',vol:''};\n";
        echo "    if(pts.length===3)return{success:true,directory:base+pts[1]+'/'+pts[2]+'/',type:t,step:pts[1],vol:pts[2]};\n";
        echo "    return{success:true,directory:base+pts.slice(1).join('/')+'/',type:t,step:pts[1],vol:''};\n";
        echo "}\n";
        echo "function getBookDirectoryFromURL() {\n";
        echo "    const u=new URL(window.location.href);\n";
        echo "    const bp=sessionStorage.getItem('ebookBook')||u.searchParams.get('book')||BOOK_CONFIG.DEFAULT_BOOK;\n";
        echo "    const r=parseBookParameter(bp);\n";
        echo "    return r.success?{success:true,directory:r.directory,bookInfo:{type:r.type,step:r.step,vol:r.vol,fullName:bp}}\n";
        echo "        :{success:false,directory:BOOK_CONFIG.ROOT_PATH,bookInfo:{type:'phonics',step:'v1',vol:'1',fullName:'phonics.v1.1'}};\n";
        echo "}\n";
        echo "function getBookTypeName(t){return BOOK_TYPE_NAMES[t]||t;}\n";
        echo "function createBookDirectory(t,s,v){if(!t||!s||!v)return'';const r=(BOOK_CONFIG.ROOT_PATH_MAP&&BOOK_CONFIG.ROOT_PATH_MAP[t])||BOOK_CONFIG.ROOT_PATH;return r+BOOK_CONFIG.DIRECTORY_MAP[t]+s+'/'+v+'/';}\n";
        echo "function getAvailableBookTypes(){return BOOK_CONFIG.VALID_TYPES.map(t=>({value:t,name:getBookTypeName(t),path:BOOK_CONFIG.DIRECTORY_MAP[t]}));}\n";
        echo "window.BookConfig={init:BOOK_INIT,config:BOOK_CONFIG,typeNames:BOOK_TYPE_NAMES,parseBookParameter,getBookDirectoryFromURL,getBookTypeName,createBookDirectory,getAvailableBookTypes,error:true,errorMessage:" . json_encode($errorMessage, JSON_UNESCAPED_UNICODE) . "};\n";
    }
}
?>
