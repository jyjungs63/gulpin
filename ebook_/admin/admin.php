<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPLAT Ebook - 설정 관리</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container { max-width: 1400px; margin: 0 auto; }

        .header {
            background: white; padding: 30px; border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,.1); margin-bottom: 30px;
        }
        .header h1 { color: #333; margin-bottom: 10px; }
        .header p  { color: #666; }

        .tabs { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }

        .tab-button {
            background: white; border: none; padding: 15px 30px;
            border-radius: 8px; cursor: pointer; font-size: 16px;
            font-weight: 500; transition: all .3s;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .tab-button:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,.15); }
        .tab-button.active { background: linear-gradient(135deg,#667eea,#764ba2); color: white; }

        .tab-content {
            display: none; background: white; padding: 30px;
            border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,.1);
        }
        .tab-content.active { display: block; }

        .action-buttons { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }

        .btn {
            padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer;
            font-size: 14px; font-weight: 500; transition: all .3s;
        }
        .btn-primary   { background: #667eea; color: white; }
        .btn-primary:hover { background: #5568d3; }
        .btn-success   { background: #48bb78; color: white; }
        .btn-success:hover { background: #38a169; }
        .btn-warning   { background: #ed8936; color: white; }
        .btn-warning:hover { background: #dd6b20; }
        .btn-danger    { background: #f56565; color: white; }
        .btn-danger:hover  { background: #e53e3e; }
        .btn-secondary { background: #718096; color: white; }
        .btn-secondary:hover { background: #4a5568; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        thead { background: #f7fafc; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { font-weight: 600; color: #2d3748; font-size: 14px; text-transform: uppercase; }
        td { color: #4a5568; }
        tbody tr:hover { background: #f7fafc; }

        /* ── 카테고리 그룹 헤더 ── */
        .category-group-header td {
            background: #ebf4ff; font-weight: 700; color: #2b6cb0;
            font-size: 13px; padding: 10px 15px;
        }

        .badge {
            display: inline-block; padding: 4px 12px;
            border-radius: 12px; font-size: 12px; font-weight: 500;
        }
        .badge-active   { background: #c6f6d5; color: #22543d; }
        .badge-inactive { background: #fed7d7; color: #742a2a; }
        .badge-double   { background: #bee3f8; color: #2c5282; }
        .badge-single   { background: #feebc8; color: #7c2d12; }
        .badge-default  { background: #fbd38d; color: #7b341e; }
        .badge-category { background: #e9d8fd; color: #553c9a; }

        .modal {
            display: none; position: fixed; top: 0; left: 0;
            width: 100%; height: 100%; background: rgba(0,0,0,.5);
            z-index: 1000; align-items: center; justify-content: center;
        }
        .modal.active { display: flex; }

        .modal-content {
            background: white; padding: 30px; border-radius: 10px;
            max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;
        }
        .modal-header { margin-bottom: 20px; }
        .modal-header h2 { color: #2d3748; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #2d3748; font-weight: 500; }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%; padding: 10px 15px; border: 1px solid #cbd5e0;
            border-radius: 6px; font-size: 14px;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus { outline: none; border-color: #667eea; }

        .form-actions {
            display: flex; gap: 10px; justify-content: flex-end; margin-top: 30px;
        }

        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
        .alert-error   { background: #fed7d7; color: #742a2a; border: 1px solid #fc8181; }

        .loading { text-align: center; padding: 40px; color: #718096; }

        .action-cell { display: flex; gap: 8px; }
        .btn-icon { padding: 8px 12px; font-size: 12px; }

        .code-preview {
            background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 6px;
            padding: 20px; margin-top: 20px; font-family: 'Courier New', monospace;
            font-size: 13px; overflow-x: auto;
        }

        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        .checkbox-group input[type="checkbox"] { width: auto; }

        .radio-group { display: flex; gap: 20px; margin-top: 10px; }
        .radio-group label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .radio-group input[type="radio"] { width: auto; }

        .help-text { font-size: 12px; color: #718096; margin-top: 5px; }
    </style>
</head>
<body>
<div class="container">
    <!-- 헤더 -->
    <div class="header">
        <h1>📚 EPLAT Ebook 설정 관리</h1>
        <p>카테고리 · 책 타입 · 시스템 설정을 관리합니다</p>
    </div>

    <!-- 탭 버튼 -->
    <div class="tabs">
        <button class="tab-button active"  onclick="switchTab('book-types',this)">📖 책 타입 관리</button>
        <button class="tab-button"         onclick="switchTab('categories',this)">🗂️ 카테고리 관리</button>
        <button class="tab-button"         onclick="switchTab('system-config',this)">⚙️ 시스템 설정</button>
        <button class="tab-button"         onclick="switchTab('export',this)">📄 설정 내보내기</button>
    </div>

    <!-- 알림 -->
    <div id="alert-container"></div>

    <!-- ① 책 타입 관리 -->
    <div id="book-types" class="tab-content active">
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="openBookTypeModal()">➕ 새 책 타입 추가</button>
            <button class="btn btn-success" onclick="loadBookTypes()">🔄 새로고침</button>
        </div>
        <div id="book-types-table"><div class="loading">로딩 중...</div></div>
    </div>

    <!-- ② 카테고리 관리 -->
    <div id="categories" class="tab-content">
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="openCategoryModal()">➕ 새 카테고리 추가</button>
            <button class="btn btn-success" onclick="loadCategories()">🔄 새로고침</button>
        </div>
        <div id="categories-table"><div class="loading">로딩 중...</div></div>
    </div>

    <!-- ③ 시스템 설정 -->
    <div id="system-config" class="tab-content">
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="openSystemConfigModal()">➕ 새 설정 추가</button>
            <button class="btn btn-success" onclick="loadSystemConfig()">🔄 새로고침</button>
        </div>
        <div id="system-config-table"><div class="loading">로딩 중...</div></div>
    </div>

    <!-- ④ 설정 내보내기 -->
    <div id="export" class="tab-content">
        <h2>book-config.js 파일 생성</h2>
        <p>데이터베이스 설정을 book-config.js 파일로 내보냅니다.</p>
        <div class="action-buttons" style="margin-top:20px;">
            <button class="btn btn-primary" onclick="generateConfigFile()">📄 미리보기</button>
            <button class="btn btn-success" onclick="downloadConfigFile()">💾 다운로드</button>
        </div>
        <div id="config-preview" class="code-preview" style="display:none;">
            <pre id="config-code"></pre>
        </div>
    </div>
</div>

<!-- ═══ 모달: 책 타입 추가/수정 ═══ -->
<div id="book-type-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="book-type-modal-title">책 타입 추가</h2>
        </div>
        <form id="book-type-form">
            <input type="hidden" id="book-type-id">

            <div class="form-group">
                <label for="type-code">타입 코드 *</label>
                <input type="text" id="type-code" required placeholder="예: phonics">
            </div>

            <div class="form-group">
                <label for="type-name">타입 이름 *</label>
                <input type="text" id="type-name" required placeholder="예: 파닉스">
            </div>

            <div class="form-group">
                <label for="directory-path">디렉토리 경로 *</label>
                <input type="text" id="directory-path" required placeholder="예: bookshelf-1/2-1_phonics/">
                <div class="help-text">루트 경로(카테고리) 이후의 상대 경로</div>
            </div>

            <div class="form-group">
                <label for="display-mode">페이지 표시 모드 *</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="display-mode" id="display-mode-double" value="double" checked>
                        📖 Double (두 페이지)
                    </label>
                    <label>
                        <input type="radio" name="display-mode" id="display-mode-single" value="single">
                        📱 Single (한 페이지)
                    </label>
                </div>
            </div>

            <!-- 순서 컬럼 → 카테고리 선택으로 변경 -->
            <div class="form-group">
                <label for="book-type-category">카테고리 (루트 경로)</label>
                <select id="book-type-category">
                    <option value="">— 기본 카테고리 사용 —</option>
                </select>
                <div class="help-text">미선택 시 기본(default) 카테고리의 루트 경로가 적용됩니다.</div>
            </div>

            <div class="form-group">
                <label for="display-order">표시 순서</label>
                <input type="number" id="display-order" value="0">
            </div>

            <div class="form-group checkbox-group">
                <input type="checkbox" id="is-active" checked>
                <label for="is-active">활성화</label>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeBookTypeModal()">취소</button>
                <button type="submit" class="btn btn-primary">저장</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══ 모달: 카테고리 추가/수정 ═══ -->
<div id="category-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="category-modal-title">카테고리 추가</h2>
        </div>
        <form id="category-form">
            <input type="hidden" id="category-edit-code">

            <div class="form-group">
                <label for="cat-code">카테고리 코드 *</label>
                <input type="text" id="cat-code" required placeholder="예: premium">
                <div class="help-text">영문·숫자·언더스코어만 사용 권장. 추후 변경 불가.</div>
            </div>

            <div class="form-group">
                <label for="cat-name">카테고리 이름 *</label>
                <input type="text" id="cat-name" required placeholder="예: 프리미엄">
            </div>

            <div class="form-group">
                <label for="cat-root-path">루트 경로 *</label>
                <input type="text" id="cat-root-path" required placeholder="예: ../ebook_premium/">
                <div class="help-text">이 카테고리 소속 책들의 공통 루트 경로</div>
            </div>

            <div class="form-group">
                <label for="cat-description">설명</label>
                <input type="text" id="cat-description" placeholder="카테고리 설명">
            </div>

            <div class="form-group">
                <label for="cat-order">표시 순서</label>
                <input type="number" id="cat-order" value="0">
            </div>

            <div class="form-group checkbox-group">
                <input type="checkbox" id="cat-is-default">
                <label for="cat-is-default">기본 카테고리로 설정 (기존 기본이 해제됩니다)</label>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeCategoryModal()">취소</button>
                <button type="submit" class="btn btn-primary">저장</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══ 모달: 시스템 설정 추가/수정 ═══ -->
<div id="system-config-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="system-config-modal-title">시스템 설정 추가</h2>
        </div>
        <form id="system-config-form">
            <div class="form-group">
                <label for="config-key">설정 키 *</label>
                <input type="text" id="config-key" required placeholder="예: ROOT_PATH">
            </div>

            <div class="form-group">
                <label for="config-value">설정 값 *</label>
                <input type="text" id="config-value" required placeholder="예: ../ebook_new_eplat/">
            </div>

            <div class="form-group">
                <label for="config-category">카테고리</label>
                <select id="config-category">
                    <option value="general">기본 설정 (general)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="config-type">데이터 타입</label>
                <select id="config-type">
                    <option value="string">문자열</option>
                    <option value="number">숫자</option>
                    <option value="boolean">불린</option>
                    <option value="json">JSON</option>
                </select>
            </div>

            <div class="form-group">
                <label for="config-description">설명</label>
                <textarea id="config-description" rows="3" placeholder="설정 설명"></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeSystemConfigModal()">취소</button>
                <button type="submit" class="btn btn-primary">저장</button>
            </div>
        </form>
    </div>
</div>

<script src="admin.js?v=<?= filemtime('admin.js') ?>"></script>
</body>
</html>
