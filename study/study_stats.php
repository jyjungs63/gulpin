<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>학습 통계</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script src="../js/common.js?v=<?= filemtime('../js/common.js') ?>"></script>
    <link rel="stylesheet" href="../role-nav.css">
    <script src="../js/role-nav.js" defer></script>
    <style>
        body { background: #f4f6f9; }
        .page-title { font-size: 1.4rem; font-weight: 700; color: #2c3e50; }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 16px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            text-align: center;
        }
        .stat-card .val { font-size: 2rem; font-weight: 700; color: #3498db; }
        .stat-card .lbl { font-size: .85rem; color: #7f8c8d; margin-top: 4px; }
        .tbl-wrap { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.08); overflow: hidden; }
        thead th { background: #2c3e50; color: #fff; font-weight: 600; white-space: nowrap; }
        tbody tr:hover { background: #f0f7ff; }
        .rank-badge {
            display: inline-block;
            width: 26px; height: 26px;
            border-radius: 50%;
            line-height: 26px;
            text-align: center;
            font-size: .8rem; font-weight: 700;
        }
        .rank-1 { background: #f1c40f; color: #fff; }
        .rank-2 { background: #bdc3c7; color: #fff; }
        .rank-3 { background: #e67e22; color: #fff; }
        .rank-n { background: #ecf0f1; color: #555; }
        .bar-wrap { background: #ecf0f1; border-radius: 4px; height: 8px; min-width: 80px; }
        .bar-fill  { background: #3498db; border-radius: 4px; height: 8px; transition: width .4s; }
        #idSpinner { display: none; }
        .tab-btn { border: none; background: #e9ecef; padding: 8px 20px; border-radius: 8px 8px 0 0; cursor: pointer; font-weight: 600; color: #555; }
        .tab-btn.active { background: #fff; color: #2c3e50; box-shadow: 0 -2px 4px rgba(0,0,0,.06); }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        .clickable-row { cursor: pointer; }
        .clickable-row:hover td { background: #dbeafe !important; }
        #detailPanel .tbl-wrap { margin-top: 12px; }
        .student-clickable { cursor: pointer; }
        .student-clickable:hover td { background: #fef3c7 !important; }
        .chart-area { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.08); padding: 20px; margin-top: 16px; }
        .chart-area canvas { max-height: 300px; }
    </style>
</head>
<body>
<header id="roleNav"></header>
<div class="container-fluid py-5 px-5">

    <!-- 헤더 -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <span class="page-title"><i class="fa-solid fa-chart-bar me-2 text-primary"></i>그룹별 학습 통계</span>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <input type="month" id="idMonth" class="form-control form-control-sm" style="width:150px">
            <button class="btn btn-sm btn-outline-secondary" id="btnAll">전체</button>
            <button class="btn btn-sm btn-primary" id="btnSearch">
                <i class="fa-solid fa-magnifying-glass me-1"></i>조회
            </button>
            <button class="btn btn-sm btn-danger" id="btnPdf" disabled>
                <i class="fa-solid fa-file-pdf me-1"></i>PDF 저장
            </button>
            <button class="btn btn-sm btn-success" id="btnExcel" disabled>
                <i class="fa-solid fa-file-excel me-1"></i>Excel 저장
            </button>
        </div>
    </div>

    <!-- PDF 캡처 대상 영역 -->
    <div id="pdfArea">

    <!-- PDF 전용 제목 (화면 숨김, 캡처 시 표시) -->
    <div id="pdfTitle" style="display:none; font-size:1.2rem; font-weight:700; color:#2c3e50; margin-bottom:16px; padding:4px 0;"></div>

    <!-- 요약 카드 -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="val" id="sumGroups">-</div>
                <div class="lbl">총 그룹 수</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="val" id="sumStudents">-</div>
                <div class="lbl">총 학생 수</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="val" id="sumStudy">-</div>
                <div class="lbl">총 학습 횟수</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="val" id="sumAvg">-</div>
                <div class="lbl">그룹 평균 학습</div>
            </div>
        </div>
    </div>

    <!-- 탭 버튼 -->
    <div class="d-flex gap-1 mb-0">
        <button class="tab-btn active" data-tab="groupPanel">그룹별 통계</button>
        <button class="tab-btn" data-tab="detailPanel" id="btnDetailTab" style="display:none">
            <i class="fa-solid fa-users me-1"></i><span id="detailTabLabel">학생별 상세</span>
        </button>
    </div>

    <!-- 그룹별 통계 패널 -->
    <div id="groupPanel" class="tab-panel active">
    <div class="tbl-wrap">
        <div class="p-3 d-flex align-items-center justify-content-between border-bottom">
            <span class="fw-semibold text-secondary" id="idPeriodLabel"></span>
            <div id="idSpinner" class="spinner-border spinner-border-sm text-primary"></div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width:60px">순위</th>
                        <th class="sortable" data-sort="branch_name" role="button" style="cursor:pointer">지사 <i class="fa-solid fa-sort fa-xs"></i></th>
                        <th>원/지사 ID</th>
                        <th>담당자명</th>
                        <th class="text-end sortable-col" data-sort="total_study_count" role="button" style="cursor:pointer">학습 횟수 <i class="fa-solid fa-sort fa-xs"></i></th>
                        <th class="text-center" style="width:160px">비율</th>
                        <th class="text-end sortable-col" data-sort="student_count" role="button" style="cursor:pointer">학생 수 <i class="fa-solid fa-sort fa-xs"></i></th>
                        <th class="text-end sortable-col" data-sort="avg" role="button" style="cursor:pointer">학생당 평균 <i class="fa-solid fa-sort fa-xs"></i></th>
                    </tr>
                </thead>
                <tbody id="idTableBody">
                    <tr><td colspan="8" class="text-center py-5 text-muted">조회 버튼을 눌러주세요</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <!-- 학생별 상세 패널 -->
    <div id="detailPanel" class="tab-panel">
    <!-- 전체 학생 학습 횟수 차트 -->
    <div id="allStudentsChartArea" class="chart-area mb-3" style="display:none">
        <h6 class="fw-bold text-secondary mb-2"><i class="fa-solid fa-chart-column me-1"></i><span id="allStudentsChartLabel">학생별 학습 횟수</span></h6>
        <div style="position:relative; height:280px;">
            <canvas id="chartAllStudents"></canvas>
        </div>
    </div>
    <div class="tbl-wrap">
        <div class="p-3 d-flex align-items-center justify-content-between border-bottom">
            <span class="fw-semibold text-secondary" id="detailPeriodLabel"></span>
            <button class="btn btn-sm btn-outline-secondary" id="btnBackGroup">
                <i class="fa-solid fa-arrow-left me-1"></i>그룹 목록으로
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width:60px">순위</th>
                        <th>학생 ID</th>
                        <th>학생명</th>
                        <th>반(원명)</th>
                        <th class="text-end">학습 횟수</th>
                        <th class="text-center" style="width:160px">비율</th>
                        <th class="text-end">최근 학습일</th>
                    </tr>
                </thead>
                <tbody id="idDetailBody">
                </tbody>
            </table>
        </div>
    </div>
    <!-- 학생 개인 차트 영역 -->
    <div id="studentChartArea" class="chart-area" style="display:none">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="fw-bold text-secondary" id="chartStudentLabel"></span>
            <button class="btn btn-sm btn-outline-secondary" id="btnCloseChart" title="차트 닫기">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="row g-3">
            <div class="col-md-8">
                <h6 class="text-center text-muted mb-2">일별 학습 횟수</h6>
                <canvas id="chartDaily"></canvas>
            </div>
            <div class="col-md-4">
                <h6 class="text-center text-muted mb-2">단계별 비율</h6>
                <canvas id="chartStep"></canvas>
            </div>
        </div>
    </div>
    </div>

    </div><!-- /#pdfArea -->
</div>

<script>
    // 로그인 체크
    (function() {
        const role = getRole();
        if (!role || !['9','30','3','2'].includes(String(role))) {
            alert('접근 권한이 없습니다.');
            location.href = '../index.php';
        }
    })();

    const API_URL = 'api.php';
    const _userRole = String(getRole());
    const _userId   = String(getUser());

    // 현재 달 기본 설정
    (function() {
        const now = new Date();
        const y   = now.getFullYear();
        const m   = String(now.getMonth() + 1).padStart(2, '0');
        document.getElementById('idMonth').value = `${y}-${m}`;
    })();

    function setSpinner(on) {
        document.getElementById('idSpinner').style.display = on ? 'inline-block' : 'none';
    }

    function load(month) {
        setSpinner(true);
        let url = API_URL + '?mode=study_stats' + (month ? '&month=' + encodeURIComponent(month) : '');
        // role=2/3/30은 본인 ID 기반 필터링
        if (['2','3','30'].includes(_userRole)) {
            url += '&id=' + encodeURIComponent(_userId) + '&role=' + encodeURIComponent(_userRole);
        }
        fetch(url)
            .then(r => r.json())
            .then(res => {
                setSpinner(false);
                if (res.status !== 'success') { alert('조회 오류: ' + res.message); return; }
                render(res.data, month);
            })
            .catch(() => { setSpinner(false); alert('서버 오류'); });
    }

    let _lastRows = [];
    let _lastMonth = '';
    let _branchSortDir = 0; // 0: 기본(학습횟수순), 1: 지사 오름차순, 2: 지사 내림차순

    function render(rows, month) {
        _lastRows = rows;
        _lastMonth = month;

        // 기간 라벨
        document.getElementById('idPeriodLabel').textContent =
            month ? month + ' 기준' : '전체 기간';

        const totalStudy    = rows.reduce((s, r) => s + Number(r.total_study_count), 0);
        const totalStudents = rows.reduce((s, r) => s + Number(r.student_count), 0);
        const avg = rows.length ? Math.round(totalStudy / rows.length) : 0;

        document.getElementById('sumGroups').textContent   = rows.length.toLocaleString();
        document.getElementById('sumStudents').textContent = totalStudents.toLocaleString();
        document.getElementById('sumStudy').textContent    = totalStudy.toLocaleString();
        document.getElementById('sumAvg').textContent      = avg.toLocaleString();

        document.getElementById('btnPdf').disabled = !rows.length;
        document.getElementById('btnExcel').disabled = !rows.length;

        renderTable(rows);
    }

    function renderTable(rows) {
        const maxCount = rows.length ? Math.max(...rows.map(r => Number(r.total_study_count))) : 1;
        const tbody = document.getElementById('idTableBody');

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted">데이터가 없습니다</td></tr>';
            return;
        }

        tbody.innerHTML = rows.map((r, i) => {
            const rank    = i + 1;
            const cnt     = Number(r.total_study_count);
            const stuCnt  = Number(r.student_count);
            const perStu  = stuCnt > 0 ? (cnt / stuCnt).toFixed(1) : '0.0';
            const pct     = maxCount > 0 ? Math.round(cnt / maxCount * 100) : 0;
            const rClass  = rank === 1 ? 'rank-1' : rank === 2 ? 'rank-2' : rank === 3 ? 'rank-3' : 'rank-n';

            return `<tr class="clickable-row" data-tid="${esc(r.tid ?? '')}" data-teacher="${esc(r.teacher_name ?? '')}">
                <td class="text-center">
                    <span class="rank-badge ${rClass}">${rank}</span>
                </td>
                <td>${esc(r.branch_name || '-')}</td>
                <td class="fw-semibold">${esc(r.tid ?? '-')}</td>
                <td>${esc(r.teacher_name ?? '-')}</td>
                <td class="text-end fw-bold text-primary">${cnt.toLocaleString()}</td>
                <td class="text-center">
                    <div class="bar-wrap">
                        <div class="bar-fill" style="width:${pct}%"></div>
                    </div>
                </td>
                <td class="text-end">${stuCnt.toLocaleString()}</td>
                <td class="text-end text-muted">${perStu}</td>
            </tr>`;
        }).join('');

        // 원 클릭 → 학생별 상세
        tbody.querySelectorAll('.clickable-row').forEach(tr => {
            tr.addEventListener('click', function() {
                const tid = this.dataset.tid;
                const teacher = this.dataset.teacher;
                loadDetail(tid, teacher);
            });
        });
    }

    // 지사 컬럼 클릭 정렬
    document.querySelector('th[data-sort="branch_name"]').addEventListener('click', function() {
        _branchSortDir = (_branchSortDir + 1) % 3;
        let sorted;
        if (_branchSortDir === 0) {
            sorted = [..._lastRows].sort((a, b) => Number(b.total_study_count) - Number(a.total_study_count));
            this.querySelector('i').className = 'fa-solid fa-sort fa-xs';
        } else if (_branchSortDir === 1) {
            sorted = [..._lastRows].sort((a, b) => (a.branch_name || '').localeCompare(b.branch_name || '', 'ko'));
            this.querySelector('i').className = 'fa-solid fa-sort-up fa-xs';
        } else {
            sorted = [..._lastRows].sort((a, b) => (b.branch_name || '').localeCompare(a.branch_name || '', 'ko'));
            this.querySelector('i').className = 'fa-solid fa-sort-down fa-xs';
        }
        renderTable(sorted);
    });

    // 학습 횟수 / 학생 수 / 학생당 평균 정렬
    let _colSortState = {}; // { 'total_study_count': 0, 'student_count': 0, 'avg': 0 }

    document.querySelectorAll('.sortable-col').forEach(th => {
        _colSortState[th.dataset.sort] = 0;

        th.addEventListener('click', function() {
            const col = this.dataset.sort;

            // 다른 정렬 컬럼 초기화
            document.querySelectorAll('.sortable-col').forEach(h => {
                if (h.dataset.sort !== col) {
                    _colSortState[h.dataset.sort] = 0;
                    h.querySelector('i').className = 'fa-solid fa-sort fa-xs';
                }
            });
            // 지사 정렬도 초기화
            _branchSortDir = 0;
            document.querySelector('th[data-sort="branch_name"] i').className = 'fa-solid fa-sort fa-xs';

            _colSortState[col] = (_colSortState[col] + 1) % 3;
            const dir = _colSortState[col];

            const getValue = (r) => {
                if (col === 'total_study_count') return Number(r.total_study_count);
                if (col === 'student_count') return Number(r.student_count);
                if (col === 'avg') {
                    const sc = Number(r.student_count);
                    return sc > 0 ? Number(r.total_study_count) / sc : 0;
                }
                return 0;
            };

            let sorted;
            if (dir === 0) {
                sorted = [..._lastRows].sort((a, b) => Number(b.total_study_count) - Number(a.total_study_count));
                this.querySelector('i').className = 'fa-solid fa-sort fa-xs';
            } else if (dir === 1) {
                sorted = [..._lastRows].sort((a, b) => getValue(b) - getValue(a));
                this.querySelector('i').className = 'fa-solid fa-sort-down fa-xs';
            } else {
                sorted = [..._lastRows].sort((a, b) => getValue(a) - getValue(b));
                this.querySelector('i').className = 'fa-solid fa-sort-up fa-xs';
            }
            renderTable(sorted);
        });
    });

    // === 탭 전환 ===
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(this.dataset.tab).classList.add('active');
        });
    });

    // 그룹 목록으로 돌아가기
    document.getElementById('btnBackGroup').addEventListener('click', function() {
        document.querySelector('.tab-btn[data-tab="groupPanel"]').click();
    });

    // === 학생별 상세 로딩 ===
    let _detailRows = [];

    function loadDetail(tid, teacherName) {
        const month = document.getElementById('idMonth').value;
        let url = API_URL + '?mode=study_stats_detail&tid=' + encodeURIComponent(tid);
        if (month) url += '&month=' + encodeURIComponent(month);

        document.getElementById('detailTabLabel').textContent = (teacherName || tid) + ' 학생별 상세';
        document.getElementById('btnDetailTab').style.display = '';
        document.getElementById('detailPeriodLabel').textContent =
            (teacherName || tid) + (month ? ' (' + month + ')' : ' (전체 기간)');

        // 탭 전환
        document.querySelector('.tab-btn[data-tab="detailPanel"]').click();

        const tbody = document.getElementById('idDetailBody');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> 로딩 중...</td></tr>';

        fetch(url)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') { alert('조회 오류: ' + res.message); return; }
                _detailRows = res.data;
                renderDetail(res.data);
            })
            .catch(() => { alert('서버 오류'); });
    }

    let _chartAllStudents = null;

    function renderDetail(rows) {
        const tbody = document.getElementById('idDetailBody');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">데이터가 없습니다</td></tr>';
            document.getElementById('studentChartArea').style.display = 'none';
            document.getElementById('allStudentsChartArea').style.display = 'none';
            return;
        }
        const maxCount = Math.max(...rows.map(r => Number(r.study_count)));

        // 전체 학생 이름별 학습 횟수 차트
        drawAllStudentsChart(rows);

        tbody.innerHTML = rows.map((r, i) => {
            const rank   = i + 1;
            const cnt    = Number(r.study_count);
            const pct    = maxCount > 0 ? Math.round(cnt / maxCount * 100) : 0;
            const rClass = rank === 1 ? 'rank-1' : rank === 2 ? 'rank-2' : rank === 3 ? 'rank-3' : 'rank-n';
            const lastDt = r.last_study ? r.last_study.slice(0, 10) : '-';

            return `<tr class="student-clickable" data-sid="${esc(r.student_id)}" data-sname="${esc(r.student_name)}">
                <td class="text-center"><span class="rank-badge ${rClass}">${rank}</span></td>
                <td class="fw-semibold">${esc(r.student_id)}</td>
                <td>${esc(r.student_name)}</td>
                <td>${esc(r.class_name || '-')}</td>
                <td class="text-end fw-bold text-primary">${cnt.toLocaleString()}</td>
                <td class="text-center">
                    <div class="bar-wrap"><div class="bar-fill" style="width:${pct}%"></div></div>
                </td>
                <td class="text-end text-muted">${lastDt}</td>
            </tr>`;
        }).join('');

        // 학생 클릭 → 차트
        tbody.querySelectorAll('.student-clickable').forEach(tr => {
            tr.addEventListener('click', function() {
                loadStudentChart(this.dataset.sid, this.dataset.sname);
            });
        });
    }

    // === 학생 개인 차트 ===
    let _chartDaily = null;
    let _chartStep  = null;

    function loadStudentChart(studentId, studentName) {
        const month = document.getElementById('idMonth').value;
        let url = API_URL + '?mode=study_stats_student_chart&student_id=' + encodeURIComponent(studentId);
        if (month) url += '&month=' + encodeURIComponent(month);

        const area = document.getElementById('studentChartArea');
        document.getElementById('chartStudentLabel').textContent =
            studentName + ' (' + studentId + ')' + (month ? ' — ' + month : ' — 전체 기간');
        area.style.display = 'block';
        area.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        fetch(url)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') { alert('차트 조회 오류: ' + res.message); return; }
                drawDailyChart(res.daily);
                drawStepChart(res.byStep);
            })
            .catch(() => alert('차트 서버 오류'));
    }

    function drawDailyChart(daily) {
        const ctx = document.getElementById('chartDaily').getContext('2d');
        if (_chartDaily) _chartDaily.destroy();

        const labels = daily.map(d => d.study_date.slice(5)); // MM-DD
        const data   = daily.map(d => Number(d.cnt));

        _chartDaily = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '학습 횟수',
                    data: data,
                    backgroundColor: 'rgba(52, 152, 219, 0.6)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { maxRotation: 45, font: { size: 10 } } }
                }
            }
        });
    }

    function drawStepChart(byStep) {
        const ctx = document.getElementById('chartStep').getContext('2d');
        if (_chartStep) _chartStep.destroy();

        const stepNames = { '0': '한자뚝0', 'A': '한자뚝A', 'B': '한자뚝B', 'T': '어휘담' };
        const colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c'];

        const labels = byStep.map(d => stepNames[d.step] || d.step);
        const data   = byStep.map(d => Number(d.cnt));

        _chartStep = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, data.length),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 } } }
                }
            }
        });
    }

    // 차트 닫기
    document.getElementById('btnCloseChart').addEventListener('click', function() {
        document.getElementById('studentChartArea').style.display = 'none';
        if (_chartDaily) { _chartDaily.destroy(); _chartDaily = null; }
        if (_chartStep)  { _chartStep.destroy();  _chartStep  = null; }
    });

    // === 전체 학생 이름별 학습 횟수 차트 ===
    function drawAllStudentsChart(rows) {
        const area = document.getElementById('allStudentsChartArea');
        area.style.display = 'block';

        if (_chartAllStudents) _chartAllStudents.destroy();

        const labels = rows.map(r => r.student_name || r.student_id);
        const data   = rows.map(r => Number(r.study_count));

        // 색상: 1~3위 강조
        const bgColors = data.map((_, i) =>
            i === 0 ? '#f1c40f' : i === 1 ? '#bdc3c7' : i === 2 ? '#e67e22' : 'rgba(52,152,219,0.6)'
        );

        const ctx = document.getElementById('chartAllStudents').getContext('2d');
        _chartAllStudents = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '학습 횟수',
                    data: data,
                    backgroundColor: bgColors,
                    borderColor: bgColors.map(c => c.replace('0.6', '1')),
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: rows.length > 15 ? 'y' : 'x',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed[rows.length > 15 ? 'x' : 'y'] + '회'
                        }
                    }
                },
                scales: {
                    ...(rows.length > 15
                        ? { x: { beginAtZero: true, ticks: { stepSize: 1 } }, y: { ticks: { font: { size: 11 } } } }
                        : { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { ticks: { maxRotation: 45, font: { size: 11 } } } }
                    )
                }
            }
        });

        document.getElementById('allStudentsChartLabel').textContent =
            document.getElementById('detailPeriodLabel').textContent + ' — 학생별 학습 횟수';
    }

    // === Excel 저장 ===
    document.getElementById('btnExcel').addEventListener('click', function() {
        const month = document.getElementById('idMonth').value;
        const wb = XLSX.utils.book_new();

        // 시트1: 그룹별 통계
        if (_lastRows.length) {
            const groupData = _lastRows.map((r, i) => ({
                '순위': i + 1,
                '지사': r.branch_name || '-',
                '원/지사 ID': r.tid || '-',
                '담당자명': r.teacher_name || '-',
                '학습 횟수': Number(r.total_study_count),
                '학생 수': Number(r.student_count),
                '학생당 평균': Number(r.student_count) > 0
                    ? Number((Number(r.total_study_count) / Number(r.student_count)).toFixed(1))
                    : 0
            }));
            const ws1 = XLSX.utils.json_to_sheet(groupData);
            XLSX.utils.book_append_sheet(wb, ws1, '그룹별 통계');
        }

        // 시트2: 학생별 상세 (열려있으면)
        if (_detailRows.length) {
            const detailData = _detailRows.map((r, i) => ({
                '순위': i + 1,
                '학생 ID': r.student_id,
                '학생명': r.student_name,
                '반(원명)': r.class_name || '-',
                '학습 횟수': Number(r.study_count),
                '최근 학습일': r.last_study ? r.last_study.slice(0, 10) : '-'
            }));
            const ws2 = XLSX.utils.json_to_sheet(detailData);
            const label = document.getElementById('detailTabLabel').textContent || '학생별';
            XLSX.utils.book_append_sheet(wb, ws2, label.slice(0, 31));
        }

        XLSX.writeFile(wb, '학습통계_' + (month || '전체') + '.xlsx');
    });

    function esc(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    document.getElementById('btnSearch').addEventListener('click', () => {
        load(document.getElementById('idMonth').value);
    });
    document.getElementById('btnAll').addEventListener('click', () => {
        document.getElementById('idMonth').value = '';
        load('');
    });

    // PDF 저장
    document.getElementById('btnPdf').addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>생성 중...';

        try {
            const { jsPDF } = window.jspdf;
            const area      = document.getElementById('pdfArea');
            const titleEl   = document.getElementById('pdfTitle');
            const period    = document.getElementById('idPeriodLabel').textContent || '통계';

            // 캡처 전: 제목 표시
            titleEl.textContent = '그룹별 학습 통계  (' + period + ')';
            titleEl.style.display = 'block';

            const canvas = await html2canvas(area, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#f4f6f9',
                scrollY: -window.scrollY
            });

            // 캡처 후: 제목 다시 숨김
            titleEl.style.display = 'none';

            const imgW  = 210; // A4 가로 mm
            const imgH  = (canvas.height * imgW) / canvas.width;
            const pageH = 297; // A4 세로 mm

            const pdf = new jsPDF({ unit: 'mm', format: 'a4' });

            let posY  = 10;
            let remaining = imgH;

            while (remaining > 0) {
                const sliceH  = Math.min(pageH - posY, remaining);
                const srcY    = (imgH - remaining) * (canvas.height / imgH);
                const srcH    = sliceH * (canvas.height / imgH);

                const sliceCanvas  = document.createElement('canvas');
                sliceCanvas.width  = canvas.width;
                sliceCanvas.height = srcH;
                sliceCanvas.getContext('2d').drawImage(canvas, 0, srcY, canvas.width, srcH, 0, 0, canvas.width, srcH);

                pdf.addImage(sliceCanvas.toDataURL('image/jpeg', 0.95), 'JPEG', 0, posY, imgW, sliceH);
                remaining -= sliceH;

                if (remaining > 0) { pdf.addPage(); posY = 10; }
            }

            const month = document.getElementById('idMonth').value;
            pdf.save('학습통계_' + (month || '전체') + '.pdf');

        } catch (e) {
            alert('PDF 생성 오류: ' + e.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-file-pdf me-1"></i>PDF 저장';
        }
    });

    // 초기 로딩
    load(document.getElementById('idMonth').value);
</script>
</body>
</html>
