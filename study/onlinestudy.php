<?php
// 캐시 무효화 헤더
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html lang="ko" class="notranslate" translate="no">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="google" content="notranslate">

    <!-- ── JS ── -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alasql/4.5.0/alasql.min.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="mp4list.js?v=<?= filemtime('mp4list.js') ?>"></script>
    <script src="../common.js?v=<?= filemtime('../common.js') ?>"></script>

    <!-- ── CSS ── -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/brands.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&display=swap" rel="stylesheet">
    <link rel="stylesheet" as="style" crossorigin href="https://cdnjs.cloudflare.com/ajax/libs/pretendard/1.3.9/static/pretendard.css" />
    <link href="onlinestudy.css?v=<?= filemtime('onlinestudy.css') ?>" rel="stylesheet">

    <style>
        /* ── 학습 카드 이미지 ── */
        .stdcls {
            width: 22vh;
            height: 30vh;
        }

        .tdstdcls {
            height: 31vh;
        }

        /* ── 카드 배경 ── */
        .divstdcls {
            background-color: #FFFAE3;
            margin: 2px;
            padding: 2px;
        }

        /* ── 좌측 컨트롤 패널 ── */
        #divControl {
            height: 436px;
        }

        .table {
            margin-bottom: 0;
        }

        /* ── 커버 이미지 영역 ── */
        #divIntro {
            height: 536px;
        }

        /* ── E-Book 이미지 간격 ── */
        #idstudy img {
            padding: 15px;
        }

        /* ── 버튼 테두리 전역 제거 ── */
        .btn {
            border: none;
            outline: none;
        }

        /* ── 텍스트 드래그 방지 ── */
        #idDis {
            user-select: none;
        }

        /* ── step0 표시/숨김 클래스 ── */
        .step0-hidden {
            display: none !important;
        }

        .step0-visible {
            display: flex !important;
        }

        /* E-Book 클릭 카운트 뱃지 */
        .ebook-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #e74c3c;
            color: white;
            font-size: 12px;
            font-weight: bold;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            pointer-events: none;
        }

        /* ── select2 비활성 옵션 스타일 ── */
        .select2-results__option--disabled {
            color: #aaa !important;
            background-color: #f0f0f0 !important;
            cursor: not-allowed !important;
        }

        /* ════════════════════════════════════════
         * 모바일 반응형 (480px 이하)
         * ════════════════════════════════════════ */
        @media (max-width: 480px) {
            .stdcls {
                width: 12vh;
                height: 15vh;
            }

            .tdstdcls {
                height: 180px;
            }

            #divControl {
                height: 380px;
            }

            .topDiv {
                margin-top: 1vh;
            }

            #divIntro {
                height: 330px;
            }

            #idstudy img {
                padding: 10px;
            }

            #idMotion {
                margin-top: 60px !important;
                font-weight: bold;
            }
        }

        /* ── 모바일 배너 이미지 (768px 이하) ── */
        @media (max-width: 768px) {
            #idBanner img {
                width: 55px !important;
                height: 30px !important;
            }
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light" style="overflow: auto;">

    <div class="row">

        <!-- ═══════════════════════════════════════════ -->
        <!-- 좌측 상단 : 커버 이미지                       -->
        <!-- ═══════════════════════════════════════════ -->
        <div class="col-lg-6 topDiv">
            <div id="divIntro" class="row divstdcls">
                <img id="idImgCover" src='..\assets\portal_image\1.jpg'
                    class="img-responsive" style="width:100%; height:100%; object-fit:cover;">
            </div>
        </div>

        <!-- ═══════════════════════════════════════════ -->
        <!-- 좌측 하단 : 교재 선택 컨트롤 패널              -->
        <!-- ═══════════════════════════════════════════ -->
        <div class="col-lg-6 topDiv" style="background:#FFFAE3">
            <div id="divControl" class="row divstdcls">
                <div style="text-align:center">
                    <table class="table rounded rounded-4 giro-table boder-success overflow-hidden"
                        style="table-layout:fixed; word-break:break-all; height:auto">
                        <thead>
                            <tr>
                                <th height="70" colspan="2" class="talign" scope="col"
                                    style="font-size:1.5rem; background-color:#FFE98D; color:#2d2d2d;
                                       text-align:-webkit-center; border:0; border-radius:5px;">
                                    <b>한문왕 교재 선택</b>
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- ── 행 1 : 한문왕 0 ── -->
                            <tr>
                                <td class="talign" style="background-color:#5FA63B; height:80px;">
                                    <div class="d-grid">
                                        <button class="btn btn-block" id="basic0" value="#5FA63B"
                                            disabled onclick="changecolor(this)"
                                            style="background-color:#5FA63B">
                                            <p style="font-size:1.2rem; margin-bottom:0"><strong>한문왕 0</strong></p>
                                        </button>
                                    </div>
                                </td>
                                <td class="talign thh colr" style="height:50px; background-color:#5FA63B;">
                                    <div id="idBanner" class="d-flex justify-content-center align-items-center">
                                        <!-- 워크북 (step=0일 때만 표시) -->
                                        <div id="idStep00" class="d-flex justify-content-center align-items-center step0-hidden">
                                            <a id="w0_1" href="#" style="text-align:center; position:relative; display:inline-block;">
                                                <img id="idEbook00" src="images/story0.png"
                                                    data-bs-toggle="tooltip" title="인성동화 워크북"
                                                    style="width:90px; height:50px; display: ">
                                                <span id="idEbook00Badge" class="ebook-badge">0</span>
                                            </a>
                                        </div>&nbsp;
                                        <!-- E-Book (step=0일 때만 표시) -->
                                        <div id="idStep0" class="d-flex justify-content-center align-items-center step0-hidden">
                                            <a id="w0_2" href="#" style="text-align:center; position:relative; display:inline-block;">
                                                <img id="idEbook0" src="images/story0.png"
                                                    data-bs-toggle="tooltip" title="인성동화 E-Book"
                                                    style="width:90px; height:50px">
                                                <span id="idEbook0Badge" class="ebook-badge">0</span>
                                            </a>
                                        </div>&nbsp;
                                        <!-- E-Book 학습관 -->
                                        <div class="d-flex justify-content-center align-items-center">
                                            <a id="w1_3" href="#" style="text-align:center; position:relative; display:inline-block;">
                                                <img id="idEbook" src="images/a.png"
                                                    data-bs-toggle="tooltip" title="E Book 학습관"
                                                    style="width:90px; height:50px;display: ">
                                                <span id="idEbookBadge" class="ebook-badge">0</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- ── 행 2 : 한문왕 A  +  Vol 드롭다운 ── -->
                            <tr>
                                <td class="talign" style="background-color:#FFA63B; height:80px;">
                                    <div class="d-grid">
                                        <button class="btn btn-block" id="basic" value="#FFA63B"
                                            disabled onclick="changecolor(this)"
                                            style="background-color:#FFA63B">
                                            <p style="font-size:1.2rem; margin-bottom:0"><strong>한문왕 A</strong></p>
                                        </button>
                                    </div>
                                </td>
                                <td class="talign thh colr" style="height:50px; background-color:#FFA63B;">
                                    <!--
                                  ■ Vol 드롭다운 활성화 규칙
                                    • v1 = 3월, v2 = 4월, … , v12 = 2월  (학기순환)
                                    • JS 의 getActiveVolumes() 가 현재월·전월 → v값 계산
                                    • applyMonthFilter() 에서 해당 두 개만 활성, 나머지 disabled
                                    • guest / guest2 계정은 setGuest() 가 마지막으로 덮어씀
                                -->
                                    <div id="leftdiv" class="input-group" style="background-color:#FFA63B;">
                                        <div id="idselect" class="input-group-text" style="background-color:#FFA63B;">Vol:</div>
                                        <select id="select-field" class="form-select custom-select">
                                            <option value="v1">v1</option>
                                            <option value="v2">v2</option>
                                            <option value="v3">v3</option>
                                            <option value="v4">v4</option>
                                            <option value="v5">v5</option>
                                            <option value="v6">v6</option>
                                            <option value="v7">v7</option>
                                            <option value="v8">v8</option>
                                            <option value="v9">v9</option>
                                            <option value="v10">v10</option>
                                            <option value="v11">v11</option>
                                            <option value="v12">v12</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>

                            <!-- ── 행 3 : 한문왕 B  +  홈 아이콘 / 오디오 ── -->
                            <tr>
                                <td class="talign thh" style="background-color:#5FCAFF; height:80px;">
                                    <div class="d-grid">
                                        <button class="btn btn-block" id="step1" value="#5FCAFF"
                                            disabled onclick="changecolor(this)"
                                            style="background-color:#5FCAFF;">
                                            <p style="font-size:1.2rem; margin-bottom:0"><strong>한문왕 B</strong></p>
                                        </button>
                                    </div>
                                </td>
                                <td class="talign thh colr" style="background-color:#FFA63B;">
                                    <span class="d-flex justify-content-center align-items-center input-group-text colr"
                                        style="background-color:#FFA63B; border:0;">
                                        <a href="../index.php" style="text-decoration:none;"
                                            data-bs-toggle="tooltip" title="홈으로 이동">
                                            <i class="fa fa-home fa-2x" style="color:Indigo;"></i>
                                        </a>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <!-- 오디오 플레이어 -->
                                        <div class="audio-player" style="background:transparent;">
                                            <button id="playBtn" style="border:none; background:transparent; font-size:2rem">▶</button>
                                            <button id="volumeBtn" style="border:none; background:transparent; font-size:2rem">🔊</button>
                                            <audio id="audio" src="test.mp3"></audio>
                                        </div>
                                    </span>
                                </td>
                            </tr>

                            <!-- ── 행 4 : 한문왕 T  +  복습 영상 ── -->
                            <tr id="idRevtable">
                                <td class="talign thh" style="background-color:#B5B1FC; height:80px;">
                                    <div class="d-grid">
                                        <button class="btn btn-block" id="step2" value="#B5B1FC"
                                            disabled onclick="changecolor(this)"
                                            style="background-color:#B5B1FC;">
                                            <p style="font-size:1.2rem; margin-bottom:0"><strong>한문왕 T</strong></p>
                                        </button>
                                    </div>
                                </td>
                                <td class="talign thh colr" style="background-color:#5FCAFF;">
                                    <span class="d-flex justify-content-center align-items-center input-group-text colr"
                                        style="background-color:#5FCAFF; border:0;">
                                        <a id="idReview" href="#" style="text-align:center;">
                                            <img src="images/review.png" data-bs-toggle="tooltip" title="복습 영상"
                                                style="width:120px; height:60px">
                                        </a>
                                    </span>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════ -->
        <!-- 우측 상단 : 영상 학습                          -->
        <!-- ═══════════════════════════════════════════ -->
        <div id="idMotion" class="col-lg-6" style="margin-top:15px;">
            <div class="row divstdcls">
                <div style="margin-top:10px; text-align:center">
                    <table class="table table-hover rounded rounded-4 giro-table boder-success overflow-hidden"
                        style="table-layout:fixed; word-break:break-all;">
                        <thead>
                            <tr id="idstudyHead">
                                <th height="70" id="Head-1" colspan="3" class="talign colr" scope="col"
                                    style="font-size:1.5rem; background-color:#FFe576; text-align:-webkit-center;">
                                    <b>영상</b>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="idstudy">
                                <td class="talign align-middle tdstdcls" style="background-color:white;">
                                    <div class="center-container d-flex justify-content-center align-items-center">
                                        <a id="w1_1" href="#" style="position:relative; display:inline-block;">
                                            <img class="stdcls ebook-img" src="images/mo1.jpg"
                                                data-bs-toggle="tooltip" title="동영상 학습 1"
                                                style="object-fit:cover;">
                                            <span id="idW11Badge" class="ebook-badge">0</span>
                                        </a>
                                    </div>
                                </td>
                                <td class="talign align-middle tdstdcls" style="background-color:white;">
                                    <div class="center-container d-flex justify-content-center align-items-center">
                                        <a id="w1_2" href="#" style="position:relative; display:inline-block;">
                                            <img class="stdcls ebook-img" src="images/mo2.jpg"
                                                data-bs-toggle="tooltip" title="동영상 학습 2"
                                                style="object-fit:cover;">
                                            <span id="idW12Badge" class="ebook-badge">0</span>
                                        </a>
                                    </div>
                                </td>
                                <td class="talign align-middle tdstdcls" style="background-color:white;">
                                    <div class="center-container d-flex justify-content-center align-items-center">
                                        <a id="w1_3_1" href="#" style="position:relative; display:inline-block;">
                                            <img class="stdcls ebook-img" src="images/mo0.jpg"
                                                data-bs-toggle="tooltip" title="동영상 학습 3"
                                                style="object-fit:cover;">
                                            <span id="idW131Badge" class="ebook-badge">0</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════ -->
        <!-- 우측 하단 : 게임                               -->
        <!-- ═══════════════════════════════════════════ -->
        <div class="col-lg-6" style="margin-top:15px;">
            <div class="row divstdcls">
                <div style="margin-top:10px; text-align:center">
                    <table class="table table-hover rounded rounded-4 giro-table boder-success overflow-hidden"
                        style="table-layout:fixed; word-break:break-all; height:auto">
                        <thead>
                            <tr>
                                <th height="70" id="Head-3" colspan="3" class="talign colr" scope="col"
                                    style="font-size:1.5rem; background-color:#FFe576; text-align:-webkit-center;">
                                    <b>게임</b>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="talign align-middle tdstdcls" style="background-color:white;">
                                    <div class="center-container d-flex justify-content-center align-items-center">
                                        <a id="w2_1" href="#" style="position:relative; display:inline-block;">
                                            <img class="stdcls" src="images/game1.png"
                                                data-bs-toggle="tooltip" title="매칭게임">
                                            <span id="idW21Badge" class="ebook-badge">0</span>
                                        </a>
                                    </div>
                                </td>
                                <td class="talign align-middle tdstdcls" style="background-color:white;">
                                    <div class="center-container d-flex justify-content-center align-items-center">
                                        <a id="w2_2" href="#" style="position:relative; display:inline-block;">
                                            <img class="stdcls" src="images/game2.png"
                                                data-bs-toggle="tooltip" title="메모리게임">
                                            <span id="idW22Badge" class="ebook-badge">0</span>
                                        </a>
                                    </div>
                                </td>
                                <td class="talign align-middle tdstdcls" style="background-color:white;">
                                    <div id="idDis" class="center-container d-flex justify-content-center align-items-center">
                                        <a id="w2_3" href="#" style="position:relative; display:inline-block;">
                                            <img class="stdcls" src="images/game3.png"
                                                data-bs-toggle="tooltip" title="파자게임">
                                            <span id="idW23Badge" class="ebook-badge">0</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!-- /.row -->

    <!-- ═══════════════════════════════════════════ -->
    <!-- 모달 : 공지사항                               -->
    <!-- ═══════════════════════════════════════════ -->
    <div class="modal fade" tabindex="-1" role="dialog" id="idInfo">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document" style="width:700px;">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div id="idImage">
                        <img src="" id="noticeImg" class="img-fluid" alt="Notice Image">
                    </div>
                    <div class="mt-4 py-2">
                        <h6 id="idNotice" class="h6"> </h6>
                    </div>
                    <div class="py-1">
                        <button id="idClose" type="button"
                            class="btn btn-sm btn-outline-success rounded-pill px-5"
                            data-bs-dismiss="modal">그만보기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════ -->
    <!-- 메인 스크립트                                  -->
    <!-- ═══════════════════════════════════════════ -->
    <script>
        /* ═══════════════════════════════════════════════════
         * 전역 상태
         * ═══════════════════════════════════════════════════ */
        var vol = "v1"; // 현재 볼륨  (v1 ~ v12)
        var stp = "A"; // 현재 단계  (0 / A / B / T)
        var stat = {}; // localStorage 저장·복원용 객체
        let user = getUser();
        let role = getRole();
        // if ( role != "9")
        //     user = "guest"; // 테스트용 임시 설정
        let colorClicked = false; // 교재 단계 버튼이 한 번이라도 클릭되었는지

        /* ═══════════════════════════════════════════════════
         * Vol 옵션 정의
         *   optionsData1 – A단계 용 (v1 ~ v12)
         *   optionsData2 – T단계 용 (v1 ~ v10, 급수별 라벨)
         * ═══════════════════════════════════════════════════ */
    const optionsData1 = [
        { value:'v1',  text:'v1',      disabled:false },
        { value:'v2',  text:'v2',      disabled:false },
        { value:'v3',  text:'v3',      disabled:false },
        { value:'v4',  text:'v4',      disabled:false },
        { value:'v5',  text:'v5',      disabled:false },
        { value:'v6',  text:'v6',      disabled:false },
        { value:'v7',  text:'v7',      disabled:false },
        { value:'v8',  text:'v8',      disabled:false },
        { value:'v9',  text:'v9',      disabled:false },
        { value:'v10', text:'v10',     disabled:false },
        { value:'v11', text:'v11',     disabled:false },
        { value:'v12', text:'v12',     disabled:false }
    ];
    const optionsData2 = [
        { value:'v1',  text:'8급 T-1', disabled:false },
        { value:'v2',  text:'8급 T-2', disabled:false },
        { value:'v3',  text:'8급 T-3', disabled:false },
        { value:'v4',  text:'8급 T-4', disabled:false },
        { value:'v5',  text:'8급 T-5', disabled:false },
        { value:'v6',  text:'7급 T-1', disabled:false },
        { value:'v7',  text:'7급 T-2', disabled:false },
        { value:'v8',  text:'7급 T-3', disabled:false },
        { value:'v9',  text:'7급 T-4', disabled:false },
        { value:'v10', text:'7급 T-5', disabled:false }
    ];

        /* ═══════════════════════════════════════════════════
         * getActiveVolumes()
         * ─────────────────────────────────────────────────
         * v1=3월  v2=4월  …  v10=12월  v11=1월  v12=2월
         * 현재 날짜 기준 "이번달" · "전달" 에 해당하는 v값을
         * { current, prev } 형태로 반환한다.
         *
         *   예시) 현재 2월  →  current="v12"  prev="v11"
         *         현재 3월  →  current="v1"   prev="v12"
         * ═══════════════════════════════════════════════════ */
        /**
         * @param {number} [targetMonth] - 선택적 월 (1~12). 생략 시 현재 월 기준.
         */
        function getActiveVolumes(targetMonth) {
            // 월(1~12) → v값 매핑 (제공해주신 규칙 유지)
            const monthToV = {
                3:'v1',  4:'v2',  5:'v3',  6:'v4',
                7:'v5',  8:'v6',  9:'v7', 10:'v8',
            11:'v9', 12:'v10', 1:'v11', 2:'v12'
            };

            // 단일 값, 배열 모두 처리: getActiveVolumes(), getActiveVolumes(4), getActiveVolumes([4,5])
            var months = targetMonth == null ? [(new Date().getMonth() + 1)]
                       : Array.isArray(targetMonth) ? targetMonth
                       : [targetMonth];

            var activeSet = new Set();
            months.forEach(function(m) {
                m = Number(m);
                if (monthToV[m]) activeSet.add(monthToV[m]);
                // 전달도 활성화
                var prev = m === 1 ? 12 : m - 1;
                if (monthToV[prev]) activeSet.add(monthToV[prev]);
            });

            // 기존 호환: current/prev도 반환
            var curMonth = months[0] || (new Date().getMonth() + 1);
            var prevMonth = curMonth === 1 ? 12 : curMonth - 1;

            return {
                current: monthToV[curMonth],
                prev: monthToV[prevMonth],
                activeSet: activeSet
            };
        }

        /* ═══════════════════════════════════════════════════
         * applyMonthFilter(optionsArr)
         * ─────────────────────────────────────────────────
         * 옵션 배열 안의 disabled 속성을 이번달·전달 기준으로
         * 재설정한다.  (배열 자체를 변경 → 이후 렌더링에 반영)
         *
         * 주의 : guest · guest2 등은 이후 setGuest() 가
         *        다시 덮어씀  →  호출 순서에 주의
         * ═══════════════════════════════════════════════════ */
        function applyMonthFilter(optionsArr) {
            const { activeSet } = getActiveVolumes([6,7]);
            optionsArr.forEach(opt => {
                opt.disabled = !activeSet.has(opt.value);
            });
        }

        /* ═══════════════════════════════════════════════════
         * 로그인 체크
         * ═══════════════════════════════════════════════════ */
        if (user === "" || getUser() === undefined)
            window.location.href = "../index.php";

        const selectElement = document.getElementById('select-field');

        /* ═══════════════════════════════════════════════════
         * setGuest()  –  guest / guest2 계정 제한
         * ─────────────────────────────────────────────────
         *   guest  : v1만 활성
         *   guest2 : v1, v2만 활성
         * 월별 필터 이후 마지막으로 호출하여 덮어쓰는다.
         * ═══════════════════════════════════════════════════ */
        function setGuest() {
        
            if (user == "guest") {
                // 모든 옵션을 순회하면서 v1만 활성화
                for (let i = 0; i < selectElement.options.length; i++) {
                    if (selectElement.options[i].value === 'v1') {
                        selectElement.options[i].disabled = false;
                        selectElement.options[i].selected = true; // v1 선택
                    } else {
                        selectElement.options[i].disabled = true;
                    }
                }
                
                // select 값을 v1으로 강제 설정
                selectElement.value = 'v1';
                vol = 'v1'; // 전역 변수도 업데이트
                
                // Select2가 초기화된 경우 업데이트
                if ($.fn.select2 && $('#select-field').hasClass('select2-hidden-accessible')) {
                    $('#select-field').val('v1').trigger('change.select2');
                }
            }
            
            if (user == "guest2") {
                // 모든 옵션을 순회하면서 v1, v2만 활성화
                let hasV1 = false;
                for (let i = 0; i < selectElement.options.length; i++) {
                    if (selectElement.options[i].value === 'v1' || 
                        selectElement.options[i].value === 'v2') {
                        selectElement.options[i].disabled = false;
                        
                        // v1이 있으면 v1을 기본 선택
                        if (selectElement.options[i].value === 'v1') {
                            hasV1 = true;
                            selectElement.options[i].selected = true;
                        }
                    } else {
                        selectElement.options[i].disabled = true;
                    }
                }
                
                // select 값을 v1으로 강제 설정
                if (hasV1) {
                    selectElement.value = 'v1';
                    vol = 'v1'; // 전역 변수도 업데이트
                }
                
                // Select2가 초기화된 경우 업데이트
                if ($.fn.select2 && $('#select-field').hasClass('select2-hidden-accessible')) {
                    $('#select-field').val('v1').trigger('change.select2');
                }
            }
        }
        setGuest(); // 초기 로드 시 적용

        /* ═══════════════════════════════════════════════════
         * step0 표시 / 숨김
         * ═══════════════════════════════════════════════════ */
        function showStep0Element() {
            // step=0: idEbook(E-Book 학습관), idEbook00(워크북) 숨김
            ['idStep0', 'idStep00'].forEach(id => {
                const el = document.getElementById(id);
                el.classList.remove('step0-hidden');
                el.classList.add('step0-visible');
            });
            document.getElementById('idEbook').style.display = 'none';
            document.getElementById('idEbook00').style.display = 'none';
        }

        function hideStep0Element() {
            // step≠0: idEbook(E-Book 학습관), idEbook00(워크북) 표시
            ['idStep0', 'idStep00'].forEach(id => {
                const el = document.getElementById(id);
                el.classList.remove('step0-visible');
                el.classList.add('step0-hidden');
            });
            document.getElementById('idEbook').style.display = '';
            document.getElementById('idEbook00').style.display = '';
        }

        /* ═══════════════════════════════════════════════════
         * 초기 단계(step) 복원 및 단계 버튼 활성화
         * ═══════════════════════════════════════════════════ */
        let stepp = getStep();
        let step;
        var ckStep = (stepp != null && stepp !== "") ? 1 : 0;

        if (role === '30' || role === '1' || role === '2') {
            // ── 관리자 계정 ──────────────────────────────
            let cl = getClass();

            if ((cl === "" || cl === undefined) && role === '30') {
                if (stepp && stepp.includes("0")) $("#basic0").prop('disabled', false);
                if (stepp && stepp.includes("A")) $("#basic").prop('disabled', false);
                if (stepp && stepp.includes("B")) $("#step1").prop('disabled', false);
                if (stepp && stepp.includes("T")) $("#step2").prop('disabled', false);
            }

            // getClass() 문자열에 포함된 단계별 활성화
            if (cl && cl.includes("0")) $("#basic0").prop('disabled', false);
            if (cl && cl.includes("A")) $("#basic").prop('disabled', false);
            if (cl && cl.includes("B")) $("#step1").prop('disabled', false);
            if (cl && cl.includes("T")) $("#step2").prop('disabled', false);

        } else {
            // ── 일반 학생 계정 ────────────────────────────
            $("#basic0, #basic, #step1").prop('disabled', true);

            if (ckStep === 1) {
                // stepp 값("한자뚝0" / "한자뚝A" / "한자뚝B") 으로 복원
                if (stepp === "한자뚝0") $("#basic0").prop('disabled', false);
                if (stepp === "한자뚝A") $("#basic").prop('disabled', false);
                if (stepp === "한자뚝B") {
                    $("#step1").prop('disabled', false);
                    $("#idImgCover").attr("src", '..\images\ebook_cover\story_cover.jpg');
                }
                stp = stepp;
            } else {
                // stepp 없음  →  getClass() 로 복원
                let cl = getClass();

                if (cl !== "" && cl !== undefined && cl !== null) {
                    // cl 문자열 내 단계 문자를 포함하는지로 활성화
                    if (cl.includes("0")) $("#basic0").prop('disabled', false);
                    if (cl.includes("A")) $("#basic").prop('disabled', false);
                    if (cl.includes("B")) $("#step1").prop('disabled', false);
                    if (cl.includes("T")) $("#step2").prop('disabled', false);

                    disableClass({
                        name: 'cb2',
                        stp: cl
                    });

                    // 첫 번째 활성 단계 버튼 자동 클릭
                    let firstBtn = cl.includes("0") ? document.getElementById("basic0") :
                        cl.includes("A") ? document.getElementById("basic") :
                        cl.includes("B") ? document.getElementById("step1") :
                        null;
                    if (firstBtn) firstBtn.click();
                }
            }

            // localStorage 복원
            if (isKeyExists(user)) {
                stat = JSON.parse(lsGet(user));
                vol = stat['vol'];
                stp = stat['stp'];
            }
            stepp = stp;
        }

        /* ── 복습 영상 표시 조건 ── */
        if (stepp && stepp.slice(-1).toLowerCase() === "b") {
            document.getElementById("idReview").style.display = "inline";
        } else {
            document.getElementById("idReview").style.display =
                (role === '30' || role === '1' || role === '2' || role === '9') ? "inline" : "none";
        }

        setGuest();

        /* ── 단계별 색상 맵 ── */
        stp = stepp ? stepp.slice(-1) : "0";
        var cmap = {
            0: "#5FA63B",
            A: "#FFA63B",
            B: "#5FCAFF",
            T: "#B5B1FC"
        };
        if (stp === "" || stp === undefined) stp = "0";

        /* ── 초기화 실행 ── */
        setInit(stp);
        setGuest();

        /* ═══════════════════════════════════════════════════
         * isKeyExists  –  localStorage 키 존재 여부
         * ═══════════════════════════════════════════════════ */
        function isKeyExists(key) {
            try {
                return localStorage.getItem(key) !== null;
            } catch (e) {
                return false;
            }
        }

        function lsGet(key) {
            try {
                return localStorage.getItem(key);
            } catch (e) {
                return null;
            }
        }

        function lsSet(key, value) {
            try {
                localStorage.setItem(key, value);
            } catch (e) {
                // 차단되거나 용량 초과 시 무시
            }
        }

        /* ═══════════════════════════════════════════════════
         * toggleAnchor(anchorIds, isEnabled)
         * ─────────────────────────────────────────────────
         * 링크 활성/비활성 토글
         *   true  → 정상 표시 (클릭 가능)
         *   false → 반투명 + 그레이스케일 + 클릭 불가
         * ═══════════════════════════════════════════════════ */
        function toggleAnchor(anchorIds, isEnabled) {
            anchorIds.forEach(id => {
                const a = document.querySelector(`#${id}`);
                if (!a) {
                    console.warn(`Anchor #${id} not found`);
                    return;
                }

                a.style.pointerEvents = isEnabled ? 'auto' : 'none';
                a.style.opacity = isEnabled ? '1' : '0.5';
                a.style.cursor = isEnabled ? 'pointer' : 'default';

                const img = a.querySelector('img');
                if (img) img.style.filter = isEnabled ? 'none' : 'grayscale(100%)';
            });
        }

        /* ═══════════════════════════════════════════════════
         * disableClass(b)  –  단계별 게임 링크 활성·비활성
         *   0단계 : 매칭게임만 활성
         *   A / T : 매칭 + 메모리 활성 (파자 비활성)
         *   B     : 전체 활성
         * ═══════════════════════════════════════════════════ */
        function disableClass(b) {
            const s = b.stp || "";
            const gameIds = ['w2_1', 'w2_2', 'w2_3'];

            if (b.name === 'cb2' && s.includes("0")) {
                showStep0Element();
                toggleAnchor(gameIds, true);
                toggleAnchor(['w2_2', 'w2_3'], false);
            } else if (b.name === 'cb2' && (s.includes("A") || s.includes("T"))) {
                hideStep0Element();
                toggleAnchor(gameIds, true);
                toggleAnchor(['w2_3'], false);
            } else {
                toggleAnchor(gameIds, true);
            }
        }

        /* ═══════════════════════════════════════════════════
         * setInit(step)  –  localStorage 복원 후 초기 UI 세팅
         * ═══════════════════════════════════════════════════ */
        function setInit(step) {
            document.querySelector('#idRevtable > td.colr').style.display = 'none';

            if (isKeyExists(user)) {
                stat = JSON.parse(lsGet(user));
                vol = stat['vol'];
                stp = (step !== stat['stp']) ? step : stat['stp'];
            } else {
                stat = {
                    vol: "v1",
                    stp: step
                };
            }

            vol = stat['vol'];
            stp = step;

            changecolor2(cmap[stp]);
            $('#select-field').val(stat['vol']).trigger('change');
            disableClass({
                name: 'cb2',
                stp: stp
            });
            assignHref(Number(stat['vol'].substring(1)));
            assinCoverImage();
            setGuest();
        }

        /* ═══════════════════════════════════════════════════
         * assinGame2  –  게임 링크 href 세팅
         * ═══════════════════════════════════════════════════ */
        function assinGame2(v, s) {
            $("#w2_1").attr('href', `../game/matching-game/index.php?vol=${v}&step=${s}`);
            $("#w2_2").attr('href', `../game/memory-game/index1.html?vol=${v}&clas=${s}`);
            $("#w2_3").attr('href', `../game/paza-game/index.php?vol=${v}&step=${s}`);
        }

        /* ═══════════════════════════════════════════════════
         * assignbook  –  E-Book 링크 세팅
         * ═══════════════════════════════════════════════════ */
        // function assignbook(week) {
        //     const s = stp.toLowerCase();
        //     if (s === "0") {
        //         $("#w1_3").attr('href', '#');
        //     } else if (s === "t") {
        //         $("#w1_3").attr('href',
        //             `https://chaitalkkid.co.kr/ebook_new_chaitalk/ebook_t.html?book=step_${s}.v${week}`);
        //     }
        // }
        function assignbook(week) {
            let stp1 = stp.toLowerCase();
            const ebookUrl = "../ebook/index.php";
            let prt = stp1 == 't' ? 'PRINT_ENABLED=true' : 'PRINT_ENABLED=false';
            setEbookLink("#w0_1", ebookUrl, `?book=local.step_${stp1}_Wbook.v${week}&${prt}`);
            setEbookLink("#w0_2", ebookUrl, `?book=local.step_${stp1}_Sbook.v${week}&${prt}`);
            const bookKey = stp1 == "0"
                ? `?book=local.step_${stp1}_Fchar.v${week}&${prt}`
                : `?book=local.step_${stp1}.v${week}&${prt}`;
            setEbookLink("#w1_3", ebookUrl, bookKey);
        }

        /* ═══════════════════════════════════════════════════
         * setEbookLink  –  E-Book 링크를 sessionStorage 경유로 설정
         *   URL에 book 파라미터가 노출되지 않도록
         *   클릭 시 sessionStorage에 book을 저장한 뒤 페이지 이동
         * ═══════════════════════════════════════════════════ */
        function setEbookLink(selector, page, book) {
            $(selector)
                .attr('href', '#')
                .off('click.ebook')
                .on('click.ebook', function(e) {
                    e.preventDefault();
                    sessionStorage.setItem('ebookBook', book);
                    location.href = page;
                });
        }

        /* ═══════════════════════════════════════════════════
         * assignHref  –  Vol 변경 시 전체 링크 갱신
         * ═══════════════════════════════════════════════════ */
        function assignHref(week) {
            assignbook(week);
            assinGame2(vol, stp);
            $("audio").attr('src', `${stp}.mp3`);
            assinCoverImage();
        }

        /* ═══════════════════════════════════════════════════
         * setVideoLink  –  영상 링크를 sessionStorage 경유로 설정
         *   URL에 src 파라미터가 노출되지 않도록
         *   클릭 시 sessionStorage에 src를 저장한 뒤 페이지 이동
         * ═══════════════════════════════════════════════════ */
        function setVideoLink(selector, page, src) {
            $(selector)
                .attr('href', '#')
                .off('click.video')
                .on('click.video', function(e) {
                    e.preventDefault();
                    sessionStorage.setItem('videoSrc', src);
                    location.href = page;
                });
        }

        /* ═══════════════════════════════════════════════════
         * assinCoverImage  –  커버 이미지 · 영상 링크 세팅
         * ═══════════════════════════════════════════════════ */
        function assinCoverImage() {
            const s = stp.toLowerCase();
            const vv = vol.substring(1);

            // 영상 링크 (URL 파라미터 노출 방지: sessionStorage 경유)
            setVideoLink("#w1_1",   "videoplay.html",  `${s}${vv}/1.mp4`);
            setVideoLink("#w1_2",   "videoplay2.html", `${s}${vv}/2.mp4`);
            setVideoLink("#w1_3_1", "videoplay2.html", `${s}${vv}/3.mp4`);

            // E-Book · 복습 영상
            assignbook(vv);
            if (s === "b")
                setVideoLink("#idReview", "videoplay2.html", `review_${s}/${s}${vv}_study_review.mp4`);

            // 커버 이미지 (단계 버튼을 직접 클릭한 경우에만)
            if (colorClicked)
                $("#idImgCover").attr("src", `../assets/portal_image/${s}${vv}.jpg`);

            // E-Book 아이콘
            $("#idEbook").attr("src", (s === "") ? "images/0.png" : `images/${s}.png`);

            // 학생 계정이면 E-Book 비활성
            if (role == 0 || role == 30) {
                document.getElementById('w1_3').classList.add('disabled');
                document.getElementById('idEbook').classList.add('disabled');
            } else {
                document.getElementById('w1_3').classList.remove('disabled');
                document.getElementById('idEbook').classList.remove('disabled');
            }
            const images = document.getElementsByClassName("ebook-img");
            const sources = stp === "T"
            ? ["images/t1.png", "images/t2.png", "images/t3.png"]
            : stp === "0"
                ? ["images/mo1.jpg", "images/mo2.jpg", "images/mo0.jpg"]
                : ["images/mo1.jpg", "images/mo2.jpg", "images/mo3.jpg"];

            sources.forEach((src, i) => images[i].src = src);
        }

        /* ═══════════════════════════════════════════════════
         * select2 초기화
         * ═══════════════════════════════════════════════════ */
        $(document).ready(function() {
            $("#select-field").select2({
                theme: "bootstrap-5",
                selectionCssClass: "select2--x-small",
                dropdownCssClass: "select2--x-small",
                minimumResultsForSearch: -1
            });
        });

        /* ═══════════════════════════════════════════════════
         * changecolor(e)  –  교재 단계 버튼 클릭 핸들러
         * ─────────────────────────────────────────────────
         *   색상 변경 → step 결정 → 영상·게임·E-Book 링크 갱신
         *   → changeOption() 로 월필터 적용 → setGuest()
         * ═══════════════════════════════════════════════════ */
        function changecolor(e) {
            colorClicked = true;
            const currentVol = $('#select-field').val(); // 클릭 중 Vol 값 보존

            // 색상 적용
            $("[id^='idangle']").css("color", e.value);
            $(".select2-selection").css("background-color", e.value);
            $(".input-group").css("background-color", e.value);
            $("[id^='idselect']").css("background-color", e.value);
            $(".colr").css("background-color", e.value);

            // 클릭된 버튼  →  step 결정
            let step = "A";
            if (e.id === "step1") step = "B";
            if (e.id === "step2") step = "T";
            if (e.id === "basic0") step = "0";
            stp = step;

            vol = currentVol || vol;
            assinGame2(vol, stp);
            document.querySelector('#idstudyHead th').style.fontWeight = 'bold';

            // step0  →  워크북·E-Book 표시 / 아니면 숨김
            disableClass({
                name: 'cb2',
                stp: stp
            });
            if (step === "0") {
                showStep0Element();
                $("#w1_3_1").find("img").attr("src", 'images/mo0.jpg');
            } else {
                hideStep0Element();
                $("#w1_3_1").find("img").attr("src", 'images/mo3.jpg');
            }

            // B단계일 때 복습 영상 행 표시
            document.querySelector('#idRevtable > td.colr').style.display =
                (step === "B") ? '' : 'none';

            // 커버 이미지 · E-Book 아이콘 갱신
            $("#idImgCover").attr("src", `../assets/portal_image/${step}${vol[1]}.jpg`);
            $("#idEbook").attr("src", (step === "") ? "images/0.png" : `images/${step}.png`);

            if (role == 0 || role == 30) {
                document.getElementById('w1_3').classList.add('disabled');
                document.getElementById('idEbook').classList.add('disabled');
            } else {
                document.getElementById('w1_3').classList.remove('disabled');
                document.getElementById('idEbook').classList.remove('disabled');
            }

            // Vol 값 복원 · select2 갱신
            $('#select-field').val(currentVol);
            $('#select-field').trigger('change.select2');

            // 영상 이미지 갱신  (T단계 별도 이미지)
            const images = document.getElementsByClassName("ebook-img");
            const sources = step === "T"
            ? ["images/it1.png", "images/t2.png", "images/t3.png"]
            : step === "0"
                ? ["images/mo1.jpg", "images/mo2.jpg", "images/mo0.jpg"]
                : ["images/mo1.jpg", "images/mo2.jpg", "images/mo3.jpg"];

            sources.forEach((src, i) => images[i].src = src);


            assinCoverImage();

            // 현재 Vol 텍스트에서 숫자 추출  →  assignHref
            const sel = document.getElementById("select-field");
            let selText = (sel && sel.selectedIndex >= 0 && sel.options[sel.selectedIndex].text.trim() !== "") ?
                sel.options[sel.selectedIndex].text : "v1";
            assignHref(Number(selText.replace("v", "")));

            // ■ 월별 필터 적용  (이번달 / 전달만 활성화)
            changeOption(stp);
            setGuest(); // guest 제한을 마지막으로
        }

        /* ═══════════════════════════════════════════════════
         * changecolor2(value)  –  색상만 적용 (초기화용)
         * ═══════════════════════════════════════════════════ */
        function changecolor2(value) {
            $("[id^='idangle']").css("color", value);
            $(".select2-selection").css("background-color", value);
            $(".input-group").css("background-color", value);
            $("[id^='idselect']").css("background-color", value);
            $(".colr").css("background-color", value);
        }

        /* ═══════════════════════════════════════════════════
         * select2:select  –  Vol 드롭다운 선택 이벤트
         * ═══════════════════════════════════════════════════ */
        $('#select-field').on('select2:select', function(e) {
            vol = e.params.data.id;
            assignHref(Number(vol.substring(1)));
            assinGame2(vol, stp);
            assinCoverImage();
        });

        /* ═══════════════════════════════════════════════════
         * changeOption(stp)
         * ─────────────────────────────────────────────────
         * 단계에 따라 옵션 목록을 교체하고 월별 필터를 적용.
         *
         * 처리 순서 :
         *   ① stp  →  optionsData1 또는 optionsData2 선택
         *   ② applyMonthFilter()  →  이번달·전달만 활성화
         *   ③ <select> 옵션 다시 렌더링
         *   ④ 이전 선택값 복원 가능하면 복원, 아니면 첫 활성 옵션
         *   ⑤ setGuest()  →  guest 제한 덮어쓰기
         * ═══════════════════════════════════════════════════ */
        function changeOption(stp) {
            const selectEl = document.getElementById('select-field');
            const currentVal = selectEl.value;

            // ① 단계별 옵션 배열 선택
            let optionsData = (stp === "T") ? optionsData2 : optionsData1;

            // ② 월별 필터  –  이번달·전달만 활성화
            // if (stp !== "T" && user !== "chaitalk") applyMonthFilter(optionsData);

            // ③ 옵션 목록 초기화 후 다시 렌더링
            selectEl.innerHTML = '';
            optionsData.forEach(opt => {
                const o = document.createElement('option');
                o.value = opt.value;
                o.textContent = opt.text;
                o.disabled = opt.disabled;
                selectEl.appendChild(o);
            });

            // ④ 이전 값 복원 가능하면 복원, 아니면 첫 활성 옵션으로
            const canRestore = optionsData.some(opt => opt.value === currentVal && !opt.disabled);
            if (canRestore) {
                selectEl.value = currentVal;
            } else {
                const first = optionsData.find(opt => !opt.disabled);
                if (first) selectEl.value = first.value;
            }

            // ⑤ select2 갱신 후 guest 제한 덮어쓰기
            $('#select-field').trigger('change.select2');
            setGuest();
        }

        /* ═══════════════════════════════════════════════════
         * 오디오 플레이어 이벤트
         * ═══════════════════════════════════════════════════ */
        const audio = document.getElementById('audio');
        const playBtn = document.getElementById('playBtn');
        const volumeBtn = document.getElementById('volumeBtn');

        playBtn.addEventListener('click', () => {
            if (audio.paused) {
                audio.play();
                playBtn.textContent = '⏸';
            } else {
                audio.pause();
                playBtn.textContent = '▶';
            }
        });
        volumeBtn.addEventListener('click', () => {
            audio.muted = !audio.muted;
            volumeBtn.textContent = audio.muted ? '🔇' : '🔊';
        });

        /* ═══════════════════════════════════════════════════
         * 페이지 종료 시  →  현재 상태를 localStorage에 저장
         * ═══════════════════════════════════════════════════ */
        window.addEventListener('beforeunload', function() {
            lsSet(user, JSON.stringify({
                vol: vol,
                stp: stp
            }));
        });

        /* ═══════════════════════════════════════════════════
         * Bootstrap tooltip 초기화
         * ═══════════════════════════════════════════════════ */
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });

        const API_URL = 'api.php';

        /* ═══════════════════════════════════════════════════
         * 클릭 카운트 배지 + DB 학습 기록 저장
         * - 초기 로딩: DB에서 이번 달 카운트 조회 후 배지 표시
         * - 클릭 시: 배지 즉시 증가 + DB INSERT
         * ═══════════════════════════════════════════════════ */
        (function() {
            const ITEMS = [
                { id: 'w0_1',   badgeId: 'idEbook00Badge' },
                { id: 'w0_2',   badgeId: 'idEbook0Badge'  },
                { id: 'w1_1',   badgeId: 'idW11Badge'     },
                { id: 'w1_2',   badgeId: 'idW12Badge'     },
                { id: 'w1_3',   badgeId: 'idEbookBadge'   },
                { id: 'w1_3_1', badgeId: 'idW131Badge'    },
                { id: 'w2_1',   badgeId: 'idW21Badge'     },
                { id: 'w2_2',   badgeId: 'idW22Badge'     },
                { id: 'w2_3',   badgeId: 'idW23Badge'     },
            ];

            const counts = {};

            function setBadge(badgeId, cnt) {
                const el = document.getElementById(badgeId);
                if (el) el.textContent = cnt;
            }

            function saveRecord(uid) {
                fetch(API_URL + '?mode=record', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: user, step: stp, volume: vol, uid: uid })
                }).catch(function() {});
            }

            // 초기 로딩: 이번 달 카운트를 DB에서 조회
            fetch(API_URL + '?mode=record_count&id=' + encodeURIComponent(user))
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.status === 'success') {
                        ITEMS.forEach(function(item) {
                            counts[item.id] = res.data[item.id] || 0;
                            setBadge(item.badgeId, counts[item.id]);
                        });
                    }
                })
                .catch(function() {});

            // 클릭 핸들러: 배지 즉시 증가 + DB 기록
            ITEMS.forEach(function(item) {
                document.getElementById(item.id)?.addEventListener('click', function() {
                    counts[item.id] = (counts[item.id] || 0) + 1;
                    setBadge(item.badgeId, counts[item.id]);
                    saveRecord(item.id);
                });
            });
        })();

        document.addEventListener('DOMContentLoaded', async function() {

            changeOption(stp);
            const selectEl = document.getElementById('select-field');
            const { status, activeMonths } = await getUserSettings(user);
            let optionsData = (stp === "T") ? optionsData2 : optionsData1;
            if (status === 'all') {
                if (user == "chaitalk") {
                    for (var i = 0; i <selectEl.options.length; i++) {
                        selectEl.options[i].disabled = false;
                    }
                }
                else {
                    let optData = (stp === "T") ? optionsData2 : optionsData1;
                    applyMonthFilter(optData);
                    selectEl.innerHTML = '';
                    optData.forEach(opt => {
                        const o       = document.createElement('option');
                        o.value       = opt.value;
                        o.textContent = opt.text;
                        o.disabled    = opt.disabled;
                        selectEl.appendChild(o);
                    });
                }
            }
            else if (status === 'partial') {
                // 현재 월이 activeMonths에 포함되면 제한 없이 전체 활성화
                const currentMonth = (new Date().getMonth() + 1).toString();
                if (activeMonths.includes(currentMonth)) {
                    for (let i = 0; i < selectEl.options.length; i++) {
                        selectEl.options[i].disabled = false;
                    }
                } else {
                    // activeMonths ['7','10'] → 'v7','v10'만 활성화, 나머지 비활성화
                    const activeValues = activeMonths.map(m => 'v' + m);
                    optionsData.forEach(opt => {
                        opt.disabled = !activeValues.includes(opt.value);
                    });
                    // 옵션 목록 초기화 후 다시 렌더링
                    selectEl.innerHTML = '';
                    optionsData.forEach(opt => {
                        const o        = document.createElement('option');
                        o.value        = opt.value;
                        o.textContent  = opt.text;
                        o.disabled     = opt.disabled;
                        selectEl.appendChild(o);
                    });
                }
            }
            else if (status === 'none') {
                // 모든 월 비활성화
                //const selectEl = document.getElementById('select-field');
                for (let i = 0; i < selectEl.options.length; i++) {
                    selectEl.options[i].disabled = true;
                }
                $('#select-field').trigger('change.select2');
            }

        });
        async function getUserSettings(id) {
            try {
                const response = await fetch(`${API_URL}?mode=get&id=${id}`);
                const result = await response.json();

                if (result.status === 'success') {
                    return {
                        status: result.data.status,
                        // "1,2,5" 형태의 문자열을 ["1", "2", "5"] 배열로 변환
                        activeMonths: result.data.mon ? result.data.mon.split(',') : []
                    };
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error("데이터 조회 중 오류 발생:", error);
                return {
                    status: 'all',
                    activeMonths: []
                }; // 에러 시 기본값 반환
            }
        }
    </script>

</body>

</html>