const fs = require('fs');
const path = require('path');
const assert = require('assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const root = path.resolve(__dirname, '..');
(async () => {
  const browser = await chromium.launch({ headless: true, channel: process.env.BROWSER_CHANNEL || 'msedge' });
  try {
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    const books = JSON.parse(fs.readFileSync(path.join(root, 'Server/price_catalog.json'))).groups.homeschool.items;
    let rows = [], saved, materialSaved, ordered, statusSaved, windowOpen = true;
    const orderedIds = [];
    const otherData = params => Object.fromEntries([...params].filter(([key]) => key.startsWith('otherData['))
      .map(([key, value]) => [key.slice(10, -1), value]));
    await page.route('https://test.local/**', async route => {
      const pathname = new URL(route.request().url()).pathname;
      if (pathname.endsWith('/Server/SMethods.php')) {
        const params = new URLSearchParams(route.request().postData());
        if (params.get('functionName') === 'SSaveHomeschoolStudent') {
          saved = Object.fromEntries([...params].filter(([key]) => key.startsWith('otherData['))
            .map(([key, value]) => [key.slice(10, -1), value]));
          rows = [saved];
          return route.fulfill({ json: { success: true } });
        }
        if (params.get('functionName') === 'SSaveHomeschoolMaterial') {
          materialSaved = otherData(params);
          rows = rows.map(row => ({ ...row, material_code: materialSaved.material_code }));
          return route.fulfill({ json: { success: true } });
        }
        if (params.get('functionName') === 'SSaveHomeschoolStatus') {
          statusSaved = otherData(params);
          rows = rows.map(row => ({ ...row, status: statusSaved.status }));
          return route.fulfill({ json: { success: true } });
        }
        if (params.get('functionName') === 'SHomeschoolOrder') {
          ordered = otherData(params);
          orderedIds.push(ordered.id);
          rows = rows.map(row => (row.id === ordered.id ? { ...row, order_status: 'pending' } : row));
          return route.fulfill({ json: { success: true } });
        }
        // 서버(SHomeschoolStudents)는 교사 과정(방문수업)의 교재만 보낸다
        return route.fulfill({ json: { success: rows, materials: books.filter(book => book.family === '방문수업'), course: '방문수업',
          order_window: { start: 10, end: 15, open: windowOpen } } });
      }
      if (pathname.endsWith('.js')) {
        const file = path.join(root, pathname);
        return route.fulfill({ contentType: 'application/javascript', body: fs.readFileSync(file, 'utf8') });
      }
      let html = fs.readFileSync(path.join(root, 'purchase/homeschoolstudents.php'), 'utf8');
      html = html.slice(html.indexOf('<!DOCTYPE')).replace(/<\?php[\s\S]*?\?>/g, '').replace(/<\?=[\s\S]*?\?>/g, '1');
      return route.fulfill({ contentType: 'text/html', body: html });
    });
    await page.addInitScript(() => localStorage.setItem('infochaitalk', JSON.stringify({ role: 30, owner: '온에듀홈스쿨지사', name: '이호선' })));
    await page.goto('https://test.local/purchase/homeschoolstudents.php');
    await page.locator('#saveStudent:not([disabled])').waitFor();
    assert.equal(await page.locator('#pageTitle').textContent(), '온에듀홈스쿨지사-이호선 : 방문수업 학생관리');
    // 화면 표시는 방문수업, 저장 값(과정·교재 코드)은 홈스쿨 그대로
    assert.equal(await page.locator('[name=course] option').first().textContent(), '방문수업');
    assert.equal(await page.locator('[name=material_code]').evaluate(select => select.textContent.includes('홈스쿨')), false);
    // 방문수업 과정 교사 — 급파한 교재는 선택지에 없음
    assert.equal(await page.locator('[name=material_code] option').count(), books.filter(book => book.family !== '급파한').length + 1);
    assert.equal(await page.locator('[name=material_code] optgroup[label="급파한"]').count(), 0);
    await page.locator('[name=name]').fill('학생');
    await page.locator('[name=id]').fill('student1');
    await page.locator('[name=passwd]').fill('Pass123');
    await page.locator('[name=birth_year]').fill('2018');
    await page.locator('[name=classnm]').fill('학교');
    await page.locator('[name=p_mobile]').fill('010-1234-5678');
    await page.locator('[name=material_code]').selectOption('SO1-A');
    assert.equal(await page.locator('[name=course]').inputValue(), '방문수업');
    await page.locator('[name=sdate]').fill('2026-10-01');
    assert.equal(await page.locator('[name=edate]').isDisabled(), true);   // 수업 종료일은 목록·수업종료로만 변경
    await page.locator('#saveStudent').click();
    await page.getByText('학생 정보를 저장했습니다.').waitFor();
    assert.equal(saved.material_code, 'SO1-A');
    assert.equal(saved.p_mobile, '010-1234-5678');
    // 수정은 행 클릭 — 학생명·아이디·단계·수업 시작일 잠금, 나머지 수정 가능
    await page.locator('#studentRows td').nth(1).click();
    for (const field of ['name', 'id', 'material_code', 'sdate']) {
      assert.equal(await page.locator(`[name=${field}]`).isDisabled(), true, field);
    }
    for (const field of ['passwd', 'birth_year', 'classnm', 'p_mobile']) {
      assert.equal(await page.locator(`[name=${field}]`).isDisabled(), false, field);
    }
    assert.equal(await page.locator('[name=passwd]').getAttribute('type'), 'password');
    await page.locator('#showPassword').check();
    assert.equal(await page.locator('[name=passwd]').getAttribute('type'), 'text');
    await page.locator('[name=classnm]').fill('새학교');
    assert.equal(await page.locator('[name=course]').inputValue(), '방문수업');   // 과정은 홈스쿨 고정
    await page.locator('#saveStudent').click();
    await page.getByText('학생 정보를 저장했습니다.').waitFor();
    assert.equal(saved.original_id, 'student1');
    assert.equal(saved.classnm, '새학교');
    assert.deepEqual([saved.name, saved.id, saved.material_code, saved.sdate], ['학생', 'student1', 'SO1-A', '2026-10-01']);
    assert.equal(await page.locator('[name=name]').isDisabled(), false);   // 저장 후 등록 폼으로 돌아오면 잠금 해제
    // 목록 행: 단계 select 즉시 저장, 버튼은 교재 신청만 (수정·삭제 없음)
    const rowSelect = page.getByLabel('학생 단계(교재명)');
    assert.equal(await rowSelect.inputValue(), 'SO1-A');
    await rowSelect.selectOption('SO1-B');
    await page.getByText('단계(교재명)를 변경했습니다.').waitFor();
    assert.deepEqual(materialSaved, { id: 'student1', material_code: 'SO1-B' });
    assert.equal(await page.getByLabel('학생 단계(교재명)').inputValue(), 'SO1-B');
    assert.equal(await page.locator('#studentRows td').nth(4).textContent(), 'Pass123');   // 비번 표시 (첫 칸은 일괄신청 체크)
    assert.deepEqual(await page.locator('#studentRows button').allTextContents(), ['교재 신청']);
    page.once('dialog', dialog => dialog.accept());
    await page.getByRole('button', { name: '교재 신청' }).click();
    await page.getByText('교재를 주문했습니다.').waitFor();
    assert.deepEqual(ordered, { id: 'student1' });
    const pendingButton = page.getByRole('button', { name: '신청 완료' });
    assert.equal(await pendingButton.isDisabled(), true);
    await page.locator('#reloadStudents').click();
    await page.locator('#reloadStudents:not([disabled])').waitFor();
    assert.equal(await page.getByRole('button', { name: '신청 완료' }).isDisabled(), true);   // setBusy(false) 뒤에도 잠금 유지
    assert.equal(await page.getByLabel('학생 일괄신청 선택').isDisabled(), true);   // 신청한 학생은 체크 불가
    // 일괄신청: 체크한 학생만 차례로 신청 → 신청 완료
    rows.push({ ...rows[0], id: 'student2', name: '학생2', order_status: '' }, { ...rows[0], id: 'student3', name: '학생3', order_status: '' });
    await page.locator('#reloadStudents').click();
    await page.locator('#reloadStudents:not([disabled])').waitFor();
    assert.equal(await page.locator('#bulkOrder').isDisabled(), true);   // 체크 전
    await page.locator('#checkAll').check();
    assert.equal(await page.locator('#studentRows .hs-check:checked').count(), 2);   // 이미 신청한 학생 제외
    await page.getByLabel('학생3 일괄신청 선택').uncheck();
    page.once('dialog', dialog => dialog.accept());
    await page.locator('#bulkOrder').click();
    await page.getByText('1명 신청 완료').waitFor();
    assert.deepEqual(orderedIds, ['student1', 'student2']);
    assert.deepEqual(await page.locator('#studentRows td:last-child button').allTextContents(), ['신청 완료', '신청 완료', '교재 신청']);
    // 신청 기간 밖: 신청·일괄신청 잠금
    windowOpen = false;
    await page.locator('#reloadStudents').click();
    await page.locator('#reloadStudents:not([disabled])').waitFor();
    assert.match(await page.locator('#orderWindowInfo').textContent(), /매월 10일~15일 \(지금은 신청할 수 없습니다\)/);
    assert.equal(await page.getByRole('button', { name: '교재 신청' }).isDisabled(), true);
    assert.equal(await page.getByLabel('학생3 일괄신청 선택').isDisabled(), true);
    assert.equal(await page.locator('#bulkOrder').isDisabled(), true);
    windowOpen = true;
    rows = rows.filter(row => row.id === 'student1');
    await page.locator('#reloadStudents').click();
    await page.locator('#reloadStudents:not([disabled])').waitFor();
    // 관리: 수업중/수업종료 선택 즉시 저장
    const statusSelect = page.getByLabel('학생 수업 상태');
    assert.equal(await statusSelect.inputValue(), '1');
    page.once('dialog', dialog => dialog.accept());
    await statusSelect.selectOption('0');
    await page.getByText('수업종료(으)로 변경했습니다.').waitFor();
    assert.deepEqual(statusSaved, { id: 'student1', status: '0' });
    assert.equal(await page.getByLabel('학생 수업 상태').inputValue(), '0');
    assert.equal(await page.getByLabel('학생 수업 상태').evaluate(el => getComputedStyle(el).backgroundColor), 'rgb(255, 243, 163)');   // 수업종료 노란 배경
    await page.setViewportSize({ width: 390, height: 844 });
    assert.equal(await page.locator('#studentForm').evaluate(form => getComputedStyle(form).gridTemplateColumns.split(' ').length), 1);
    assert.equal(await page.locator('#idClass, #idClassname').count(), 0);
    assert.deepEqual(errors, []);
    console.log('PASS homeschool form, catalog, create/edit, password visibility and mobile checks');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exit(1); });
