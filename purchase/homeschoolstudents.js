(() => {
  const form = document.getElementById('studentForm');
  const notice = document.getElementById('notice');
  const saveButton = document.getElementById('saveStudent');
  const resetButton = document.getElementById('resetStudent');
  const reloadButton = document.getElementById('reloadStudents');
  const search = document.getElementById('studentSearch');
  const bulkButton = document.getElementById('bulkOrder');
  const checkAll = document.getElementById('checkAll');
  let students = [], materials = [], selectable = [], course = '방문수업', originalId = '', busy = false, ready = false;
  let orderWindow = { start: 10, end: 15, open: false };   // 교재 신청 기간 (서버 SHomeschoolStudents order_window)
  const fields = ['course', 'name', 'id', 'passwd', 'birth_year', 'classnm', 'p_mobile', 'material_code', 'sdate', 'edate'];
  const lockedOnEdit = ['name', 'id', 'material_code', 'sdate'];   // 수정 시 바꿀 수 없는 항목 (단계는 목록에서만 변경)
  const loginInfo = (() => { try { return JSON.parse(localStorage.getItem('infochaitalk')) || {}; } catch (_) { return {}; } })();
  // 예전 데이터의 '홈스쿨'도 화면에는 '방문수업'으로 보여 준다
  const label = text => String(text ?? '').replace(/홈스쿨/g, '방문수업');
  document.getElementById('pageTitle').textContent =
    `${[loginInfo.owner, loginInfo.name].filter(Boolean).join('-')}${loginInfo.owner || loginInfo.name ? ' : ' : ''}방문수업 학생관리`;
  form.elements.birth_year.max = new Date().getFullYear();
  const request = (functionName, otherData = {}) => new Promise((resolve, reject) => {
    CallAjax('SMethods.php', 'POST', { functionName, otherData }, resolve, reject);
  });
  function message(text = '', success = false) {
    notice.textContent = text;
    notice.classList.toggle('success', success);
  }
  function setBusy(value) {
    busy = value;
    saveButton.disabled = value || !ready;
    resetButton.disabled = reloadButton.disabled = value;
    for (const field of fields) form.elements[field].disabled = field === 'edate' || value || (!ready && ['course', 'material_code'].includes(field))   // 수업 종료일은 목록·수업종료로만 변경
      || (originalId !== '' && lockedOnEdit.includes(field));
    document.querySelectorAll('#studentRows button, #studentRows select, #studentRows input').forEach(control => { control.disabled = value || control.dataset.locked === '1'; });
    updateBulk();
  }
  // 일괄신청 — 체크한 학생이 있고 신청 기간일 때만
  const checkedBoxes = () => [...document.querySelectorAll('#studentRows .hs-check:checked')];
  function updateBulk() {
    const boxes = [...document.querySelectorAll('#studentRows .hs-check:not([data-locked="1"])')];
    bulkButton.disabled = busy || !orderWindow.open || checkedBoxes().length === 0;
    checkAll.disabled = busy || boxes.length === 0;
    checkAll.checked = boxes.length > 0 && boxes.every(box => box.checked);
  }
  function resetForm() {
    form.reset();
    originalId = '';
    setBusy(busy);
    form.elements.passwd.type = 'password';
    document.getElementById('showPassword').checked = false;
    materialOptions();   // 수정 때 붙인 '기존 교재' 선택지 제거
    document.getElementById('formHeading').textContent = '학생 등록';
    saveButton.textContent = '학생 등록';
  }
  function materialOptions() {
    selectable = materials;   // 서버가 교사 과정(방문수업/급파한)의 교재만 보낸다 (다른 교재의 기존 학생은 '기존 교재'로 표시)
    const families = [...new Set(selectable.map(book => book.family))];
    form.elements.course.replaceChildren(new Option(course, course));   // 과정은 교사 과정 하나
    form.elements.material_code.replaceChildren(new Option('교재를 선택하세요', ''));
    for (const family of families) {
      const group = document.createElement('optgroup');
      group.label = label(family);
      for (const book of selectable.filter(book => book.family === family)) {
        const contents = book.includes?.length ? ` / 구성: ${book.includes.join(', ')}` : '';
        group.appendChild(new Option(label(`${book.title} (${book.code})${contents}`), book.code));
      }
      form.elements.material_code.appendChild(group);
    }
  }
  function renderStudents() {
    const query = search.value.trim().toLocaleLowerCase();
    const visible = students.filter(row => [row.name, row.id, row.classnm].some(value => String(value || '').toLocaleLowerCase().includes(query)));
    const body = document.getElementById('studentRows');
    body.replaceChildren();
    document.getElementById('studentCount').textContent = `총 학생 수: ${students.length}명 / 조회: ${visible.length}명`;
    for (const row of visible) {
      const tr = document.createElement('tr');
      tr.dataset.id = row.id;
      // 행 클릭 시 학생 등록 폼에 채움 — 행 안의 선택·날짜·버튼 조작은 제외
      tr.addEventListener('click', event => { if (!event.target.closest('button, select, input')) editStudent(row); });
      // 교재 신청: 신청 기간 + 이번 달 미신청 + 교재 선택된 학생만 (신청/주문 완료면 잠금)
      const orderLocked = !orderWindow.open || Boolean(row.order_status) || !materials.some(item => item.code === row.material_code);
      const check = document.createElement('input');
      check.type = 'checkbox'; check.className = 'hs-check'; check.value = row.id;
      check.dataset.locked = orderLocked ? '1' : ''; check.disabled = busy || orderLocked;
      check.setAttribute('aria-label', `${row.name} 일괄신청 선택`);
      check.addEventListener('change', updateBulk);
      const values = [check, label(row.course), row.name, row.id, row.passwd, row.birth_year, row.classnm, row.p_mobile,
        materialSelect(row), row.sdate, edateInput(row)];
      for (const value of values) {
        const td = document.createElement('td');
        if (value instanceof Node) td.appendChild(value); else td.textContent = value ?? '';
        tr.appendChild(td);
      }
      const actions = document.createElement('td');
      actions.appendChild(statusSelect(row));
      // 신청 완료(지사 처리 전) → 주문 완료(지사 처리 후)
      const orderLabel = { pending: '신청 완료', done: '주문 완료' }[row.order_status] || '교재 신청';
      const orderClass = { pending: 'btn-success', done: 'btn-secondary' }[row.order_status] || 'btn-outline-primary';
      // 수정은 행 클릭, 삭제 버튼은 없음
      const button = document.createElement('button');
      button.type = 'button'; button.textContent = orderLabel;
      button.dataset.locked = orderLocked ? '1' : ''; button.disabled = busy || orderLocked;
      button.className = `btn btn-sm ${orderClass} mr-1`;
      button.addEventListener('click', () => orderMaterial(row));
      actions.appendChild(button);
      tr.appendChild(actions); body.appendChild(tr);
    }
    if (!visible.length) {
      const tr = document.createElement('tr'), td = document.createElement('td');
      td.colSpan = 12; td.textContent = '조회된 학생이 없습니다.'; tr.appendChild(td); body.appendChild(tr);
    }
    const info = document.getElementById('orderWindowInfo');
    info.textContent = `교재 신청 기간: 매월 ${orderWindow.start}일~${orderWindow.end}일${orderWindow.open ? '' : ' (지금은 신청할 수 없습니다)'}`;
    info.classList.toggle('closed', !orderWindow.open);
    updateBulk();
  }
  // 체크한 학생을 차례로 신청 — 한 명씩 끝날 때마다 버튼이 '신청 완료'로 바뀐다
  async function bulkOrder() {
    const boxes = checkedBoxes();
    if (busy || !boxes.length || !confirm(`선택한 ${boxes.length}명의 이번 달 교재를 신청할까요?`)) return;
    setBusy(true); message();
    let done = 0;
    const failed = [];
    for (const box of boxes) {
      const row = students.find(item => item.id === box.value);
      try {
        await request('SHomeschoolOrder', { id: row.id });
        done++;
        const button = box.closest('tr').querySelector('td:last-child button');
        button.textContent = '신청 완료'; button.className = 'btn btn-sm btn-success mr-1';
        button.dataset.locked = box.dataset.locked = '1'; box.checked = false;
        message(`일괄신청 중... ${done}/${boxes.length}`, true);
      } catch (error) {
        failed.push(`${row.name}: ${error?.responseJSON?.error || '신청 실패'}`);
      }
    }
    try { await loadStudents(); } catch (_) { /* 목록 갱신 실패는 아래 결과 메시지로 충분 */ }
    message([`${done}명 신청 완료${failed.length ? `, ${failed.length}명 실패` : ''}`, ...failed].join('\n'), failed.length === 0);
    setBusy(false);
  }
  checkAll.addEventListener('change', () => {
    document.querySelectorAll('#studentRows .hs-check:not([data-locked="1"])').forEach(box => { box.checked = checkAll.checked; });
    updateBulk();
  });
  bulkButton.addEventListener('click', bulkOrder);
  // 수업중(1) / 수업종료(0) — 선택 즉시 저장. 수업종료 학생은 로그인할 수 없다.
  function statusSelect(row) {
    const select = document.createElement('select');
    select.className = 'mr-1'; select.disabled = busy;
    select.setAttribute('aria-label', `${row.name} 수업 상태`);
    select.add(new Option('수업중', '1')); select.add(new Option('수업종료', '0'));
    const current = String(row.status) === '0' ? '0' : '1';
    select.value = current;
    select.classList.toggle('hs-ended', current === '0');   // 수업종료는 노란 배경
    select.addEventListener('change', async () => {
      if (busy) { select.value = current; return; }
      if (select.value === '0' && !confirm(`${row.name} 학생을 수업종료로 바꿀까요? 오늘 날짜가 수업 종료일로 입력되며, 수업종료 학생은 로그인할 수 없습니다.`)) {
        select.value = current; return;
      }
      setBusy(true); message();
      try {
        await request('SSaveHomeschoolStatus', { id: row.id, status: select.value });
        await loadStudents(); message(`${row.name} 학생을 ${select.value === '1' ? '수업중' : '수업종료'}(으)로 변경했습니다.`, true);
      } catch (error) { select.value = current; message(error?.responseJSON?.error || '수업 상태 변경에 실패했습니다.'); }
      finally { setBusy(false); }
    });
    return select;
  }
  // 목록에서 단계(교재명)를 바로 변경 — 선택 즉시 저장
  function materialSelect(row) {
    const select = form.elements.material_code.cloneNode(true);
    select.removeAttribute('name'); select.required = false; select.disabled = busy;
    select.setAttribute('aria-label', `${row.name} 단계(교재명)`);
    if (row.material_code && !selectable.some(item => item.code === row.material_code)) {
      select.add(new Option(`기존 교재 (${row.material_code})`, row.material_code), 1);
    }
    select.value = row.material_code || '';
    select.addEventListener('change', async () => {
      if (busy || !select.value) { select.value = row.material_code || ''; return; }
      setBusy(true); message();
      try {
        await request('SSaveHomeschoolMaterial', { id: row.id, material_code: select.value });
        await loadStudents(); message(`${row.name} 학생의 단계(교재명)를 변경했습니다.`, true);
      } catch (error) { select.value = row.material_code || ''; message(error?.responseJSON?.error || '단계 변경에 실패했습니다.'); }
      finally { setBusy(false); }
    });
    return select;
  }
  // 목록에서 수업 종료일을 바로 변경 — 날짜 선택 즉시 저장 (비우면 NULL). 수업중 학생은 비우고 잠금
  function edateInput(row) {
    const input = document.createElement('input');
    const active = String(row.status) !== '0';
    input.type = 'date'; input.min = row.sdate || '1900-01-01';
    input.dataset.locked = active ? '1' : ''; input.disabled = busy || active;
    input.setAttribute('aria-label', `${row.name} 수업 종료일`);
    input.value = active ? '' : row.edate || '';
    input.addEventListener('change', async () => {
      if (busy || input.value === (row.edate || '')) { input.value = row.edate || ''; return; }
      setBusy(true); message();
      try {
        await request('SSaveHomeschoolEdate', { id: row.id, edate: input.value });
        await loadStudents(); message(`${row.name} 학생의 수업 종료일을 변경했습니다.`, true);
      } catch (error) { input.value = row.edate || ''; message(error?.responseJSON?.error || '수업 종료일 변경에 실패했습니다.'); }
      finally { setBusy(false); }
    });
    return input;
  }
  async function orderMaterial(row) {
    const book = materials.find(item => item.code === row.material_code);
    if (busy || !book || !confirm(`${row.name} 학생의 이번 달 교재 '${label(book.title)}'을(를) 주문할까요?`)) return;
    setBusy(true); message();
    try {
      await request('SHomeschoolOrder', { id: row.id });
      await loadStudents(); message(`${row.name} 학생의 교재를 주문했습니다. 지사에서 주문을 완료하면 '주문 완료'로 바뀝니다.`, true);
    } catch (error) { message(error?.responseJSON?.error || '교재 주문에 실패했습니다.'); }
    finally { setBusy(false); }
  }
  function editStudent(row) {
    if (busy) return;
    resetForm(); originalId = row.id;
    if (row.material_code && !selectable.some(item => item.code === row.material_code)) {
      form.elements.material_code.add(new Option(`기존 교재 (${row.material_code})`, row.material_code), 1);
    }
    for (const field of fields) form.elements[field].value = row[field] ?? '';
    setBusy(busy);   // 학생명·아이디·단계·수업 시작일 잠금
    document.getElementById('formHeading').textContent = `${row.name} 학생 수정`;
    saveButton.textContent = '변경 저장';
    message(row.material_code ? '' : '교재를 선택한 뒤 저장해 주세요.');
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  async function loadStudents() {
    const response = await request('SHomeschoolStudents');
    students = response.success || [];
    materials = response.materials || [];
    course = response.course || course;
    orderWindow = response.order_window || orderWindow;
    ready = materials.length > 0;
    materialOptions(); resetForm(); renderStudents();
    if (!ready) message(`선택할 수 있는 ${course} 교재가 없습니다.`);
    else if (response.auto_advanced > 0) {
      message(`${response.auto_advanced}명의 단계(교재명)를 지난달 신청 교재의 다음 단계로 자동 변경했습니다. 바꿀 학생만 수정한 뒤 신청해 주세요.`, true);
    }
  }
  document.getElementById('showPassword').addEventListener('change', event => {
    form.elements.passwd.type = event.target.checked ? 'text' : 'password';
  });
  form.addEventListener('submit', async event => {
    event.preventDefault(); if (busy || !ready) return;
    const data = Object.fromEntries(new FormData(form)); data.original_id = originalId;
    // disabled 항목은 FormData에서 빠지므로 화면 값으로 채움 (수업 종료일, 수정 시 잠긴 항목)
    for (const field of ['edate', 'course', ...lockedOnEdit]) data[field] = form.elements[field].value;
    setBusy(true); message();
    try {
      await request('SSaveHomeschoolStudent', data);
      await loadStudents(); message('학생 정보를 저장했습니다.', true);
    } catch (error) { message(error?.responseJSON?.error || '저장에 실패했습니다.'); }
    finally { setBusy(false); }
  });
  resetButton.addEventListener('click', () => { resetForm(); message(); });
  search.addEventListener('input', renderStudents);
  reloadButton.addEventListener('click', async () => {
    if (busy || (originalId && !confirm('입력 중인 내용을 취소하고 새로고침할까요?'))) return;
    setBusy(true);
    try { await loadStudents(); message(); }
    catch (error) { message(error?.responseJSON?.error || '조회에 실패했습니다.'); }
    finally { setBusy(false); }
  });
  setBusy(true);
  loadStudents().catch(error => message(error?.responseJSON?.error || '학생 정보를 불러오지 못했습니다.'))
    .finally(() => setBusy(false));
})();
