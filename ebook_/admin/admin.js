/**
 * ========================================
 * EPLAT Ebook - 관리 페이지 JavaScript (카테고리 관리 포함)
 * ========================================
 */

const API_BASE = '';

// ========================================
// 초기화
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    loadBookTypes();
    setupEventListeners();
});

function setupEventListeners() {
    document.getElementById('book-type-form').addEventListener('submit', async (e) => {
        e.preventDefault(); await saveBookType();
    });
    document.getElementById('category-form').addEventListener('submit', async (e) => {
        e.preventDefault(); await saveCategory();
    });
    document.getElementById('system-config-form').addEventListener('submit', async (e) => {
        e.preventDefault(); await saveSystemConfig();
    });
}

// ========================================
// 탭 전환
// ========================================
function switchTab(tabName, btnEl) {
    document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');
    document.getElementById(tabName).classList.add('active');

    if (tabName === 'book-types')   loadBookTypes();
    else if (tabName === 'categories')   loadCategories();
    else if (tabName === 'system-config') loadSystemConfig();
}

// ========================================
// 알림
// ========================================
function showAlert(message, type = 'success') {
    const container = document.getElementById('alert-container');
    const el = document.createElement('div');
    el.className = `alert alert-${type}`;
    el.textContent = message;
    container.innerHTML = '';
    container.appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

// ========================================
// ─── 카테고리 관리 ───────────────────────
// ========================================

async function loadCategories() {
    try {
        const res = await fetch(`${API_BASE}api_categories.php`);
        const result = await res.json();
        if (!result.success) throw new Error(result.error || '로드 실패');
        renderCategoriesTable(result.data);
    } catch (err) {
        showAlert('카테고리 로드 실패: ' + err.message, 'error');
        document.getElementById('categories-table').innerHTML =
            `<div class="alert alert-error">${err.message}</div>`;
    }
}

function renderCategoriesTable(data) {
    const rows = data.map(item => `
        <tr>
            <td><code>${item.category_code}</code></td>
            <td>
                ${item.category_name}
                ${item.is_default
                    ? '<span class="badge badge-default" style="margin-left:8px;">⭐ 기본</span>'
                    : ''}
            </td>
            <td><code>${item.root_path || '-'}</code></td>
            <td>${item.description || '-'}</td>
            <td>${item.display_order}</td>
            <td class="action-cell">
                <button class="btn btn-primary btn-icon" onclick="editCategory('${item.category_code}')">✏️ 수정</button>
                ${!item.is_default
                    ? `<button class="btn btn-warning btn-icon" onclick="setDefaultCategory('${item.category_code}')">⭐ 기본 설정</button>
                       <button class="btn btn-danger btn-icon" onclick="deleteCategory('${item.category_code}')">🗑️ 삭제</button>`
                    : ''}
            </td>
        </tr>
    `).join('');

    document.getElementById('categories-table').innerHTML = `
        <table>
            <thead>
                <tr>
                    <th>코드</th><th>이름</th><th>루트 경로</th>
                    <th>설명</th><th>순서</th><th>작업</th>
                </tr>
            </thead>
            <tbody>${rows}</tbody>
        </table>
    `;
}

function openCategoryModal(code = null) {
    const modal = document.getElementById('category-modal');
    document.getElementById('category-form').reset();
    document.getElementById('category-edit-code').value = '';

    if (code) {
        document.getElementById('category-modal-title').textContent = '카테고리 수정';
        document.getElementById('cat-code').readOnly = true;
        loadCategoryData(code);
    } else {
        document.getElementById('category-modal-title').textContent = '카테고리 추가';
        document.getElementById('cat-code').readOnly = false;
    }
    modal.classList.add('active');
}

function closeCategoryModal() {
    document.getElementById('category-modal').classList.remove('active');
}

async function loadCategoryData(code) {
    try {
        const res = await fetch(`${API_BASE}api_categories.php?code=${encodeURIComponent(code)}`);
        const result = await res.json();
        if (!result.success) throw new Error(result.error);
        const d = result.data;
        document.getElementById('category-edit-code').value = d.category_code;
        document.getElementById('cat-code').value       = d.category_code;
        document.getElementById('cat-name').value       = d.category_name;
        document.getElementById('cat-root-path').value  = d.root_path || '';
        document.getElementById('cat-description').value= d.description || '';
        document.getElementById('cat-order').value      = d.display_order;
        document.getElementById('cat-is-default').checked = d.is_default == 1;
    } catch (err) {
        showAlert('카테고리 데이터 로드 실패: ' + err.message, 'error');
    }
}

function editCategory(code) { openCategoryModal(code); }

async function saveCategory() {
    const editCode = document.getElementById('category-edit-code').value;
    const data = {
        category_code : document.getElementById('cat-code').value,
        category_name : document.getElementById('cat-name').value,
        root_path     : document.getElementById('cat-root-path').value,
        description   : document.getElementById('cat-description').value,
        display_order : parseInt(document.getElementById('cat-order').value) || 0,
        is_default    : document.getElementById('cat-is-default').checked ? 1 : 0
    };

    try {
        const method = editCode ? 'PUT' : 'POST';
        const res = await fetch(`${API_BASE}api_categories.php`, {
            method, headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (!result.success) throw new Error(result.error);
        showAlert(result.message);
        closeCategoryModal();
        loadCategories();
        // 책 타입 모달 카테고리 드롭다운 재로드
        _populateCategoryDropdowns();
    } catch (err) {
        showAlert('저장 실패: ' + err.message, 'error');
    }
}

async function setDefaultCategory(code) {
    if (!confirm(`'${code}' 카테고리를 기본 카테고리로 설정하시겠습니까?`)) return;
    try {
        const res = await fetch(`${API_BASE}api_categories.php`, {
            method: 'PUT', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ category_code: code, set_default: true })
        });
        const result = await res.json();
        if (!result.success) throw new Error(result.error);
        showAlert(result.message);
        loadCategories();
        _populateCategoryDropdowns();
    } catch (err) {
        showAlert('기본 설정 실패: ' + err.message, 'error');
    }
}

async function deleteCategory(code) {
    if (!confirm(`카테고리 '${code}'를 삭제하시겠습니까?`)) return;
    try {
        const res = await fetch(`${API_BASE}api_categories.php?code=${encodeURIComponent(code)}`,
            { method: 'DELETE' });
        const result = await res.json();
        if (!result.success) throw new Error(result.error);
        showAlert(result.message);
        loadCategories();
    } catch (err) {
        showAlert('삭제 실패: ' + err.message, 'error');
    }
}

// 카테고리 드롭다운 공통 채우기
async function _populateCategoryDropdowns() {
    try {
        const res = await fetch(`${API_BASE}api_categories.php`);
        const result = await res.json();
        if (!result.success) return;
        const cats = result.data;

        // 책 타입 모달
        const sel1 = document.getElementById('book-type-category');
        const saved1 = sel1.value;
        sel1.innerHTML = '<option value="">— 기본 카테고리 사용 —</option>';
        cats.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.category_code;
            opt.textContent = `${c.category_name} (${c.category_code}) — ${c.root_path}`;
            if (c.is_default) opt.textContent += ' ⭐';
            sel1.appendChild(opt);
        });
        if (saved1) sel1.value = saved1;

        // 시스템 설정 모달
        const sel2 = document.getElementById('config-category');
        const saved2 = sel2.value;
        sel2.innerHTML = '';
        cats.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.category_code;
            opt.textContent = `${c.category_name} (${c.category_code})${c.is_default ? ' ⭐' : ''}`;
            sel2.appendChild(opt);
        });
        if (saved2) sel2.value = saved2;
    } catch (_) {}
}

// ========================================
// ─── 책 타입 관리 ────────────────────────
// ========================================

async function loadBookTypes() {
    try {
        const res  = await fetch(`${API_BASE}api_book_types.php`);
        const text = await res.text();
        const result = JSON.parse(text);
        if (!result.success) throw new Error(result.error || '로드 실패');
        renderBookTypesTable(result.data);
    } catch (err) {
        showAlert('책 타입 로드 실패: ' + err.message, 'error');
        document.getElementById('book-types-table').innerHTML =
            `<div class="alert alert-error">${err.message}</div>`;
    }
}

function renderBookTypesTable(data) {
    const rows = data.map(item => `
        <tr>
            <td>${item.id}</td>
            <td><code>${item.type_code}</code></td>
            <td>${item.type_name}</td>
            <td><code>${item.directory_path}</code></td>
            <td>
                <span class="badge badge-${item.display_mode || 'double'}">
                    ${item.display_mode === 'single' ? '📱 Single' : '📖 Double'}
                </span>
            </td>
            <td>
                <span class="badge badge-category">
                    ${item.effective_category_name || item.category_name || '-'}
                </span>
                ${!item.category_code
                    ? '<span class="badge badge-default" style="margin-left:4px;font-size:10px;">기본</span>'
                    : ''}
            </td>
            <td>
                <span class="badge ${item.is_active ? 'badge-active' : 'badge-inactive'}">
                    ${item.is_active ? '활성' : '비활성'}
                </span>
            </td>
            <td class="action-cell">
                <button class="btn btn-primary btn-icon" onclick="editBookType(${item.id})">✏️ 수정</button>
                <button class="btn btn-danger  btn-icon" onclick="deleteBookType(${item.id})">🗑️ 삭제</button>
            </td>
        </tr>
    `).join('');

    document.getElementById('book-types-table').innerHTML = `
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>타입 코드</th><th>이름</th><th>디렉토리 경로</th>
                    <th>페이지 모드</th><th>카테고리</th><th>상태</th><th>작업</th>
                </tr>
            </thead>
            <tbody>${rows}</tbody>
        </table>
    `;
}

async function openBookTypeModal(id = null) {
    await _populateCategoryDropdowns();

    const modal = document.getElementById('book-type-modal');
    document.getElementById('book-type-form').reset();
    document.getElementById('book-type-id').value = '';
    document.getElementById('display-mode-double').checked = true;

    if (id) {
        document.getElementById('book-type-modal-title').textContent = '책 타입 수정';
        await loadBookTypeData(id);
    } else {
        document.getElementById('book-type-modal-title').textContent = '책 타입 추가';
        document.getElementById('is-active').checked = true;
    }
    modal.classList.add('active');
}

function closeBookTypeModal() {
    document.getElementById('book-type-modal').classList.remove('active');
}

async function loadBookTypeData(id) {
    try {
        const res = await fetch(`${API_BASE}api_book_types.php?id=${id}`);
        const result = await res.json();
        if (!result.success) throw new Error(result.error);
        const d = result.data;
        document.getElementById('book-type-id').value    = d.id;
        document.getElementById('type-code').value       = d.type_code;
        document.getElementById('type-name').value       = d.type_name;
        document.getElementById('directory-path').value  = d.directory_path;
        document.getElementById('display-order').value   = d.display_order;
        document.getElementById('is-active').checked     = d.is_active == 1;
        document.getElementById('book-type-category').value = d.category_code || '';

        const displayMode = d.display_mode || 'double';
        document.getElementById(
            displayMode === 'single' ? 'display-mode-single' : 'display-mode-double'
        ).checked = true;
    } catch (err) {
        showAlert('데이터 로드 실패: ' + err.message, 'error');
    }
}

function editBookType(id) { openBookTypeModal(id); }

async function saveBookType() {
    const id = document.getElementById('book-type-id').value;
    const catVal = document.getElementById('book-type-category').value;

    const data = {
        type_code     : document.getElementById('type-code').value,
        type_name     : document.getElementById('type-name').value,
        directory_path: document.getElementById('directory-path').value,
        display_mode  : document.querySelector('input[name="display-mode"]:checked').value,
        category_code : catVal || null,
        display_order : parseInt(document.getElementById('display-order').value) || 0,
        is_active     : document.getElementById('is-active').checked ? 1 : 0
    };

    try {
        let res;
        if (id) {
            data.id = id;
            res = await fetch(`${API_BASE}api_book_types.php`, {
                method: 'PUT', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        } else {
            res = await fetch(`${API_BASE}api_book_types.php`, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        }
        const result = await res.json();
        if (!result.success) throw new Error(result.error);
        showAlert(result.message);
        closeBookTypeModal();
        loadBookTypes();
    } catch (err) {
        showAlert('저장 실패: ' + err.message, 'error');
    }
}

async function deleteBookType(id) {
    if (!confirm('정말 삭제하시겠습니까?')) return;
    try {
        const res = await fetch(`${API_BASE}api_book_types.php?id=${id}`, { method: 'DELETE' });
        const result = await res.json();
        if (!result.success) throw new Error(result.error);
        showAlert(result.message);
        loadBookTypes();
    } catch (err) {
        showAlert('삭제 실패: ' + err.message, 'error');
    }
}

// ========================================
// ─── 시스템 설정 관리 ────────────────────
// ========================================

async function loadSystemConfig() {
    try {
        const res = await fetch(`${API_BASE}api_system_config.php`);
        const result = await res.json();
        if (!result.success) throw new Error(result.error || '로드 실패');
        renderSystemConfigTable(result.data);
    } catch (err) {
        showAlert('시스템 설정 로드 실패: ' + err.message, 'error');
        document.getElementById('system-config-table').innerHTML =
            `<div class="alert alert-error">${err.message}</div>`;
    }
}

function renderSystemConfigTable(data) {
    // 카테고리별 그룹핑
    const groups = {};
    data.forEach(item => {
        const key = item.category_code || 'general';
        if (!groups[key]) groups[key] = { name: item.category_name || key, items: [] };
        groups[key].items.push(item);
    });

    let tableBody = '';
    Object.entries(groups).forEach(([catCode, group]) => {
        tableBody += `
            <tr class="category-group-header">
                <td colspan="6">🗂️ ${group.name} <code style="font-size:11px;margin-left:6px;">(${catCode})</code></td>
            </tr>
        `;
        group.items.forEach(item => {
            tableBody += `
                <tr>
                    <td><code>${item.config_key}</code></td>
                    <td><code>${item.config_value}</code></td>
                    <td>${item.config_type}</td>
                    <td>${item.description || '-'}</td>
                    <td>
                        <span class="badge ${item.is_editable ? 'badge-active' : 'badge-inactive'}">
                            ${item.is_editable ? '가능' : '불가능'}
                        </span>
                    </td>
                    <td class="action-cell">
                        ${item.is_editable
                            ? `<button class="btn btn-primary btn-icon" onclick="editSystemConfig('${item.config_key}')">✏️ 수정</button>
                               <button class="btn btn-danger btn-icon" onclick="deleteSystemConfig('${item.config_key}')">🗑️ 삭제</button>`
                            : '<span style="color:#999;">-</span>'}
                    </td>
                </tr>
            `;
        });
    });

    document.getElementById('system-config-table').innerHTML = `
        <table>
            <thead>
                <tr>
                    <th>설정 키</th><th>설정 값</th><th>타입</th>
                    <th>설명</th><th>수정 가능</th><th>작업</th>
                </tr>
            </thead>
            <tbody>${tableBody}</tbody>
        </table>
    `;
}

async function openSystemConfigModal(key = null) {
    await _populateCategoryDropdowns();

    const modal = document.getElementById('system-config-modal');
    document.getElementById('system-config-form').reset();

    if (key) {
        document.getElementById('system-config-modal-title').textContent = '시스템 설정 수정';
        document.getElementById('config-key').readOnly = true;
        loadSystemConfigData(key);
    } else {
        document.getElementById('system-config-modal-title').textContent = '시스템 설정 추가';
        document.getElementById('config-key').readOnly = false;
    }
    modal.classList.add('active');
}

function closeSystemConfigModal() {
    document.getElementById('system-config-modal').classList.remove('active');
}

async function loadSystemConfigData(key) {
    try {
        const res = await fetch(`${API_BASE}api_system_config.php?key=${encodeURIComponent(key)}`);
        const result = await res.json();
        if (!result.success) throw new Error(result.error);
        const d = result.data;
        document.getElementById('config-key').value         = d.config_key;
        document.getElementById('config-value').value       = d.config_value;
        document.getElementById('config-type').value        = d.config_type;
        document.getElementById('config-description').value = d.description || '';
        document.getElementById('config-category').value    = d.category_code || 'general';
    } catch (err) {
        showAlert('데이터 로드 실패: ' + err.message, 'error');
    }
}

function editSystemConfig(key) { openSystemConfigModal(key); }

async function saveSystemConfig() {
    const data = {
        config_key   : document.getElementById('config-key').value,
        config_value : document.getElementById('config-value').value,
        config_type  : document.getElementById('config-type').value,
        description  : document.getElementById('config-description').value,
        category_code: document.getElementById('config-category').value || 'general'
    };

    try {
        const res = await fetch(`${API_BASE}api_system_config.php`, {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (!result.success) throw new Error(result.error);
        showAlert(result.message);
        closeSystemConfigModal();
        loadSystemConfig();
    } catch (err) {
        showAlert('저장 실패: ' + err.message, 'error');
    }
}

async function deleteSystemConfig(key) {
    if (!confirm(`'${key}' 설정을 삭제하시겠습니까?`)) return;
    try {
        const res = await fetch(`${API_BASE}api_system_config.php?key=${encodeURIComponent(key)}`,
            { method: 'DELETE' });
        const result = await res.json();
        if (!result.success) throw new Error(result.error);
        showAlert(result.message);
        loadSystemConfig();
    } catch (err) {
        showAlert('삭제 실패: ' + err.message, 'error');
    }
}

// ========================================
// ─── 설정 내보내기 ───────────────────────
// ========================================

async function generateConfigFile() {
    try {
        const res = await fetch(`${API_BASE}generate_book_config.php`);
        const code = await res.text();
        document.getElementById('config-code').textContent = code;
        document.getElementById('config-preview').style.display = 'block';
        showAlert('설정 파일이 생성되었습니다.');
    } catch (err) {
        showAlert('파일 생성 실패: ' + err.message, 'error');
    }
}

async function downloadConfigFile() {
    try {
        const res = await fetch(`${API_BASE}generate_book_config.php`);
        const code = await res.text();
        const blob = new Blob([code], { type: 'application/javascript' });
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href = url; a.download = 'book-config.js';
        document.body.appendChild(a); a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        showAlert('파일이 다운로드되었습니다.');
    } catch (err) {
        showAlert('다운로드 실패: ' + err.message, 'error');
    }
}
