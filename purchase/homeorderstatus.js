(() => {
  const $ = id => document.getElementById(id);
  const admin = Number(document.body.dataset.role) === 9;
  const filters = { month: $('fMonth'), branch: $('fBranch'), teacher: $('fTeacher'), student: $('fStudent'), status: $('fStatus') };
  const value = el => (el ? el.value : '');
  const branchName = o => o.branch_owner || o.branch_name || o.branch_id;
  const teacherName = o => o.teacher_name || o.teacher_id;
  const studentName = o => `${o.student_name || ''} (${o.student_id})`;
  // [정렬키, 제목, 값] — 본사는 지사명 칸 추가
  const columns = [
    ...(admin ? [['branch', '지사명', branchName]] : []),
    ['round', 'No', o => o.round],
    ['order_date', '주문일자', o => (o.order_date || '').slice(0, 10)],
    ['teacher', '교사명', teacherName],
    ['student', '학생명', o => o.student_name || o.student_id],
    ['material', '교재명', o => `${GP_ROLE.courseLabel(o.material_title)} (${o.material_code})`],
    ['complete_date', '주문완료일', o => (o.status === '1' ? (o.complete_date || '').slice(0, 10).replaceAll('-', '.') : '')],
  ];
  let orders = [], sortKey = 'order_date', sortDir = -1;

  for (const [key, label] of columns) {
    const th = document.createElement('th');
    th.dataset.key = key; th.dataset.label = label;
    th.addEventListener('click', () => { sortDir = sortKey === key ? -sortDir : 1; sortKey = key; render(); });
    $('orderHead').appendChild(th);
  }

  function fill(el, pairs) {
    if (!el) return;
    const keep = el.value;
    el.replaceChildren(new Option('전체', ''), ...pairs.map(([v, t]) => new Option(t, v)));
    el.value = pairs.some(([v]) => v === keep) ? keep : '';
  }
  const unique = (rows, key, label) => [...new Map(rows.map(o => [o[key], label(o)])).entries()]
    .sort((a, b) => String(a[1]).localeCompare(String(b[1]), 'ko'));

  function render() {
    // 지사 → 선생님 → 학생 순으로 상위 선택에 맞춰 하위 목록을 좁힌다
    const byBranch = orders.filter(o => !value(filters.branch) || o.branch_id === value(filters.branch));
    fill(filters.teacher, unique(byBranch, 'teacher_id', teacherName));
    const byTeacher = byBranch.filter(o => !value(filters.teacher) || o.teacher_id === value(filters.teacher));
    fill(filters.student, unique(byTeacher, 'student_id', studentName));
    const status = value(filters.status);
    const visible = byTeacher.filter(o => (!value(filters.month) || o.order_month === value(filters.month))
      && (!value(filters.student) || o.student_id === value(filters.student))
      && (!status || (status === 'done') === (o.status === '1')));
    const get = columns.find(c => c[0] === sortKey)[2];
    visible.sort((a, b) => sortDir * String(get(a)).localeCompare(String(get(b)), 'ko', { numeric: true }));

    $('orderHead').querySelectorAll('th').forEach(th => {
      th.textContent = th.dataset.label + (th.dataset.key === sortKey ? (sortDir > 0 ? ' ▲' : ' ▼') : '');
    });
    const body = $('orderRows');
    body.replaceChildren();
    for (const o of visible) {
      const tr = body.insertRow();
      for (const [key, , getValue] of columns) {
        const td = tr.insertCell();
        if (key === 'round') td.textContent = `${o.round}차`;
        else if (key === 'complete_date' && o.status !== '1') { td.textContent = '신청 완료'; td.className = 'ho-pending'; }
        else td.textContent = getValue(o);
      }
    }
    if (!visible.length) {
      const td = body.insertRow().insertCell();
      td.colSpan = columns.length; td.textContent = '주문 내역이 없습니다.';
    }
    const pending = visible.filter(o => o.status !== '1').length;
    $('orderSummary').textContent = `${visible.length}건 (신청 완료 ${pending} / 주문 완료 ${visible.length - pending})`;
  }

  function load() {
    $('orderSummary').textContent = '불러오는 중...';
    CallAjax('SMethods.php', 'POST', { functionName: 'SHomeOrderHistory', otherData: {} }, response => {
      orders = response.success || [];
      const rounds = {};   // 학생별 누적 주문 차수 (서버가 주문일 순으로 보냄)
      for (const o of orders) o.round = rounds[o.student_id] = (rounds[o.student_id] || 0) + 1;
      fill(filters.month, [...new Set(orders.map(o => o.order_month))].sort().reverse().map(m => [m, m]));
      fill(filters.branch, unique(orders, 'branch_id', branchName));
      render();
    }, error => { $('orderSummary').textContent = error?.responseJSON?.error || '주문현황 조회에 실패했습니다.'; });
  }

  // 교재 신청 기간 — 모두 조회, 본사만 변경
  function showWindow(w) {
    $('windowInfo').textContent = `매월 ${w.start}일~${w.end}일 (오늘은 ${w.open ? '신청 가능' : '신청 불가'})`;
    if (admin) { $('windowStart').value = w.start; $('windowEnd').value = w.end; }
  }
  function loadWindow() {
    CallAjax('SMethods.php', 'POST', { functionName: 'SHomeOrderWindow', otherData: {} }, response => showWindow(response.success),
      error => { $('windowInfo').textContent = error?.responseJSON?.error || '신청 기간을 불러오지 못했습니다.'; });
  }
  if (admin) {
    $('saveWindow').addEventListener('click', () => {
      const start = Number($('windowStart').value), end = Number($('windowEnd').value);
      if (!confirm(`교재 신청 기간을 매월 ${start}일~${end}일로 바꿀까요?`)) return;
      CallAjax('SMethods.php', 'POST', { functionName: 'SSaveHomeOrderWindow', otherData: { start, end } }, response => {
        showWindow(response.success); CallToast('교재 신청 기간을 저장했습니다.', 'success');
      }, error => alert(error?.responseJSON?.error || '신청 기간 저장에 실패했습니다.'));
    });
  }

  Object.values(filters).forEach(el => el && el.addEventListener('change', render));
  $('reloadOrders').addEventListener('click', load);
  loadWindow();
  load();
})();
