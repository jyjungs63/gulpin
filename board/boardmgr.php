<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <!-- <link rel="stylesheet" href="../common.css"> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script> -->
    <script src="../js/common.js"></script>

<!-- <script src="../header.js"></script> -->
    <!-- <script src="../header.js"></script> -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Jua&family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
    <title>Chaitlak Kids 자료실</title>
    <style>
        .gp-visitor-strip{background:#1f5233; color:#eafff0; font-size:.78rem; padding:6px 20px; display:flex; justify-content:flex-end; align-items:center; gap:10px;}
        .gp-visitor-strip .pill{background:rgba(255,255,255,.15); border-radius:999px; padding:3px 12px; font-weight:700;}
        .gp-site-header{background:linear-gradient(180deg,#3da35d,#2e7d46); padding:14px 28px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:1050; box-shadow:0 4px 18px rgba(0,0,0,.08);}
        .gp-strip-logo{height:24px; display:block; margin-right:auto;}
        .gp-home-link{display:flex; align-items:center; gap:6px; color:#fff; font-weight:700; font-size:.95rem; text-decoration:none;}
        .gp-home-link:hover{opacity:.85; color:#fff;}
        .gp-main-nav{display:flex; gap:28px; align-items:center;}
        .gp-main-nav a{color:rgba(255,255,255,.75); font-weight:700; font-size:.95rem; transition:color .2s ease; text-decoration:none; cursor:pointer;}
        .gp-main-nav a:hover,.gp-main-nav a.on{color:#fff;}
        .gp-burger{display:none; color:#fff; font-size:1.5rem; background:none; border:none; cursor:pointer;}
        @media(max-width:840px){
            .gp-main-nav{position:fixed; top:0; right:-100%; height:100%; width:70%; max-width:300px; background:#2e7d46; flex-direction:column; padding:90px 30px; gap:26px; transition:right .3s ease; z-index:1060;}
            .gp-main-nav.open{right:0;}
            .gp-burger{display:block;}
            .gp-site-header{padding:12px 16px;}
        }
        * {
            box-sizing: border-box;
        }

        :root {
            --page-bg: #f6f7fb;
            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --line: #e4e8ef;
            --text: #1f2937;
            --muted: #64748b;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --danger: #ef4444;
            --success: #10b981;
            --shadow: 0 18px 45px rgba(15, 23, 42, .08);
        }

        body.board-page {
            min-height: 100vh;
            background: var(--page-bg);
            color: var(--text);
            font-family: "Inter", "Noto Sans KR", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .board-shell {
            width: min(1180px, calc(100% - 32px));
            min-height: 100vh;
            margin: 0 auto;
            padding: 28px 0 48px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .card-header {
            min-height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--surface);
            border-bottom: 1px solid var(--line);
            padding: 14px 18px;
        }

        .card-header h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
        }

        .card-nav {
            display: flex;
            align-items: center;
            gap: 22px;
            margin: 0;
            padding: 0;
            list-style: none;
            font-size: 1.08rem;
            font-weight: 800;
        }

        .card-nav li + li {
            position: relative;
        }

        .card-nav li + li::before {
            content: "/";
            position: absolute;
            left: -15px;
            color: #111827;
        }

        .card-nav a {
            color: #111827;
            text-decoration: none;
        }

        .card-nav a:hover {
            color: var(--primary);
        }

        .card-body {
            background: var(--surface);
            padding: 18px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 8px;
        }

        #toolbar {
            width: 100%;
            min-height: 44px;
            height: auto;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }

        #button {
            min-width: 96px;
        }

        #button:disabled {
            cursor: not-allowed;
            opacity: .55;
        }

        #toolbar #idSelect {
            flex: 0 1 300px;
            width: 300px;
        }

        #toolbar #idSearch {
            flex: 1 1 260px;
            max-width: 360px;
        }

        .toolbar-label {
            margin: 0;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface-soft);
            color: var(--muted);
            font-weight: 700;
            height: 40px;
        }

        input[type=text],
        select,
        textarea,
        .custom-select,
        .form-control {
            width: 100%;
            min-height: 42px;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background-color: #fff;
            color: var(--text);
            box-shadow: none;
        }

        input[type=text]:focus,
        select:focus,
        textarea:focus,
        .custom-select:focus,
        .form-control:focus,
        .editable-area:focus {
            border-color: rgba(37, 99, 235, .55);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
            outline: none;
        }

        label {
            display: inline-block;
            margin: 0;
            padding: 0;
            color: var(--muted);
            font-size: .92rem;
            font-weight: 700;
        }

        .form-panel {
            max-width: 100%;
            margin-top: 0;
            padding: 20px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface-soft);
        }

        .form-panel .row {
            display: grid;
            grid-template-columns: 130px minmax(0, 1fr);
            align-items: start;
            gap: 12px 18px;
            margin: 0 0 16px;
        }

        .form-panel .row:last-child {
            margin-bottom: 0;
        }

        .form-panel .action-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .form-panel .submit-row {
            grid-template-columns: 1fr;
        }

        .form-panel .box {
            display: contents;
        }

        .col-25,
        .col-75 {
            width: auto;
            max-width: none;
            float: none;
            padding: 0;
            margin: 0;
        }

        .editable-area {
            width: 100%;
            min-height: 150px;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background-color: #fff;
            margin-bottom: 12px;
            line-height: 1.55;
            overflow-wrap: anywhere;
        }

        .btn {
            border-radius: 8px;
            font-weight: 700;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-danger {
            background-color: var(--danger);
            border-color: var(--danger);
        }

        .btn-info {
            background-color: #0ea5e9;
            border-color: #0ea5e9;
        }

        button[type=submit] {
            min-height: 44px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            justify-self: end;
        }

        .file-pick {
            position: relative;
            display: inline-flex;
            align-items: center;
            min-height: 42px;
            overflow: hidden;
        }

        .file-pick input[type=file] {
            position: absolute;
            inset: 0;
            z-index: 2;
            opacity: 0;
            cursor: pointer;
        }

        #upload-file-info {
            margin-left: 8px;
            padding: 7px 10px;
            border-radius: 999px;
        }

        #dvPreview {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            color: var(--muted);
            overflow-wrap: anywhere;
        }

        #msg {
            color: var(--danger);
            font-weight: 700;
        }

        .inset-border {
            border: 1px solid var(--line);
            background: #fff;
        }

        .progress {
            height: 24px;
            padding: 0;
            background-color: #eaf0f7;
            border-radius: 999px;
            box-shadow: none;
            margin-bottom: 0;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background-color: var(--success);
            color: white;
            text-align: center;
            line-height: 24px;
            transition: width .6s ease;
        }

        #idFiles {
            min-height: 48px;
            padding: 12px;
            border-radius: 8px;
            overflow-wrap: anywhere;
        }

        #idFiles a {
            display: inline-block;
            margin: 0 0 8px;
            color: var(--primary);
            font-weight: 700;
        }

        .attachment-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .attachment-meta span {
            padding: 6px 10px;
            border-radius: 999px;
            background: var(--surface-soft);
            color: var(--muted);
            font-size: .88rem;
            font-weight: 700;
        }

        .attachment-desc {
            min-height: 70px;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface-soft);
            line-height: 1.55;
            overflow-wrap: anywhere;
        }

        .attachment-list {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .attachment-link {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 48px;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            color: var(--text);
            font-weight: 700;
            text-decoration: none;
            overflow-wrap: anywhere;
        }

        .attachment-link:hover {
            border-color: rgba(37, 99, 235, .45);
            color: var(--primary);
            text-decoration: none;
        }

        .attachment-link i,
        .attachment-link svg {
            flex: 0 0 auto;
            color: var(--primary);
        }

        .attachment-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 32px;
            padding: 6px 10px;
            border: 1px solid rgba(37, 99, 235, .25);
            border-radius: 999px;
            background: rgba(37, 99, 235, .08);
            color: var(--primary);
            font-weight: 800;
            pointer-events: none;
        }

        .table td,
        .table th {
            padding: 10px 8px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table td:nth-child(2),
        .table th:nth-child(2) {
            white-space: normal;
            min-width: 180px;
        }

        .fixed-table-container {
            border-color: var(--line);
            border-radius: 8px;
        }

        @media (max-width: 767px) {
            .board-shell {
                width: min(100% - 20px, 1180px);
                padding: 16px 0 32px;
            }

            .card-header,
            .card-body {
                padding: 14px;
            }

            .card-header {
                flex-wrap: wrap;
                gap: 12px;
            }

            .card-nav {
                font-size: .98rem;
            }

            #toolbar {
                align-items: stretch;
            }

            #toolbar #idSelect,
            #toolbar #idSearch,
            #toolbar .toolbar-label,
            #button {
                width: 100%;
                flex-basis: 100%;
                max-width: none;
            }

            .form-panel {
                padding: 14px;
            }

            .form-panel .row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .form-panel .action-row {
                grid-template-columns: 1fr;
            }

            .mobile-hide {
                display: none;
            }

            .table td,
            .table th {
                padding: 9px 7px;
                font-size: .9rem;
            }
        }
    </style>
</head>

<body class="board-page">

    <div class="gp-visitor-strip">
        <img class="gp-strip-logo" src="https://www.chaitalkkid.co.kr/gulpin/portal_image/gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
        <span class="pill">글핀</span><span id="gpWelcome">방문자님 환영합니다</span>
    </div>
    <header class="gp-site-header">
        <a class="gp-home-link" href="../welcome.php">🏠 Home</a>
        <button class="gp-burger" onclick="document.getElementById('gpMainNav').classList.toggle('open')">☰</button>
        <nav class="gp-main-nav" id="gpMainNav">
            <a href="../welcome.php">글핀이란?</a>
            <a href="../welcome.php">글핀 중국어</a>
            <a href="../welcome.php">상담·신청</a>
            <a href="../welcome.php">글핀 가맹점 모집</a>
        </nav>
    </header>

    <div class="board-shell">
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" id="cardList">
                        <div class="card-header">
                            <h5>자료목록</h5>
                            <ol class="card-nav">
                                <li><a href="../index.php">Home</a></li>
                                <li><a href="javascript:activeUpload()">Upload</a></li>
                            </ol>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body pad">

                            <div class="mb-3 table-responsive">
                                <div id="toolbar">
                                    <button id="button" class="btn btn-danger btn-sm" disabled><i
                                            class="fa fa-trash"></i> &nbsp;삭제</button>

                                    <label class="input-group-text toolbar-label" for="idSelect">분류선택</label>
                                    <select class="custom-select" id="idSelect">
                                        <option value="all" selected>전체</option>
                                        <option value="n1">01호</option>
                                        <option value="n2">02호</option>
                                        <option value="n3">03호</option>
                                        <option value="n4">04호</option>
                                        <option value="n5">05호</option>
                                        <option value="n6">06호</option>
                                        <option value="n7">07호</option>
                                        <option value="n8">08호</option>
                                        <option value="n9">09호</option>
                                        <option value="n10">10호</option>
                                        <option value="n11">11호</option>
                                        <option value="n12">12호</option>
                                        <option value="e1">기타1</option>
                                        <option value="e2">기타2</option>
                                        <option value="y1">연간자료</option>
                                        <!-- Add more options as needed -->
                                    </select>

                                    <input type="text" class="form-control" id="idSearch" placeholder="검색">

                                </div>
                                <table id="idBoardtable" class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th class="mobile-hide">순서</th>
                                            <th>제목</th>
                                            <th class="mobile-hide">분류</th>
                                            <!-- <th data-field="id" data-width="150" class="mobile-hide">종류</th> -->
                                            <th class="mobile-hide">날짜</th>
                                            <th>File</th>
                                            <th>삭제</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
            </div>
            <!-- ./row -->
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" id="cardUpload">
                        <div class="card-header">
                            <h5>자료등록</h5>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body pad">
                            <div class="mb-3 table-responsive">
                                <form id="form" method="post" enctype="multipart/form-data">
                                    <div class="container form-panel">
                                        <div class="row">
                                            <div class="col-25">
                                                <label for="Record" class="control-label">제목</label>
                                            </div>
                                            <div class="col-75">
                                                <input type="text" class="form-control" id="idContent">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-25">
                                                <label for="Record" class="control-label">내용</label>
                                            </div>
                                            <div class="col-75">
                                                <!-- <textarea type="text" rows="4" cols="50" id="idDesc"></textarea> -->
                                                <div rows="4" cols="50"  id="idDesc" class="editable-area" contenteditable="true"></div>

                                            <button type="button" class="button btn btn-info btn-sm w-100" onclick="addLink()">링크 추가</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-25">
                                                <label class="control-label" for="idSelect2">자료선택</label>
                                            </div>
                                            <div class="col-75">
                                                <select class="custom-select" id="idSelect2">
                                                    <option value="n1">01호</option>
                                                    <option value="n2">02호</option>
                                                    <option value="n3">03호</option>
                                                    <option value="n4">04호</option>
                                                    <option value="n5">05호</option>
                                                    <option value="n6">06호</option>
                                                    <option value="n7">07호</option>
                                                    <option value="n8">08호</option>
                                                    <option value="n9">09호</option>
                                                    <option value="n10">10호</option>
                                                    <option value="n11">11호</option>
                                                    <option value="n12">12호</option>
                                                    <option value="e1">기타1</option>
                                                    <option value="e2">기타2</option>
                                                    <option value="y1">연간자료</option>
                                                    <!-- Add more options as needed -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="box">
                                                <div class="col-25">
                                                    <label for="files-upload" class="control-label">자료</label>
                                                </div>
                                                <div class="col-75">
                                                    <div>
                                                        <a class='btn btn-primary file-pick' href='javascript:;'>
                                                            Choose File...
                                                            <input type="file" multiple="multiple" id="files-upload"
                                                                name="files-upload" size="40">
                                                        </a>
                                                        <span class='badge badge-info' id="upload-file-info"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-25">
                                                <label id="dvPreviewLB" for="dvPreview" class="control-label"
                                                    style="display: none">Files</label>
                                            </div>
                                            <div class="col-75">
                                                <div id="dvPreview">

                                                </div>
                                            </div>
                                        </div>

                                        <span id="msg"></span><br />
                                        <div class="row">
                                            <div class="box">
                                                <div class="col-25">
                                                    <label class="control-label">진행율</label>
                                                </div>
                                                <div class="col-75 inset-border progress" id="progress" style="display:none;">
                                                    <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;">0%</div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row submit-row">
                                            <button type="submit" value="Submit" class="btn btn-primary">등록</button>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
            </div>
            <!-- ./row -->
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" id="cardFileList">
                        <div class="card-header">
                            <h5>상세내용</h5>
                        </div>

                        <div class="card-body pad">
                            <div class="mb-3 table-responsive">
                                <form id="form" method="post" enctype="multipart/form-data">
                                    <div class="container form-panel">
                                        <div class="row">
                                            <div class="col-25">
                                                <label for="Record" class="control-label">No</label>
                                            </div>
                                            <div class="col-75">
                                                <input type="text" class="form-control" id="idNum" readonly>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-25">
                                                <label for="Record" class="control-label">제목</label>
                                            </div>
                                            <div class="col-75">
                                                <input type="text" class="form-control" id="idTitle">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-25">
                                                <label for="Record" class="control-label">내용</label>
                                            </div>
                                            <div class="col-75">
                                                <!-- <input type="text" class="form-control" id="idDesc2"> -->
                                                <!-- <textarea type="text" rows="4" cols="50" id="idDesc2"></textarea> -->
                                                <div rows="4" cols="50"  id="idDesc2" class="editable-area" contenteditable="true"></div>
                                                <button type="button" class="button btn btn-info btn-sm w-100" onclick="addLink()">링크 추가</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-25">
                                                <label for="Record" class="control-label">분류</label>
                                            </div>
                                            <div class="col-75">
                                                <input type="text" class="form-control" id="idCate">
                                                <!-- <input type="text" class="form-control" id="idID"> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-25">
                                                <label for="Record" class="control-label">작성일</label>
                                            </div>
                                            <div class="col-75">
                                                <input type="text" class="form-control" id="idDate">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="box">
                                                <div class="col-25">
                                                    <label for="files-upload" class="control-label">Files</label>
                                                </div>
                                                <div class="col-75 inset-border" id="idFiles">

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row action-row">
                                            <div class="col-12">
                                                <button type="button" id="btToggle" onclick="closeDownload()" class="btn btn-primary btn-sm w-100">자료실가기</button>
                                            </div>
                                            <div class="col-12">
                                                <button type="button" onclick="updateBoard()" class="btn btn-info btn-sm w-100">수정하기</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-->
            </div>
            <!-- ./row -->
        </section>
    </div>

    <p class="text-center mt-3"><strong> </strong></p>

    <!-- </div> -->

    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="attachmentModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="attachmentModalTitle">첨부 파일</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="attachment-meta">
                        <span id="attachmentModalCate"></span>
                        <span id="attachmentModalDate"></span>
                    </div>
                    <div id="attachmentModalDesc" class="attachment-desc"></div>
                    <div id="attachmentModalFiles" class="attachment-list"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btCancel" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                </div>
            </div>

        </div>
    </div>

</body>



<script>
    let gid;
    let user = "";
    let role = "";
    let conf = "";
    let name = "";
    let loc = "";
    let boardRows = [];
    let allBoardRows = [];
    let host = "https://chaitalkkid.co.kr/board/uploads/";



    function showElement(selector) {
        const element = qs(selector);
        if (element) element.style.display = "";
    }

    function hideElement(selector) {
        const element = qs(selector);
        if (element) element.style.display = "none";
    }

    document.addEventListener('DOMContentLoaded', function() {
        const loginfo = getLocalStorage('infochaitalk');
        if (loginfo) {
            user = loginfo['user'];
            role = loginfo['role'];
            conf = loginfo['conf'];
            name = loginfo['name'];
            loc = loginfo['loca'];
        }

        if (loc == "localhost") {
            host = "http://localhost:3000/assets/board/uploads/";
        }

        hideElement("#cardUpload");
        hideElement("#cardFileList");

        qs("#button").addEventListener("click", function() {
            const ids = qsa("#idBoardtable tbody input[type='checkbox']:checked").map((checkbox) => checkbox.value);
            deleteBoardList(ids);
        });
        updateDeleteButtonState();

        qs("#files-upload").addEventListener("change", handleFilePreview);
        qs("#cardUpload form").addEventListener("submit", uploadBoard);
        qs("#idSelect").addEventListener("change", function() {
            DisplayBoard1(this.options[this.selectedIndex].text);
        });
        qs("#idSearch").addEventListener("input", filterBoardRows);

        qs("#idBoardtable").addEventListener("click", function(e) {
            const attachmentButton = e.target.closest("[data-attachment-row]");
            const rowElement = e.target.closest("tr[data-row-index]");

            if (!rowElement) return;

            const row = boardRows[Number(rowElement.dataset.rowIndex)];
            if (!row) return;

            if (attachmentButton) {
                e.stopPropagation();
                showAttachmentModal(row);
                return;
            }

            if (e.target.matches("input[type='checkbox']")) return;
            showBoardDetail(row.num);
        });
        qs("#idBoardtable").addEventListener("change", function(e) {
            if (e.target.matches("input[type='checkbox']")) updateDeleteButtonState();
        });

        DisplayBoard("전체");
    });

    function getFileIcon(fileName) {
        const file = fileName.split('.').pop().toLowerCase();

        if (file.includes("pdf")) return 'fa-solid fa-file-pdf';
        if (file.includes("png") || file.includes("jpg") || file.includes("jpeg") || file.includes("gif")) return 'fas fa-file-image';
        if (file.includes("xlsx")) return 'fas fa-file-excel';
        if (file.includes("pptx")) return 'fas fa-file-powerpoint';
        if (file.includes("html")) return 'fa-brands fa-html5';
        if (file.includes("exe")) return 'fas fa-running';
        if (file.includes("mp4") || file.includes("avi") || file.includes("mov") || file.includes("wmv")) return 'fa-solid fa-file-video';

        return 'fa-regular fa-file';
    }

    function parseContents(contents) {
        try {
            return JSON.parse(contents || "[]");
        } catch (e) {
            return [];
        }
    }

    function getAttachmentLinks(contents) {
        return parseContents(contents).map((item, index) => ({
            text: `   ${index} : ${item.name}   size: (${(Number(item.size) / 1024 / 1024).toFixed(2)}) MB`,
            href: host + item.fakename
        }));
    }

    function appendAttachmentLink(target, link, className = '') {
        const anchor = document.createElement('a');
        anchor.textContent = link.text;
        anchor.href = link.href;
        anchor.target = '_blank';
        if (className) anchor.className = className;

        const iconSpan = document.createElement('span');
        iconSpan.className = 'icon-span';

        const icon = document.createElement('i');
        icon.className = getFileIcon(link.text);
        iconSpan.appendChild(icon);
        anchor.prepend(iconSpan);
        target.appendChild(anchor);
    }

    function renderBoardTable(rows) {
        boardRows = Array.isArray(rows) ? rows : [];
        const table = qs("#idBoardtable");
        let tbody = qs("tbody", table);

        if (!tbody) {
            tbody = document.createElement("tbody");
            table.appendChild(tbody);
        }

        tbody.innerHTML = "";

        if (boardRows.length === 0) {
            const row = tbody.insertRow();
            const cell = row.insertCell();
            cell.colSpan = 6;
            cell.className = "text-center text-muted py-4";
            cell.textContent = "등록된 자료가 없습니다.";
            updateDeleteButtonState();
            return;
        }

        boardRows.forEach((row, index) => {
            const tr = tbody.insertRow();
            tr.dataset.rowIndex = index;
            tr.style.cursor = "pointer";

            appendCell(tr, row.num, "mobile-hide");
            appendCell(tr, row.title);
            appendCell(tr, row.cate, "mobile-hide");
            appendCell(tr, row.rdate, "mobile-hide");
            appendAttachmentCell(tr, row, index);
            appendCheckboxCell(tr, row.num);
        });
        updateDeleteButtonState();
    }

    function updateDeleteButtonState() {
        const deleteButton = qs("#button");
        if (!deleteButton) return;

        deleteButton.disabled = qsa("#idBoardtable tbody input[type='checkbox']:checked").length === 0;
    }

    function filterBoardRows() {
        const keyword = qs("#idSearch").value.trim().toLowerCase();

        if (!keyword) {
            renderBoardTable(allBoardRows);
            return;
        }

        const filteredRows = allBoardRows.filter((row) => {
            const searchText = [
                row.title,
                row.cate,
                row.rdate,
                row.desc
            ].join(" ").toLowerCase();

            return searchText.includes(keyword);
        });

        renderBoardTable(filteredRows);
    }

    function appendCell(row, text, className = "") {
        const cell = row.insertCell();
        cell.textContent = text ?? "";
        cell.className = className;
        cell.style.textAlign = "center";
    }

    function appendAttachmentCell(row, item, index) {
        const cell = row.insertCell();
        const button = document.createElement("button");
        const count = parseContents(item.contents).length;

        button.type = "button";
        button.className = "attachment-chip";
        button.dataset.attachmentRow = index;
        button.innerHTML = `<i class="fa-regular fa-folder-open"></i> ${count}`;
        cell.appendChild(button);
        cell.style.textAlign = "center";
    }

    function appendCheckboxCell(row, num) {
        const cell = row.insertCell();
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.value = num;
        cell.appendChild(checkbox);
        cell.style.textAlign = "center";
    }

    function showAttachmentModal(row) {
        const jsarr = getAttachmentLinks(row['contents']);
        const modalFiles = qs('#attachmentModalFiles');

        modalFiles.innerHTML = "";
        qs('#attachmentModalTitle').textContent = row['title'];
        qs('#attachmentModalCate').textContent = '분류: ' + row['cate'];
        qs('#attachmentModalDate').textContent = '작성일: ' + row['rdate'];
        qs('#attachmentModalDesc').innerHTML = "";

        jsarr.forEach((link) => appendAttachmentLink(modalFiles, link, 'attachment-link'));

        if (jsarr.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'text-muted';
            empty.textContent = '등록된 첨부 파일이 없습니다.';
            modalFiles.appendChild(empty);
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('myModal')).show();
    }

    DisplayBoard = (cate) => {
        const selectedCate = cate || "전체";
        const options = {
            functionName: 'SShowBoardlist',
            otherData: {
                num: selectedCate,
                cate: 1
            }
        };

        CallAjax("SMethods.php", "POST", options, function(resp) {
            allBoardRows = Array.isArray(resp) ? resp : [];
            filterBoardRows();
            CallToast('게시판 보기  !', "success");
        }, function() {
            CallToast('게시판 보기  !', "error");
        });
    }

    DisplayBoard1 = (cate) => {
        DisplayBoard(cate || "전체");
    }

    activeUpload = () => {
        if (user == "admin" || user == "게시판" || user == "chaitalk") {
            hideElement("#cardList");
            showElement("#cardUpload");
            hideElement("#cardFileList");
        } else {
            CallToast("자료실은 admin만 올릴수 있습니다", "error");
        }
    }

    closeUpload = () => {
        showElement("#cardList");
        hideElement("#cardUpload");
        hideElement("#cardFileList");
    }

    closeDownload = () => {
        showElement("#cardList");
        hideElement("#cardUpload");
        hideElement("#cardFileList");
    }

    function showBoardDetail(num) {
        hideElement("#cardList");
        hideElement("#cardUpload");
        showElement("#cardFileList");

        const options = {
            functionName: 'SShowBoardlist',
            otherData: {
                num: num,
                cate: 0,
            }
        };

        CallAjax("SMethods.php", "POST", options, function(resp) {
            const row = resp[0];
            const jsarr = getAttachmentLinks(row['contents']);
            const idFiles = qs('#idFiles');

            idFiles.innerHTML = "";
            qs("#idNum").value = row['num'];
            qs("#idTitle").value = row['title'];
            qs("#idCate").value = row['cate'];
            qs("#idDesc2").innerHTML = row['desc'];
            qs("#idDate").value = row['rdate'];

            jsarr.forEach((link) => {
                appendAttachmentLink(idFiles, link);
                idFiles.appendChild(document.createElement('br'));
            });

            if (jsarr.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'text-muted';
                empty.textContent = '등록된 첨부 파일이 없습니다.';
                idFiles.appendChild(empty);
            }

            CallToast('게시판 상세보기 조회 !', "success");
        }, function() {
            CallToast('게시판 상세보기  !', "error");
        });
    }

    deleteBoardList = (ids) => {
        if (!(user == 'admin' || user == "게시판" || user == 'chaitalk')) {
            CallToast('게시판 삭제는 admin만 가능 합니다.  !', "error");
            return;
        }

        if (!ids.length) {
            CallToast('삭제할 자료를 선택하세요.', "error");
            return;
        }

        if (!confirm("정말 삭제 하시겠습니까 ??")) return;

        const options = {
            functionName: 'SDeleteBoardlist',
            otherData: {
                num: ids
            }
        };

        CallAjax("SMethods.php", "POST", options, function() {
            const select = qs("#idSelect");
            DisplayBoard1(select.options[select.selectedIndex].text);
            CallToast('게시판 삭제  !', "success");
        }, function() {
            CallToast('게시판 삭제  !', "error");
        });
    }

    function handleFilePreview() {
        const input = qs('#files-upload');
        const preview = qs('#dvPreview');
        const progress = qs('#progress');
        const progressBar = qs('#progressBar');
        const uploadInfo = qs('#upload-file-info');
        const previewLabel = qs('#dvPreviewLB');
        const files = Array.from(input.files || []);
        const imageRegex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp|.PNG)$/;

        uploadInfo.textContent = files.length;
        previewLabel.style.display = "block";
        progress.style.display = "";
        progressBar.style.display = "";
        progressBar.style.width = "0%";
        progressBar.textContent = "0%";
        preview.innerHTML = "";

        if (typeof FileReader === "undefined") {
            alert("This browser does not support HTML5 FileReader.");
            return;
        }

        files.forEach((file) => {
            if (imageRegex.test(file.name.toLowerCase())) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement("img");
                    img.style.cssText = "height:50px;width:50px;margin-left:5px";
                    img.src = e.target.result;
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else {
                const name = document.createElement("div");
                name.textContent = file.name;
                preview.appendChild(name);
            }
        });
    }

    function uploadBoard(e) {
        e.preventDefault();

        const files = qs('#files-upload').files;
        if (!files || files.length === 0) {
            CallToast('SUploadBoard Upload Error!', "error");
            return;
        }

        const formData = new FormData();
        Array.from(files).forEach((file, index) => {
            formData.append("file" + index, file);
        });

        const selEle = qs("#idSelect2");
        const selOpt = selEle[selEle.selectedIndex];

        formData.append("idContent", qs('#idContent').value);
        formData.append("idDesc", qs('#idDesc').innerHTML);
        formData.append("idSelect2", selOpt.text);
        formData.append("user", user);
        formData.append('functionName', 'SUploadBoard');

        CallAjax10("SMethods.php", "POST", formData, function() {
            CallToast('자료실에 등록이 성공적으로 수행했습니다. !', "success");
            hideElement("#progress");
            hideElement("#progressBar");
            closeUpload();
        }, function() {
            CallToast('SUploadBoard Upload Error!', "error");
        }, qs("#progressBar"));
    }

    function updateBoard() {
        if (user == "admin" || user == "게시판" || user == "chaitalk" || role == 9) {
            var form_data = new FormData();
            form_data.append("idNum", qs('#idNum').value);
            form_data.append("idContent", qs('#idTitle').value);
            form_data.append("idDesc", qs('#idDesc2').innerHTML);
            var selEle = document.getElementById("idSelect2");
            var selOpt = selEle[selEle.selectedIndex]
            form_data.append("idSelect2", selOpt.text)
            form_data.append("user", user)

            dispList = (resp) => {
                CallToast('자료실에 수정이 성공적으로 수행했습니다. !', "success")
                //document.getElementById('pdfDiv').src = resp['url'];
            }
            dispErr = (xhr) => {
                CallToast('SUdateBoard Upload Error!', "error")
            }
            form_data.append('functionName', 'SUdateBoard');
            CallAjax1("SMethods.php", "POST", form_data, dispList, dispErr);
            closeUpload();
        } else
            alert('admin user만 수정 가능합니다.')
    }
    function addLink() {
      const url = prompt("링크 URL을 입력하세요:", "https://");
      if (!url) return;

      const text = prompt("링크 텍스트를 입력하세요:", url);
      if (!text) return;

      const selection = window.getSelection();
      if (!selection.rangeCount) return;

      const range = selection.getRangeAt(0);
      range.deleteContents();

      const anchor = document.createElement('a');
      anchor.href = url.startsWith('http') ? url : 'https://' + url;
      anchor.target = '_blank';
      anchor.textContent = text;

      range.insertNode(anchor);
      range.setStartAfter(anchor);
      range.setEndAfter(anchor);
      selection.removeAllRanges();
      selection.addRange(range);
    }

    document.getElementById('idDesc2').addEventListener('click', function(e) {
      if (e.target.tagName === 'A' && !window.getSelection().toString()) {
        e.preventDefault();
        window.open(e.target.href, '_blank');
      }
    });

    // document.getElementById('idDesc').addEventListener('click', function(e) {
    //   if (e.target.tagName === 'A' && !window.getSelection().toString()) {
    //     e.preventDefault();
    //     window.open(e.target.href, '_blank');
    //   }
    // });

    (function(){
        var info = getLocalStorage('infochaitalk');
        if(info && info.name){
            document.getElementById('gpWelcome').textContent = info.name + '님 환영합니다';
        }
    })();
</script>

</html>
