<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>주문현황 엑셀 다운로드</title>
    
    <link href="https://unpkg.com/tabulator-tables@5.5.0/dist/css/tabulator_semanticui.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/tabulator-tables@5.5.0/dist/js/tabulator.min.js"></script>

    <style>
        /* 기본 스타일 유지 및 레이아웃 수정 */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: '맑은 고딕', sans-serif; background: #f0f2f5; color: #2d2d2d; padding: 48px 16px; display: flex; justify-content: center; }
        
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); width: 100%;  padding: 32px; }
        .card-title { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .card-sub { font-size: 13px; color: #888; margin-bottom: 28px; }

        .form-row { display: flex; gap: 10px; align-items: flex-end; margin-bottom: 14px; }
        .form-group { display: flex; flex-direction: column; flex: 1; }
        .form-group label { font-size: 12px; font-weight: 600; color: #666; margin-bottom: 6px; }
        .form-group input { border: 1.5px solid #ddd; border-radius: 8px; padding: 10px; background: #fafafa; width: 100%; }
        
        .check-row { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; }
        
        .btn-export { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 13px; background: linear-gradient(135deg, #FFA63B, #f08a1e); color: #fff; border: none; border-radius: 9px; cursor: pointer; font-weight: 600; }
        .btn-export:disabled { background: #ccc; cursor: not-allowed; }

        /* Tabulator 커스텀 스타일 */
        #order-table { margin-top: 20px; border-radius: 8px; overflow: hidden; font-size: 13px; }
        .preview-wrap { margin-top: 28px; display: none; }
        .msg { margin-top: 18px; font-size: 13px; text-align: center; padding: 10px; border-radius: 6px; display: none; }
        .msg.success { display:block; background:#eafaf1; color:#2e7d4f; }
        .msg.error { display:block; background:#fef2f2; color:#b91c1c; }
    </style>
</head>
<body>

<div class="card">
    <h1 class="card-title">📦 주문현황 관리 </h1>
    <p class="card-sub">데이터를 조회하고 엑셀로 간편하게 내보내세요.</p>

    <div class="form-row">
        <div class="form-group">
            <label>시작 날짜</label>
            <input type="date" id="fromDate">
        </div>
        <span style="padding-bottom:10px;">~</span>
        <div class="form-group">
            <label>종료 날짜</label>
            <input type="date" id="toDate">
        </div>
    </div>

    <div class="check-row">
        <input type="checkbox" id="chkAll">
        <label for="chkAll">전체 기간 조회</label>
    </div>

    <button class="btn-export" id="btnSearch" onclick="loadData()">
        <span class="icon">🔍</span> 데이터 조회하기
    </button>

    <div id="msg" class="msg"></div>

    <div class="preview-wrap" id="previewWrap">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <span style="font-weight:600;">📄 조회 내역</span>
            <button class="btn-export" style="width:auto; padding:8px 16px; font-size:12px;" onclick="exportExcel()">
                <span class="icon">⬇️</span> 엑셀로 저장
            </button>
        </div>
        <div id="order-table"></div>
    </div>
</div>

<script>
let table; // Tabulator 인스턴스
const previewUrl = <?= json_encode(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/preview_excel.php', JSON_UNESCAPED_SLASHES) ?>;

// 1. 날짜 초기화
(function init() {
    const now = new Date();
    const fmt = d => d.toISOString().split('T')[0];
    document.getElementById('fromDate').value = fmt(new Date(now.getFullYear(), now.getMonth(), 1));
    document.getElementById('toDate').value = fmt(new Date(now.getFullYear(), now.getMonth() + 1, 0));
})();

// 2. 체크박스 제어
document.getElementById('chkAll').addEventListener('change', function() {
    const isChecked = this.checked;
    document.getElementById('fromDate').disabled = isChecked;
    document.getElementById('toDate').disabled = isChecked;
});

// 3. Tabulator 테이블 설정
function formatBranchName(name) {
    const match = String(name ?? '').match(/\(([^()]*)\)/);
    return match ? match[1].trim() : name;
}

function parseJsonResponse(text) {
    try {
        return JSON.parse(text);
    } catch (e) {
        const candidates = [
            text.indexOf('['),
            text.indexOf('{"status"'),
            text.indexOf('{"message"')
        ].filter(index => index >= 0).sort((a, b) => a - b);

        for (const start of candidates) {
            const end = Math.max(text.lastIndexOf(']'), text.lastIndexOf('}'));
            if (end <= start) continue;

            try {
                return JSON.parse(text.slice(start, end + 1));
            } catch (e) {
                // 다음 후보를 시도한다.
            }
        }

        throw new Error("서버 응답이 JSON 형식이 아닙니다. " + text.slice(0, 120).replace(/\s+/g, ' '));
    }
}

function initTable(data) {
    table = new Tabulator("#order-table", {
        data: data,
        layout: "fitColumns",
        pagination: "local",
        paginationSize: 10,
        movableColumns: true,
        responsiveLayout: "collapse",
        columns: [
            {title: "날짜", field: "date", width: 100},
            {title: "지사", field: "owner", width: 100},
            {title: "유치원명", field: "order", width: 150},
            {title: "주소", field: "addr", width: 200},
            {title: "0단계", field: "title_0"},
            {title: "수량", field: "count_0", hozAlign: "center", formatter: "money", formatterParams: {precision: 0}},
            {title: "A단계", field: "title_A"},
            {title: "수량", field: "count_A", hozAlign: "center", formatter: "money", formatterParams: {precision: 0}},
            {title: "B단계", field: "title_B"},
            {title: "수량", field: "count_B", hozAlign: "center", formatter: "money", formatterParams: {precision: 0}},
            {title: "총수량", field: "total_count", hozAlign: "center", formatter: "money", formatterParams: {precision: 0}},
            {title: "합계금액", field: "total", hozAlign: "right", formatter: "money", formatterParams: {precision: 0, symbol: "₩"}},
            {title: "송장번호", field: "invoice"},
            {title: "출고일", field: "out_date"}
        ],
    });
}

// 4. 데이터 로드 (AJAX)
async function loadData() {
    const all = document.getElementById('chkAll').checked;
    const from = document.getElementById('fromDate').value;
    const to = document.getElementById('toDate').value;
    
    let url = `${previewUrl}?${all ? 'all=1' : `from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`}`;
    
    const btn = document.getElementById('btnSearch');
    const msg = document.getElementById('msg');
    
    btn.disabled = true;
    btn.innerText = "데이터 요청 중...";
    
    try {
        const response = await fetch(url);
        const text = await response.text();
        let payload;

        payload = parseJsonResponse(text);

        if (!response.ok || !Array.isArray(payload)) {
            throw new Error(payload.message || "데이터를 불러오지 못했습니다.");
        }

        const data = payload.map(row => ({
            ...row,
            owner: formatBranchName(row.owner)
        }));
        
        if (data.length === 0) {
            msg.innerText = "조회된 데이터가 없습니다.";
            msg.className = "msg error";
            document.getElementById('previewWrap').style.display = 'none';
        } else {
            msg.innerText = `총 ${data.length}건의 데이터를 로드했습니다.`;
            msg.className = "msg success";
            document.getElementById('previewWrap').style.display = 'block';
            
            // 테이블이 이미 있으면 데이터만 교체, 없으면 새로 생성
            if (table) {
                table.setData(data);
            } else {
                initTable(data);
            }
        }
    } catch (e) {
        msg.innerText = e.message || "데이터 로드 중 오류가 발생했습니다.";
        msg.className = "msg error";
    } finally {
        btn.disabled = false;
        btn.innerText = "🔍 데이터 조회하기";
    }
}

// 5. 엑셀 내보내기 (클라이언트 측에서 직접 생성)
function exportExcel() {
    if (!table) return;
    
    // table.download() 기능을 사용하여 브라우저에서 즉시 엑셀 생성
    table.download("xlsx", `주문현황_${new Date().getTime()}.xlsx`, {
        sheetName: "주문데이터"
    });
}
</script>

</body>
</html>
