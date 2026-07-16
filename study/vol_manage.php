<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>사용자 통합 관리 - 필터 기능 추가</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4a90e2;
            --bg: #f0f4f9;
            --danger: #e74c3c;
            --success: #27ae60;
            --warning: #e67e22;
            --card-bg: #ffffff;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Noto Sans KR', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .page-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        h1.page-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            letter-spacing: -0.5px;
        }

        /* ── 상단 설정 카드 ── */
        .card {
            background: var(--card-bg);
            padding: 28px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .card h2 {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 22px;
        }

        .filter-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
            background: #eef1f5;
            padding: 5px;
            border-radius: 10px;
        }
        .tab {
            flex: 1;
            padding: 9px 4px;
            text-align: center;
            cursor: pointer;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            transition: all 0.2s;
            user-select: none;
        }
        .tab.active {
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            color: var(--primary);
        }
        .tab:hover:not(.active) { background: #e0e5ec; }

        .field { margin-bottom: 18px; }
        .field label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 600;
            color: #555;
        }

        input[type="text"], select {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #dde3ec;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
            background: #fff;
        }
        input[type="text"]:focus, select:focus { border-color: var(--primary); }

        hr.divider { border: none; border-top: 1px solid #eef1f5; margin: 20px 0; }

        .month-grid {
            display: none;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            background: #f8f9fb;
            padding: 14px;
            border-radius: 8px;
            border: 1.5px solid #eef1f5;
        }
        .month-grid.active { display: grid; }
        .month-item {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: #444;
        }
        .month-item input[type="checkbox"] { accent-color: var(--primary); width: 15px; height: 15px; }

        .btn-save {
            width: 100%;
            padding: 13px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 9px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 6px;
            font-family: inherit;
        }
        .btn-save:hover { background: #357abd; }
        .btn-save:active { transform: scale(0.98); }
        .btn-sync {
            width: 100%;
            padding: 13px;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 9px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 8px;
            font-family: inherit;
        }
        .btn-sync:hover { background: #219a52; }
        .btn-sync:active { transform: scale(0.98); }

        /* 상태 셀렉트 색상 */
        select#statusSelect.bg-all    { background: #edfaf2; border-color: var(--success); color: var(--success); font-weight: 700; }
        select#statusSelect.bg-partial { background: #fff8ee; border-color: var(--warning); color: var(--warning); font-weight: 700; }
        select#statusSelect.bg-none   { background: #fdf2f2; border-color: var(--danger);  color: var(--danger);  font-weight: 700; }

        /* ── 하단 현황 테이블 ── */
        .table-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid #eef1f5;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-card-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
        }

        .legend {
            display: flex;
            gap: 14px;
            font-size: 12px;
            color: #666;
        }
        .legend-item { display: flex; align-items: center; gap: 5px; }
        .legend-dot {
            width: 9px; height: 9px;
            border-radius: 50%;
            display: inline-block;
        }

        /* 테이블 툴바 (검색 + 페이지당 건수) */
        .table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            border-bottom: 1px solid #eef1f5;
            gap: 10px;
            flex-wrap: wrap;
        }
        .table-search-wrap {
            position: relative;
            flex: 1;
            max-width: 280px;
        }
        .table-search-wrap input {
            width: 100%;
            padding: 8px 12px 8px 34px;
            border: 1.5px solid #dde3ec;
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
        }
        .table-search-wrap input:focus { border-color: var(--primary); }
        .table-search-wrap .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 14px;
            pointer-events: none;
        }
        .table-search-wrap .clear-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #aaa;
            font-size: 16px;
            line-height: 1;
            padding: 0;
            display: none;
        }
        .table-search-wrap .clear-btn.visible { display: block; }

        .table-info {
            font-size: 12px;
            color: #888;
            white-space: nowrap;
        }
        .table-info strong { color: #444; }

        /* 페이지네이션 */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 14px 20px;
            border-top: 1px solid #eef1f5;
        }
        .page-btn {
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border: 1.5px solid #dde3ec;
            border-radius: 7px;
            background: white;
            font-size: 13px;
            font-family: inherit;
            font-weight: 600;
            color: #555;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .page-btn:hover:not(:disabled) { border-color: var(--primary); color: var(--primary); }
        .page-btn.active { background: var(--primary); border-color: var(--primary); color: white; }
        .page-btn:disabled { opacity: 0.35; cursor: default; }
        .page-size-select {
            padding: 5px 8px;
            border: 1.5px solid #dde3ec;
            border-radius: 7px;
            font-size: 12px;
            font-family: inherit;
            outline: none;
            color: #555;
            background: white;
            cursor: pointer;
        }

        .table-scroll { overflow-x: auto; }

        table.vol-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            min-width: 700px;
        }

        table.vol-table thead tr {
            background: #f7f9fc;
        }
        table.vol-table th {
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #e8edf3;
            white-space: nowrap;
        }
        table.vol-table th:first-child {
            text-align: left;
            padding-left: 20px;
            min-width: 110px;
        }

        table.vol-table tbody tr {
            border-bottom: 1px solid #f0f3f7;
            transition: background 0.15s;
            cursor: pointer;
        }
        table.vol-table tbody tr:hover { background: #f5f8ff; }
        table.vol-table tbody tr:last-child { border-bottom: none; }

        table.vol-table td {
            padding: 10px 8px;
            text-align: center;
            color: #444;
        }
        table.vol-table td:first-child {
            text-align: left;
            padding-left: 20px;
            font-weight: 600;
            color: #2c3e50;
        }

        /* 볼륨 셀 상태 */
        .vol-cell-on {
            background: #d4f0e4;
            color: var(--success);
            border-radius: 4px;
            font-weight: 700;
            font-size: 12px;
        }
        .vol-cell-off { color: #ccc; }
        .vol-cell-all {
            background: #ddefff;
            color: var(--primary);
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
        }

        /* 상태 뱃지 */
        .badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }
        .badge-all     { background: #ddefff; color: #2471a3; }
        .badge-partial { background: #fef5e7; color: #d35400; }
        .badge-none    { background: #fdecea; color: #c0392b; }

        /* 테이블 하단 로딩 */
        .table-loading {
            text-align: center;
            padding: 30px;
            color: #aaa;
            font-size: 14px;
        }

        /* 선택된 행 하이라이트 */
        table.vol-table tbody tr.selected-row {
            background: #ebf3ff !important;
        }

        .refresh-btn {
            background: none;
            border: 1.5px solid #dde3ec;
            border-radius: 7px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s;
        }
        .refresh-btn:hover { border-color: var(--primary); color: var(--primary); }
    </style>
</head>
<body>

<div class="page-wrapper">

    <!-- 상단: 설정 카드 -->
    <div class="card">
        <h2>유치원 권한 관리</h2>

        <label style="font-size:13px; font-weight:600; color:#555; display:block; margin-bottom:8px;">종류별 유치원 필터</label>
        <div class="filter-tabs" id="filterTabs">
            <div class="tab active" onclick="loadUsers('all_list', this)">전체</div>
            <div class="tab" onclick="loadUsers('all', this)">전체 허용</div>
            <div class="tab" onclick="loadUsers('partial', this)">부분 허용</div>
            <div class="tab" onclick="loadUsers('none', this)">접속 금지</div>
        </div>

        <div class="field">
            <label>유치원 검색</label>
            <input type="text" id="userSearch" placeholder="이름 또는 ID 입력..." oninput="filterUsers()">
        </div>
        <div class="field">
            <label>관리 대상 선택</label>
            <select id="userCombo" onchange="fetchUserData()">
                <option value="">-- 사용자를 선택하세요 --</option>
            </select>
        </div>

        <hr class="divider">

        <div class="field">
            <label>상태 변경</label>
            <select id="statusSelect" onchange="toggleUI()">
                <option value="all">전체 허용 (All)</option>
                <option value="partial">부분 허용 (Partial)</option>
                <option value="none">접속 금지 (None)</option>
            </select>
        </div>

        <div class="field">
            <div id="monthGrid" class="month-grid"></div>
        </div>

        <button class="btn-save" onclick="submitUpdate()">설정 업데이트 저장</button>
        <button class="btn-sync" onclick="syncUsers()">사용자 업데이트 (미등록 원관리 추가)</button>
    </div>

    <!-- 하단: Volume 접근 현황 테이블 -->
    <div class="table-card">
        <div class="table-card-header">
            <h3>📊 사용자별 Volume 접근 현황</h3>
            <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                <div class="legend">
                    <div class="legend-item"><span class="legend-dot" style="background:#27ae60;"></span>접근 가능</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#4a90e2;"></span>전체 허용</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#e0e0e0;"></span>미접근</div>
                </div>
                <button class="refresh-btn" onclick="loadVolumeTable()">↻ 새로고침</button>
            </div>
        </div>

        <!-- 툴바: 검색 + 결과 건수 + 페이지 크기 -->
        <div class="table-toolbar">
            <div class="table-search-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" id="volSearch" placeholder="유치원명 검색..." oninput="onVolSearch()">
                <button class="clear-btn" id="volClearBtn" onclick="clearVolSearch()">×</button>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <span class="table-info" id="tableInfo"></span>
                <select class="page-size-select" id="pageSizeSelect" onchange="onPageSizeChange()">
                    <option value="10">10개씩</option>
                    <option value="20" selected>20개씩</option>
                    <option value="50">50개씩</option>
                    <option value="100">100개씩</option>
                </select>
            </div>
        </div>

        <div class="table-scroll">
            <div id="volumeTableContainer" class="table-loading">데이터를 불러오는 중...</div>
        </div>

        <!-- 페이지네이션 -->
        <div class="pagination" id="pagination"></div>
    </div>

</div>
<script src="../common.js?v=<?= filemtime('../common.js') ?>"></script>
<script>
const API_URL = 'api.php';
let allUsers = [];

let user = getUser();
if (user !== "chaitalk")
        window.location.href = "../index.php";

// 1. 초기화
document.addEventListener("DOMContentLoaded", () => {
    const grid = document.getElementById('monthGrid');
    for (let i = 1; i <= 12; i++) {
        grid.innerHTML += `<div class="month-item"><input type="checkbox" class="m-idx" value="${i}"> ${i}호</div>`;
    }
    loadUsers('all_list');
    loadVolumeTable();
});

// 2. 사용자 목록 로드
async function loadUsers(filterType, tabElement = null) {
    if (tabElement) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        tabElement.classList.add('active');
    }
    try {
        const res = await fetch(`${API_URL}?mode=list&filter=${filterType}`).then(r => r.json());
        if (res.status === 'success') {
            allUsers = res.data;
            renderUserOptions(allUsers);
        }
    } catch (e) { console.error("로드 실패:", e); }

    // 하단 테이블도 탭 필터 적용
    if (volAllData.length) applyVolFilter();
}

// 3. 옵션 렌더링
function renderUserOptions(users) {
    const combo = document.getElementById('userCombo');
    combo.innerHTML = '<option value="">-- 선택하세요 --</option>';
    if (users.length === 0) {
        const opt = document.createElement('option');
        opt.textContent = "검색 결과가 없습니다.";
        opt.disabled = true;
        combo.appendChild(opt);
        return;
    }
    users.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id;
        opt.textContent = `${u.name} (${u.id})`;
        combo.appendChild(opt);
    });
}

// 4. 검색 필터
function filterUsers() {
    const keyword = document.getElementById('userSearch').value.toLowerCase();
    const filtered = allUsers.filter(u =>
        u.name.toLowerCase().includes(keyword) ||
        u.id.toLowerCase().includes(keyword)
    );
    renderUserOptions(filtered);
    if (filtered.length === 1 && keyword !== "") {
        document.getElementById('userCombo').value = filtered[0].id;
        fetchUserData();
    }
}

// 5. 상세 조회
async function fetchUserData() {
    const id = document.getElementById('userCombo').value;
    if (!id) return;
    const res = await fetch(`${API_URL}?mode=get&id=${id}`).then(r => r.json());
    if (res.status === 'success') {
        const { status, mon } = res.data;
        document.getElementById('statusSelect').value = status;
        const checks = document.querySelectorAll('.m-idx');
        const activeMonths = mon ? String(mon).split(',').map(m => m.trim()) : [];
        checks.forEach(c => c.checked = activeMonths.includes(c.value));
        toggleUI();

        // 선택된 유치원명을 하단 테이블 검색창에 자동 입력 후 필터링
        const selectedText = document.getElementById('userCombo').selectedOptions[0]?.text ?? '';
        const nameOnly = selectedText.replace(/\s*\(.*\)$/, ''); // "(ID)" 부분 제거
        document.getElementById('volSearch').value = nameOnly;
        document.getElementById('volClearBtn').classList.add('visible');
        volFiltered = volAllData.filter(u => u.name.toLowerCase().includes(nameOnly.toLowerCase()));
        volCurrentPage = 1;
        renderVolumePage();

        // 테이블에서 해당 행 하이라이트
        highlightTableRow(id);
    }
}

function toggleUI() {
    const status = document.getElementById('statusSelect').value;
    const grid = document.getElementById('monthGrid');
    grid.classList.toggle('active', status === 'partial');
    const select = document.getElementById('statusSelect');
    select.className = `bg-${status}`;
}

// 6-0. 사용자 동기화 (role=2 미등록자 INSERT)
async function syncUsers() {
    if (!confirm('chaitalk_user에서 role=2(원관리) 중 미등록 사용자를 추가합니다.\n계속하시겠습니까?')) return;
    try {
        const res = await fetch(`${API_URL}?mode=sync_users`).then(r => r.json());
        if (res.status === 'success') {
            alert(`완료: ${res.inserted}명 추가됨`);
            const activeTab = document.querySelector('.tab.active');
            activeTab.click();
            if (typeof loadVolumeTable === 'function') loadVolumeTable();
        } else {
            alert('오류: ' + res.message);
        }
    } catch (e) {
        alert('요청 실패: ' + e.message);
    }
}

// 6. 저장
async function submitUpdate() {
    const id = document.getElementById('userCombo').value;
    if (!id) return alert('사용자를 선택해주세요.');
    const status = document.getElementById('statusSelect').value;
    const months = Array.from(document.querySelectorAll('.m-idx:checked')).map(c => c.value);
    const res = await fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, status, months: months.join(',') })
    }).then(r => r.json());
    if (res.status === 'success') {
        alert('성공적으로 변경되었습니다.');
        const activeTab = document.querySelector('.tab.active');
        activeTab.click();
        loadVolumeTable();
    }
}

// ── 7. Volume 현황 테이블 (검색 + 페이지네이션) ──
let volAllData    = [];   // 원본 전체 데이터
let volFiltered   = [];   // 검색 필터 후 데이터
let volCurrentPage = 1;
let volPageSize    = 20;

async function loadVolumeTable() {
    const container = document.getElementById('volumeTableContainer');
    container.innerHTML = '<div class="table-loading">데이터를 불러오는 중...</div>';
    document.getElementById('pagination').innerHTML = '';
    document.getElementById('tableInfo').textContent = '';

    try {
        // API 1번으로 전체 데이터 로드 (list_with_volume 모드 권장, 없으면 list fallback)
        const res = await fetch(`${API_URL}?mode=list_with_volume`).then(r => r.json());
        if (res.status !== 'success' || !res.data.length) {
            container.innerHTML = '<div class="table-loading">데이터가 없습니다.</div>';
            return;
        }

        // detail 구조 정리
        volAllData = res.data.map(u => ({
            ...u,
            detail: { status: u.status || 'all', mon: u.mon || '' }
        }));

        // 검색어 초기화
        document.getElementById('volSearch').value = '';
        document.getElementById('volClearBtn').classList.remove('visible');

        volCurrentPage = 1;
        volFiltered = [...volAllData];
        renderVolumePage();

    } catch (e) {
        container.innerHTML = '<div class="table-loading">데이터 로드 실패. 다시 시도해 주세요.</div>';
        console.error(e);
    }
}

// 현재 활성 탭의 status 필터값 반환
function getActiveTabFilter() {
    const activeTab = document.querySelector('.tab.active');
    if (!activeTab) return 'all_list';
    const map = { '전체': 'all_list', '전체 허용': 'all', '부분 허용': 'partial', '접속 금지': 'none' };
    return map[activeTab.textContent.trim()] ?? 'all_list';
}

// 검색어 + 탭 필터 통합 적용
function applyVolFilter() {
    const keyword    = document.getElementById('volSearch').value.trim().toLowerCase();
    const tabFilter  = getActiveTabFilter();

    volFiltered = volAllData.filter(u => {
        const matchStatus = tabFilter === 'all_list' || (u.detail.status || 'all') === tabFilter;
        const matchName   = !keyword || u.name.toLowerCase().includes(keyword);
        return matchStatus && matchName;
    });

    volCurrentPage = 1;
    renderVolumePage();
}

// 검색 입력 처리
function onVolSearch() {
    const keyword = document.getElementById('volSearch').value.trim();
    document.getElementById('volClearBtn').classList.toggle('visible', keyword !== '');
    applyVolFilter();
}

// 검색 초기화
function clearVolSearch() {
    document.getElementById('volSearch').value = '';
    document.getElementById('volClearBtn').classList.remove('visible');
    applyVolFilter();
}

// 페이지 크기 변경
function onPageSizeChange() {
    volPageSize = parseInt(document.getElementById('pageSizeSelect').value);
    volCurrentPage = 1;
    renderVolumePage();
}

// 현재 페이지 렌더링
function renderVolumePage() {
    const total      = volFiltered.length;
    const totalPages = Math.max(1, Math.ceil(total / volPageSize));
    if (volCurrentPage > totalPages) volCurrentPage = totalPages;

    const start = (volCurrentPage - 1) * volPageSize;
    const end   = Math.min(start + volPageSize, total);
    const pageData = volFiltered.slice(start, end);

    // 결과 건수 표시
    const keyword = document.getElementById('volSearch').value.trim();
    document.getElementById('tableInfo').innerHTML = keyword
        ? `검색결과 <strong>${total}개</strong> / 전체 ${volAllData.length}개`
        : `전체 <strong>${total}개</strong>`;

    // 테이블 렌더링
    const container = document.getElementById('volumeTableContainer');
    if (total === 0) {
        container.innerHTML = '<div class="table-loading">검색 결과가 없습니다.</div>';
        document.getElementById('pagination').innerHTML = '';
        return;
    }

    let html = `<table class="vol-table">
        <thead><tr><th style="text-align:center; min-width:40px;">No.</th><th>유치원명</th>`;
    for (let i = 1; i <= 12; i++) html += `<th>${i}호</th>`;
    html += `<th>상태</th></tr></thead><tbody>`;

    pageData.forEach((u, idx) => {
        const rowNum = start + idx + 1;
        const status = u.detail.status || 'all';
        const mon = u.detail.mon ? String(u.detail.mon).split(',').map(m => m.trim()) : [];

        html += `<tr id="row-${u.id}" onclick="selectUserFromTable('${u.id}')">`;
        html += `<td style="text-align:center; color:#aaa; font-size:12px; font-weight:500;">${rowNum}</td>`;
        html += `<td>${u.name}</td>`;

        for (let i = 1; i <= 12; i++) {
            const iStr = String(i);
            if (status === 'all') {
                html += `<td class="vol-cell-all">●</td>`;
            } else if (status === 'partial') {
                html += mon.includes(iStr)
                    ? `<td class="vol-cell-on">O</td>`
                    : `<td class="vol-cell-off">–</td>`;
            } else {
                html += `<td class="vol-cell-off">–</td>`;
            }
        }

        const badgeClass = status === 'all' ? 'badge-all' : status === 'partial' ? 'badge-partial' : 'badge-none';
        const badgeText  = status === 'all' ? '전체허용' : status === 'partial' ? '부분허용' : '접속금지';
        html += `<td><span class="badge ${badgeClass}">${badgeText}</span></td></tr>`;
    });

    html += `</tbody></table>`;
    container.innerHTML = html;

    // 하이라이트 유지
    const selectedId = document.getElementById('userCombo').value;
    if (selectedId) highlightTableRow(selectedId);

    // 페이지네이션 렌더링
    renderPagination(totalPages, start, end, total);
}

function renderPagination(totalPages, start, end, total) {
    const pg = document.getElementById('pagination');
    if (totalPages <= 1) { pg.innerHTML = ''; return; }

    const cur = volCurrentPage;
    let html = '';

    // 이전 버튼
    html += `<button class="page-btn" onclick="goPage(${cur-1})" ${cur===1 ? 'disabled' : ''}>‹</button>`;

    // 페이지 번호 (최대 7개 노출)
    const range = pageRange(cur, totalPages);
    range.forEach(p => {
        if (p === '...') {
            html += `<span style="padding:0 4px; color:#aaa; font-size:13px;">…</span>`;
        } else {
            html += `<button class="page-btn ${p === cur ? 'active' : ''}" onclick="goPage(${p})">${p}</button>`;
        }
    });

    // 다음 버튼
    html += `<button class="page-btn" onclick="goPage(${cur+1})" ${cur===totalPages ? 'disabled' : ''}>›</button>`;

    // 위치 표시
    html += `<span style="font-size:12px; color:#aaa; margin-left:6px;">${start+1}–${end} / ${total}</span>`;

    pg.innerHTML = html;
}

// 페이지 번호 범위 계산 (1 ... 4 5 6 ... 10 형태)
function pageRange(cur, total) {
    if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
    const pages = [];
    if (cur <= 4) {
        for (let i = 1; i <= 5; i++) pages.push(i);
        pages.push('...', total);
    } else if (cur >= total - 3) {
        pages.push(1, '...');
        for (let i = total - 4; i <= total; i++) pages.push(i);
    } else {
        pages.push(1, '...', cur - 1, cur, cur + 1, '...', total);
    }
    return pages;
}

function goPage(p) {
    const totalPages = Math.ceil(volFiltered.length / volPageSize);
    if (p < 1 || p > totalPages) return;
    volCurrentPage = p;
    renderVolumePage();
    document.querySelector('.table-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// 테이블 행 클릭 → 상단 콤보박스에 자동 선택
function selectUserFromTable(id) {
    const combo = document.getElementById('userCombo');
    const exists = Array.from(combo.options).some(o => o.value === id);
    if (!exists) {
        const allTab = document.querySelector('.tab');
        loadUsers('all_list', allTab).then(() => {
            document.getElementById('userCombo').value = id;
            fetchUserData();
        });
    } else {
        combo.value = id;
        fetchUserData();
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function highlightTableRow(id) {
    document.querySelectorAll('.vol-table tbody tr').forEach(tr => tr.classList.remove('selected-row'));
    const row = document.getElementById(`row-${id}`);
    if (row) row.classList.add('selected-row');
}
</script>
</body>
</html>