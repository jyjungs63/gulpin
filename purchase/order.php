<!DOCTYPE html>
<html lang="utf-8">
<?php include "../libs/header.php"; ?>

<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="google" content="notranslate">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/brands.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css"
        type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- <link href="../common.css" rel="stylesheet"> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/alasql/4.5.0/alasql.min.js"></script>
    <link rel="stylesheet" href="order.css">
    <link rel="stylesheet" href="../role-nav.css">
    <script src="../js/roles.js?v=<?= filemtime(__DIR__ . '/../js/roles.js') ?>"></script>
    <script src="../js/role-nav.js" defer></script>
    <style>
    </style>
</head>

<body class="kgarden-page">
    <header id="roleNav"></header>
    <div class="kgarden-shell">
        <section class="kgarden-heading">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 id="idOrdertext">Purchase</h5>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:window.history.back();">이전</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="kgarden-card" id="idCardPurchase">
                        <div class="kgarden-toolbar">
                            <ul class="kgarden-tabs" id="custom-tabs-one-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-one-home-tab"
                                        href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                        aria-selected="true">주문</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-profile-tab"
                                        href="#custom-tabs-one-profile" role="tab"
                                        aria-controls="custom-tabs-one-profile" aria-selected="false">주문내역</a>
                                </li>
                                <li class="nav-item" id="idHomeOrderTabItem" style="display:none">
                                    <a class="nav-link" id="custom-tabs-one-homeorder-tab"
                                        href="#custom-tabs-one-homeorder" role="tab"
                                        aria-controls="custom-tabs-one-homeorder" aria-selected="false">방문수업 주문내역</a>
                                </li>
                            </ul>
                        </div>
                        <div class="kgarden-body" id="cardMain">
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                                <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-home-tab">

                                    <div class="kgarden-form">
                                        <div class="kgarden-field" id="idPriceGroupField" style="display:none">
                                            <label for="idPriceGroup">가격표</label>
                                            <select id="idPriceGroup" onchange="showPriceGroup(this.value)"></select>
                                        </div>
                                        <div class="kgarden-field">
                                            <label for="idGrade">필터</label>
                                            <select id="idGrade" data-placeholder="Choose Items">
                                                <option value="va">전체</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="idHomeOrderPanel" style="display:none">
                                        <h5 class="kgarden-section-title">방문수업 교재 신청</h5>
                                        <div class="kgarden-form">
                                            <div class="kgarden-field">
                                                <label for="idHomeOrderMonth">신청월</label>
                                                <input type="month" id="idHomeOrderMonth" onchange="listHomeOrders()">
                                            </div>
                                            <div class="kgarden-field home-order-admin" style="display:none">
                                                <label for="idHomeOrderBranch">지사</label>
                                                <select id="idHomeOrderBranch" onchange="fillHomeOrderTeachers(); renderHomeOrders()"></select>
                                            </div>
                                            <div class="kgarden-field">
                                                <label for="idHomeOrderTeacher">교사</label>
                                                <select id="idHomeOrderTeacher" onchange="renderHomeOrders()"></select>
                                            </div>
                                            <button class="btn btn-outline-primary kgarden-btn home-order-manage" type="button" onclick="loadHomeOrders()">장바구니 전부담기</button>
                                            <button class="btn btn-outline-success kgarden-btn home-order-manage" type="button" onclick="completeHomeOrders()">선택 완료 처리</button>
                                            <button class="btn btn-outline-secondary kgarden-btn" type="button" id="idHomeOrderDoneToggle" onclick="toggleDoneHomeOrders()">완료 보기</button>
                                            <span id="idHomeOrderSummary"></span>
                                        </div>
                                        <div class="kgarden-table-wrap">
                                            <table class="table table-sm mb-0">
                                                <thead><tr><th class="home-order-manage"><input type="checkbox" id="idHomeOrderAll" aria-label="대기 주문 전체 선택"></th><th class="home-order-admin" style="display:none">지사</th><th>주문일</th><th>교사</th><th>학생</th><th>교재명</th><th>주문상태</th><th>주문완료일</th></tr></thead>
                                                <tbody id="idHomeOrderRows"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <h5 class="kgarden-section-title">주문할 상품 선택</h5>
                                    <div class="kgarden-table-wrap">
                                        <div id="idTable">

                                        </div>
                                    </div>

                                    <div class="kgarden-actions">
                                        <a id="anchorRead" href="javascript:orderBook()"
                                            class="btn btn-info kgarden-btn" role="button"
                                            data-toggle="tooltip" title="Add to Cart " aria-disabled="true"><i
                                                class="fa-solid fa-cart-shopping"></i> 장바구니</a>&nbsp;&nbsp;
                                    </div>
                                    <h5 class="kgarden-section-title">주문한 상품 확정</h5>
                                    <div class="kgarden-table-wrap">
                                        <div id="idTableConfirm">

                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                    <div id="idOrderForm" class="kgarden-form">
                                        <div class="kgarden-field">
                                            <label for="idCbmOrderSearch">상태</label>
                                            <select id="idCbmOrderSearch" data-placeholder="Choose Items"><option value="NULL">전품목</option><option value="0">물품준비중</option><option value="3">결제확인중</option><option value="2">입금완료</option><option value="10">배송중</option><option value="1">배송완료</option><option value="99">환불처리</option><option value="999">환불완료</option></select>
                                        </div>
                                        <div class="kgarden-field">
                                            <label for="idYearFilter">년도</label>
                                            <select id="idYearFilter"><option value="ALL">전체년도</option></select>
                                        </div>
                                        <div class="kgarden-field">
                                            <label for="idPorList">주문서</label>
                                            <select id="idPorList" data-placeholder="주문서선택"></select>
                                        </div>
                                        <div class="kgarden-field">
                                            <label for="idPorBranch">지사별조회</label>
                                            <select id="idPorBranch" data-placeholder="지사선택"></select>
                                        </div>
                                        <div class="kgarden-field">
                                            <label for="monthPicker">시작월</label>
                                            <input type="month" id="monthPicker" name="month">
                                        </div>
                                        <div class="kgarden-field">
                                            <label for="monthPickerEnd">종료월</label>
                                            <input type="month" id="monthPickerEnd" name="monthEnd">
                                        </div>
                                        <div id="idParcelDiv" class="kgarden-field">
                                            <label for="idParcel">택배비</label>
                                            <input id="idParcel" type="text" placeholder="택배비">
                                        </div>
                                        <button id="idBtParcel" class="btn btn-outline-success kgarden-btn disabled" type="button" onclick="AddParcel()">택배비추가</button>
                                        <div class="kgarden-field">
                                            <label for="idInvoieNo">송장번호</label>
                                            <input id="idInvoieNo" type="text" placeholder="로젠택배 송장번호">
                                        </div>
                                        <div class="kgarden-field">
                                            <label for="idRefund">환불비</label>
                                            <input id="idRefund" type="text" placeholder="환불비">
                                        </div>
                                        <div class="kgarden-field">
                                            <label for="idOrderStatus">주문상태</label>
                                            <select id="idOrderStatus" data-placeholder="Choose Items"><option value="0">물품준비중</option><option value="3">결제확인중</option><option value="2">입금완료</option><option value="10">배송중</option><option value="1">배송완료</option><option value="99">환불처리</option><option value="999">환불완료</option></select>
                                        </div>
                                        <button id="idBtDelever" class="btn btn-outline-primary kgarden-btn" type="button" onclick="AddDelever()" disabled>주문처리</button>
                                        <button id="idPorPay" class="btn btn-outline-primary kgarden-btn" type="button" hidden>카드결제</button>
                                        <button id="idPorRefund" class="btn btn-outline-danger kgarden-btn" type="button" hidden>카드 전액 환불</button>
                                        <a id="idPorReceipt" class="btn btn-outline-secondary kgarden-btn" target="_blank" rel="noopener noreferrer" hidden>영수증</a>
                                        <button id="idBtExportPdf" class="btn btn-outline-danger kgarden-btn" type="button" onclick="exportPorTablePdf()"><i class="fa-solid fa-file-pdf"></i> PDF</button>
                                    </div>

                                    <div id="porTableDiv"></div>
                                    <table id="porTable">
                                        <thead>
                                            <tr>
                                                <th class="col1"><a
                                                        href="javascript:globalsort('지사/유치원명')">지사/유치원명<i
                                                            class="fa-solid fa-sort"></i></a></th>
                                                <th class="col1"><a
                                                        href="javascript:globalsort('날짜')">날짜<i
                                                            class="fa-solid fa-sort"></i></a></th>
                                                <th class="col2">내역</th>
                                                <th class="col3">금액/환불액</th>
                                                <th class="col4">배송주소</th>
                                                <th class="col5"><a href="javascript:globalsort('주문상태')">주문상태<i
                                                            class="fa-solid fa-sort"></i></a></th>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- 본사: 홈스쿨 교재 신청 (idHomeOrderPanel 을 여기로 옮긴다) -->
                                <div class="tab-pane fade" id="custom-tabs-one-homeorder" role="tabpanel" aria-labelledby="custom-tabs-one-homeorder-tab"></div>
                            </div>
                        </div>
                    </div>
        </section>
    </div>
    <div class="kgarden-shell" id="idSecDiv">
        <section>
            <div class="kgarden-card">
                <div class="kgarden-toolbar" id="idCardAddress">
                    <h5 class="kgarden-section-title">배송지 확정</h5>
                    <div class="kgarden-form">
                        <div class="kgarden-field">
                            <label for="idName">지사명</label>
                            <input id="idName" type="text" placeholder="지사명">
                        </div>
                        <div class="kgarden-field">
                            <label for="idOwner">배송지명</label>
                            <input id="idOwner" type="text" placeholder="배송지명">
                        </div>
                        <div class="kgarden-field">
                            <label for="idMobile">전화</label>
                            <input id="idMobile" type="text" placeholder="전화">
                        </div>
                        <div class="kgarden-field kgarden-field-wide">
                            <label for="idAddr">배송지주소</label>
                            <input id="idAddr" type="text" placeholder="배송지주소">
                        </div>
                        <div class="kgarden-field">
                            <label for="idZip">우편번호</label>
                            <input id="idZip" type="text" placeholder="우편번호">
                        </div>
                        <button class="btn btn-outline-primary kgarden-btn" type="button" onclick="execDaumPostcode('idZip','idAddr')">주소찾기</button>
                        <button class="btn btn-outline-success kgarden-btn" type="button" onclick="AddBranch()">배송지추가</button>
                    </div>
                </div>
                <div class="kgarden-body" id="cardDest">
                    <h5 class="kgarden-section-title">배송지 선택</h5>
                    <div class="kgarden-table-wrap">
                        <div id="idTableDest"></div>
                    </div>
                </div>
            </div>

            <div class="kgarden-card" id="idCardPDF">
                <div class="kgarden-toolbar">
                    <h5 class="kgarden-section-title">구매내역서</h5>
                    <div class="kgarden-actions">
                        <a id="idConfirmOrder" href="javascript:orderPrint()" class="btn btn-warning kgarden-btn disabled"
                            role="button" data-toggle="tooltip" title="구매내역서확인" aria-disabled="true"><i
                                class="fa-solid fa-cart-shopping"></i> 구매내역서확인</a>
                        <a id="idModifyOrder" href="javascript:orderModify()" class="btn btn-warning kgarden-btn disabled"
                            role="button" data-toggle="tooltip" title="수정" aria-disabled="true">수정(재작성)</a>
                        <a id="idOKOrder" href="javascript:orderOK()" class="btn btn-warning kgarden-btn disabled"
                            role="button" data-toggle="tooltip" title="구매완료" aria-disabled="true">구매완료</a>
                        <a id="idPayOrder" href="javascript:payOrder()" class="btn btn-primary kgarden-btn disabled"
                            role="button" title="카드결제" aria-disabled="true">카드결제</a>
                    </div>
                </div>
                <!-- 주문 전 확인·동의 (전자상거래법 제7조·제13조, 개인정보보호법 제15조) — 서버 SUploadBoardPDF 에서도 확인 -->
                <div class="kgarden-body" id="idOrderAgree">
                    <p class="mb-1">결제 금액(택배비 포함)과 배송 정보를 확인해 주세요. 교재 합계 100,000원 미만은 택배비 4,500원이 추가됩니다.
                        구매내역서 확인 후 카드결제 또는 계좌 입금으로 결제할 수 있습니다. 상품을 받은 날부터 7일 이내 청약철회할 수 있으며, 자세한 내용은
                        <a href="../policy/refund.php" target="_blank">교환·반품·환불 안내</a>를 확인해 주세요.</p>
                    <label class="d-block mb-1"><input type="checkbox" id="idAgreeOrder"> [필수] 주문 내용과 결제 금액을 확인했으며
                        <a href="../policy/terms.php" target="_blank">이용약관</a>·<a href="../policy/refund.php" target="_blank">환불 안내</a>에 동의합니다.</label>
                    <label class="d-block mb-0"><input type="checkbox" id="idAgreePrivacy"> [필수] 배송을 위한 개인정보(배송지명, 전화, 주소) 수집·이용에 동의합니다.
                        (목적: 배송·주문 관리, 보유기간: 5년 — 전자상거래법, <a href="../policy/privacy.php" target="_blank">개인정보처리방침</a>)</label>
                </div>
                <div class="kgarden-body" id="cardPDF">
                    <iframe id="pdfDiv"></iframe>
                </div>
            </div>
        </section>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script> -->
    <script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/luxon/3.4.4/luxon.min.js"></script>
    <!-- <script src="listprice2.js"></script> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script src="https://unpkg.com/@pdf-lib/fontkit@0.0.4/dist/fontkit.umd.min.js"></script>
    <script src="https://unpkg.com/downloadjs@1.4.7"></script>
    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>
    <!-- <script type="text/javascript" src="https://raw.githack.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js">
    </script> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script src="../js/common.js"></script>
    <script src="https://js.tosspayments.com/v2/standard"></script>
    <script src="../js/payment.js?v=2"></script>
    <script src="../js/header.js"></script>
    <script src="../js/libpdf.js"></script>

    <script src="order.js?v=2"></script>
    <script src="acount.js"></script>

    <script>
        let listprice2 = [];
        let priceGroups = {};   // 지사 그룹별 가격표 (가격표.xlsx → Server/price_catalog.json)
        var porId = "";


        if (user == "chaitalk")
            user = "admin";
        if (user == 'admin')
            $("#idBtDelever").css('visibility', 'visible');
        else
            $("#idBtDelever").css('visibility', 'hidden');

        // 선택한 그룹의 가격표를 주문 테이블에 표시. uid = 교재 코드 (주문내역 → 학습 열람 권한에 사용)
        function showPriceGroup(key) {
            const group = priceGroups[key];
            listprice2 = (group ? group.items : []).map(item => ({
                uid: item.code,
                grade: GP_ROLE.courseLabel(item.family),
                title: item.title,
                price: item.price,
            }));
            const families = [...new Set(listprice2.map(item => item.grade))];
            document.getElementById("idGrade").replaceChildren(new Option("전체", "va"), ...families.map(family => new Option(family, family)));
            table.setData(listprice2);
        }

        // 서버는 로그인한 지사 그룹의 가격표만 준다 (본사는 전체 → 가격표 선택 표시)
        function updateTableData(response) {
            priceGroups = (response.success && response.success.groups) || {};
            const keys = Object.keys(priceGroups);
            document.getElementById("idPriceGroup").replaceChildren(...keys.map(key => new Option(priceGroups[key].name, key)));
            document.getElementById("idPriceGroupField").style.display = keys.length > 1 ? "" : "none";
            if (keys.length === 0) CallToast("주문할 수 있는 가격표가 없습니다.", "error");
            showPriceGroup(keys[0]);
        }

        document.addEventListener('DOMContentLoaded', () => {
            var options = {
                functionName: "SGetPriceCatalog",
                otherData: {},
            };
            CallAjax("SMethods.php", "POST", options, updateTableData, (e) => {
                CallToast((e && e.responseJSON && e.responseJSON.error) || "가격표 조회 실패!!", "error");
            });
            if (Number(role) === 3 || homeOrderAdmin) {   // 홈스쿨 지사장: 소속 선생님 교재 신청 / 본사: 전체 조회(지사·선생님별)
                const homeOrderPanel = document.getElementById("idHomeOrderPanel");
                homeOrderPanel.style.display = "";
                // 본사는 주문 탭이 숨겨지므로(order.js) '홈스쿨 주문내역' 탭으로 따로 보여준다
                if (homeOrderAdmin) {
                    document.getElementById("custom-tabs-one-homeorder").appendChild(homeOrderPanel);
                    document.getElementById("idHomeOrderTabItem").style.display = "";
                }
                document.querySelectorAll(".home-order-admin").forEach(el => { el.style.display = homeOrderAdmin ? "" : "none"; });
                document.querySelectorAll(".home-order-manage").forEach(el => { el.style.display = homeOrderAdmin ? "none" : ""; });
                document.getElementById("idHomeOrderMonth").value = moment().format("YYYY-MM");
                listHomeOrders();
            }
        });

        // 홈스쿨 교재 신청 (gulpin_home_order_stat, Server/homeschool_policy.php) — status null 대기, 1 완료
        let homeOrders = [];
        const homeOrderAdmin = GP_ROLE.isAdmin(Number(role));   // 본사는 조회만 (불러오기·완료 처리는 지사장)
        let showDoneHomeOrders = false;   // 완료 신청은 기본 숨김, '완료 보기'를 눌렀을 때만 표시
        let homeOrderIds = [];   // 장바구니로 불러온 신청 id → 구매내역서 저장(SUploadBoardPDF) 때 완료 처리

        // 장바구니(주문한 상품 확정) 수량 = 지사장 직접 선택(manualCartItems) + 불러온 교재 신청(homeCartItems)
        //   uid => {uid, grade, title, price, count}
        let manualCartItems = {};
        let homeCartItems = {};
        let rebuildingCart = false;

        function rebuildCart() {
            const lines = {};
            [manualCartItems, homeCartItems].forEach(source => Object.values(source).forEach(item => {
                const line = lines[item.uid] || (lines[item.uid] = { ...item, count: 0 });
                line.count += Number(item.count);
            }));
            rebuildingCart = true;
            table1.clearData();
            Object.values(lines).filter(line => line.count > 0).forEach(line => {
                table1.addRow({ ...line, count: String(line.count), total: line.count * Number(line.price) });
            });
            rebuildingCart = false;
            table1.selectRow();
            $("#idTableConfirm > div.tabulator-footer > div.tabulator-calcs-holder > div > div:nth-child(1)").html("합계");
        }

        // 장바구니에서 삭제하면 두 출처 모두에서 뺀다 (불러온 신청은 주문서에 없으면 완료 처리되지 않음)
        table1.on("rowDeleted", row => {
            if (rebuildingCart) return;
            const uid = row.getData().uid;
            delete manualCartItems[uid];
            delete homeCartItems[uid];
        });
        // 장바구니 수량을 직접 고치면 불러온 신청 수량을 뺀 나머지를 직접 선택 수량으로 본다
        table1.on("cellEdited", cell => {
            if (cell.getField() !== "count") return;
            const data = cell.getRow().getData();
            const homeCount = homeCartItems[data.uid] ? homeCartItems[data.uid].count : 0;
            const manualCount = Math.max(0, Number(String(data.count).replace(/,/g, '')) - homeCount);
            if (manualCount > 0) manualCartItems[data.uid] = { uid: data.uid, grade: data.grade, title: data.title, price: data.price, count: manualCount };
            else delete manualCartItems[data.uid];
        });

        function listHomeOrders() {
            const options = { functionName: "SHomeOrderList", otherData: { month: document.getElementById("idHomeOrderMonth").value } };
            CallAjax("SMethods.php", "POST", options, (resp) => {
                homeOrders = resp.success || [];
                if (homeOrderAdmin) fillHomeOrderBranches(); else fillHomeOrderTeachers();
                renderHomeOrders();
            }, (e) => CallToast((e && e.responseJSON && e.responseJSON.error) || "교재 신청 조회 실패!!", "error"));
        }

        // 본사 필터: 조회된 신청의 지사 / (선택 지사의) 선생님 목록. 기존 선택은 남아 있으면 유지
        function fillHomeOrderSelect(select, entries) {
            const current = select.value;
            const unique = new Map(entries);
            select.replaceChildren(new Option("전체", ""), ...[...unique].map(([id, label]) => new Option(`${label} (${id})`, id)));
            select.value = unique.has(current) ? current : "";
        }
        function fillHomeOrderBranches() {
            fillHomeOrderSelect(document.getElementById("idHomeOrderBranch"),
                homeOrders.map(order => [order.branch_id, order.branch_owner || order.branch_name || order.branch_id]));
            fillHomeOrderTeachers();
        }
        function fillHomeOrderTeachers() {
            const branch = document.getElementById("idHomeOrderBranch").value;
            fillHomeOrderSelect(document.getElementById("idHomeOrderTeacher"),
                homeOrders.filter(order => !branch || order.branch_id === branch).map(order => [order.teacher_id, order.teacher_name || order.teacher_id]));
        }

        // 지사(본사만)·선생님 필터를 적용한 신청 목록
        function visibleHomeOrders() {
            const branch = homeOrderAdmin ? document.getElementById("idHomeOrderBranch").value : "";
            const teacher = document.getElementById("idHomeOrderTeacher").value;
            return homeOrders.filter(order => (!branch || order.branch_id === branch) && (!teacher || order.teacher_id === teacher));
        }

        function toggleDoneHomeOrders() {
            showDoneHomeOrders = !showDoneHomeOrders;
            document.getElementById("idHomeOrderDoneToggle").textContent = showDoneHomeOrders ? "완료 숨기기" : "완료 보기";
            renderHomeOrders();
        }

        function renderHomeOrders() {
            const visible = visibleHomeOrders();
            const body = document.getElementById("idHomeOrderRows");
            body.replaceChildren();
            document.getElementById("idHomeOrderAll").checked = false;
            // 선생님별로 묶어 소계 표시 — 지사장은 묶음마다 그 선생님 신청만 불러와 주문서 1건으로 저장
            const groups = new Map();
            visible.forEach(order => {
                if (!groups.has(order.teacher_id)) groups.set(order.teacher_id, []);
                groups.get(order.teacher_id).push(order);
            });
            groups.forEach((orders, teacherId) => {
                const shown = orders.filter(order => showDoneHomeOrders || order.status === null);
                if (!shown.length) return;
                const pendingCount = orders.filter(order => order.status === null).length;
                const head = document.createElement("tr");
                head.style.background = "#f1f5f9";
                const headCell = document.createElement("td");
                headCell.colSpan = 7;
                headCell.style.fontWeight = "600";
                headCell.textContent = `${orders[0].teacher_name || teacherId} 교사 — ${orders.length}건 (대기 ${pendingCount}) `;
                if (!homeOrderAdmin && pendingCount) {
                    const button = document.createElement("button");
                    button.type = "button";
                    button.className = "btn btn-sm btn-outline-primary ml-2";
                    button.textContent = "장바구니 개별담기";
                    button.addEventListener("click", () => {
                        document.getElementById("idHomeOrderTeacher").value = teacherId;
                        renderHomeOrders();
                        loadHomeOrders();
                    });
                    headCell.appendChild(button);
                }
                head.appendChild(headCell);
                body.appendChild(head);
                shown.forEach(order => renderHomeOrderRow(body, order));
            });
            if (!body.children.length) {
                body.innerHTML = visible.length ? '<tr><td colspan="7">대기 중인 교재 신청이 없습니다. 완료 신청은 "완료 보기"를 누르세요.</td></tr>'
                    : '<tr><td colspan="7">해당 월의 교재 신청이 없습니다.</td></tr>';
            }
            const pending = visible.filter(order => order.status === null).length;
            document.getElementById("idHomeOrderSummary").textContent = `${visible.length}건 (대기 ${pending} / 완료 ${visible.length - pending})`;
        }

        function renderHomeOrderRow(body, order) {
            const tr = document.createElement("tr");
            if (!homeOrderAdmin) {
                const check = document.createElement("td");
                if (order.status === null) {
                    const box = document.createElement("input");
                    box.type = "checkbox"; box.value = order.id; box.className = "home-order-check";
                    check.appendChild(box);
                }
                tr.appendChild(check);
            }
            [...(homeOrderAdmin ? [order.branch_owner || order.branch_name || order.branch_id] : []),
                order.order_date, order.teacher_name || order.teacher_id, order.student_name || order.student_id,
                `${GP_ROLE.courseLabel(order.material_title)} (${order.material_code})`, order.status === null ? "대기" : "완료", order.complete_date || ""]
                .forEach(value => {
                    const td = document.createElement("td");
                    td.textContent = value;
                    tr.appendChild(td);
                });
            body.appendChild(tr);
        }

        document.getElementById("idHomeOrderAll").addEventListener("change", function() {
            document.querySelectorAll(".home-order-check").forEach(box => { box.checked = this.checked; });
        });

        // 화면에 보이는(선생님 필터) 대기 신청을 교재 코드별 수량으로 합쳐 장바구니(주문한 상품 확정)에 넣는다
        function loadHomeOrders() {
            const pending = visibleHomeOrders().filter(order => order.status === null);
            if (!pending.length) {
                CallToast("불러올 대기 주문이 없습니다.", "error");
                return;
            }
            const teacherCount = new Set(pending.map(order => order.teacher_id)).size;
            if (teacherCount > 1 && !confirm(`교사 ${teacherCount}명의 신청이 주문서 1건으로 합쳐집니다. 교사별로 나누려면 교사를 선택하세요.\n계속할까요?`)) return;
            const missing = [];
            homeCartItems = {};   // 다시 불러오면 새로 계산 (중복 합산 안 함)
            pending.forEach(order => {
                const item = listprice2.find(book => book.uid === order.material_code);
                if (!item) {
                    if (!missing.includes(order.material_code)) missing.push(order.material_code);
                    return;
                }
                const line = homeCartItems[item.uid] || (homeCartItems[item.uid] = { uid: item.uid, grade: item.grade, title: item.title, price: item.price, count: 0 });
                line.count++;
            });
            homeOrderIds = pending.filter(order => !missing.includes(order.material_code)).map(order => order.id);
            rebuildCart();
            CallToast(`교재 신청 ${homeOrderIds.length}건을 장바구니에 담았습니다.`, "success");
            if (missing.length) CallToast(`가격표에 없는 교재는 제외했습니다: ${missing.join(", ")}`, "error");
        }

        function completeHomeOrders() {
            const ids = [...document.querySelectorAll(".home-order-check:checked")].map(box => box.value);
            if (!ids.length) {
                CallToast("완료 처리할 신청을 선택하세요.", "error");
                return;
            }
            if (!confirm(`선택한 ${ids.length}건을 주문 완료로 처리할까요?`)) return;
            CallAjax("SMethods.php", "POST", { functionName: "SHomeOrderComplete", otherData: { ids } }, (resp) => {
                CallToast(`${resp.success}건을 완료 처리했습니다.`, "success");
                listHomeOrders();
            }, (e) => CallToast((e && e.responseJSON && e.responseJSON.error) || "완료 처리 실패!!", "error"));
        }
        orderToPdf = () => {
            var element = document.getElementById('idTableConfirm');
            var opt = {
                margin: [3, 0.5, 0, 0.5],
                filename: 'myfile.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'cm',
                    format: 'a4',
                    orientation: 'landscape'
                }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(element).save();

            // Old monolithic-style usage:
            //html2pdf(element, opt);
        }

        const rolename = GP_ROLE.orderTitle(role); // js/roles.js
        document.getElementById("idOrdertext").innerHTML = (name + ` ${rolename}/구매`);

        const {
            PDFDocument,
            rgb
        } = PDFLib;

        orderList = (opt) => {
            items = [];
            blist = [];

            dispList = (resp) => {
                $('#idPorList').empty();
                document.getElementById("idInvoieNo").value = "";
                document.getElementById("idRefund").value = "";
                let select = document.getElementById('idPorList');
                let option = document.createElement('option');

                select.add(option);

                // 연도 필터 옵션 동적 생성
                buildYearFilter(resp);

                // 연도 필터 적용
                var yearFilter = document.getElementById("idYearFilter").value;

                resp.forEach(el => {
                    var jarr = {
                        "id": el['id'],
                        "por_id": el['por_id'],
                        "order": el['order'],
                        "addr": el['addr'],
                        "mobile": el['mobile'],
                        "rdate": el['rdate'],
                        "confirm": el['confirm'] == 1 ? "승인" : "미승인",
                    }
                    items.push(jarr);
                    // 연도 필터 체크: por_id가 "P25-..." 형태일 때 yearFilter와 매칭
                    var porYear = el['por_id'].match(/^P(\d{2})/);
                    var porYearVal = porYear ? porYear[1] : "";
                    var passYearFilter = (yearFilter == "ALL" || porYearVal == yearFilter);

                    // Create a new option element
                    option = document.createElement('option');
                    if (opt == null) {
                        if (passYearFilter) {
                            option.text = el['por_id'];
                            option.value = el['por_id'];
                            select.add(option);
                        }
                    } else {
                        if (el['confirm'] == opt && passYearFilter) {
                            option.text = el['por_id'];
                            option.value = el['por_id'];
                            select.add(option);
                        }
                    }

                    if (user == "admin") {
                        // 표시는 지점명·이름 (같거나 비면 생략, 둘 다 없으면 아이디) — 조회에는 data-id(아이디)를 쓴다 (order.js porBranchName)
                        const label = [...new Set([el['bname'], el['uname']].map(v => String(v || '').trim()).filter(Boolean))].join(' ') || el['id'];
                        blist.push([el['id'], label])
                    } else {
                        blist.push([el['id'], el['order']])
                    }

                })
                //const unqueArr = blist.filter((value, index, self) => self.indexOf(value) === index); // 지사명 중복 제거
                const unqueArr = blist.filter((element, index) => {
                    return (
                        blist.findIndex(
                            (item) => item[0] === element[0] && item[1] === element[1]
                        ) === index
                    );
                })
                $('#idPorBranch').empty();
                let select2 = document.getElementById('idPorBranch');
                let option2 = document.createElement('option');
                option2.text = ""; // Set the text of the new option
                option2.value = ""; // Set the value attribute (if needed)
                select2.add(option2);
                if (user == "admin") {
                    option2 = document.createElement('option');
                    option2.text = "전지사"; // Set the text of the new option
                    option2.value = "admin"; // Set the value attribute (if needed)
                    select2.add(option2);
                } else {
                    option2 = document.createElement('option');
                    option2.text = "전유치원"; // Set the text of the new option
                    option2.value = user; // Set the value attribute (if needed)
                    $("#idLbParcel").hide();
                    $("#idParcelDiv").hide();
                    $("#idBtParcel").hide();

                    //$("#idRefund").hide();
                    $("#idLbRefundidLbRefund").hide();

                    $("#idSpfind").text("유치원별조회");
                    select2.add(option2);
                }
                for (let i = 0; i < unqueArr.length; i++) {
                    option2 = document.createElement('option');
                    if (user == 'admin') {
                        option2.value = 'admin'; // 지사명
                        option2.dataset.id = unqueArr[i][0]; // 조회용 지사 아이디
                    } else
                        option2.value = unqueArr[i][0]; // 지사명
                    option2.text = unqueArr[i][1]; // 유치원명
                    select2.add(option2);
                }

            }

            dispErr = (error) => {
                CallToast('SShowOrderList !', "error")
            }

            var options = {
                functionName: 'SShowOrderList',
                otherData: {
                    id: user,
                }
            };

            CallAjax("SMethods.php", "POST", options, dispList, dispErr);

            var deleteIcon = function(cell, formatterParams) { //plain text value
                return "<i class='fa fa-trash'></i>";
            };
        }


        table2.on("rowClick", function(e, row) { /// 배달 주소지 클릭시
            //e - the click event object
            var selectedRows = table2.getSelectedData();
            selectedRows.forEach(function(row) {
                //table2.deselectRow(row.id);
            });

            // table2.deselectRow();
            var ar = row._row.data['addr'].split('우(');
            $("#idName").val(row._row.data['name']);
            $("#idOwner").val(row._row.data['owner']);
            $("#idMobile").val(row._row.data['mobile']);
            if (ar.length == 2) {
                $("#idAddr").val(ar[0]);
                $("#idZip").val(ar[1].slice(0, ar[1].length - 1));
            } else {
                $("#idAddr").val(row._row.data['addr']);
                $("#idZip").val(row._row.data['zipcode']);
            }

            //table2.selectRow(Number(row._row.position));

            $("#idConfirmOrder").removeClass('disabled');
            //table2.selectRow();
            //alert(Number(row._row.position));
        });

        AddBranch = () => {

            var selectElement = document.getElementById("idGrade"); // 지사 또는 원관리
            var selectedValue = selectElement.value;

            var id = $("#idName").val(); // 아이디
            var name = $("#idName").val(); // 이름
            var owner = $("#idOwner").val(); // 지사명
            var password = $("#idPasswd").val();
            var mobile = $("#idMobile").val();
            var addr = $("#idAddr").val();
            var zipcode = $("#idZip").val();
            var role = 2; // 1 branch manager , 2 // teacher
            var rdate = "";

            const formattedDate = formatDate();
            if (rdate == undefined || rdate == "") rdate = formattedDate;

            var items = {
                id: name,
                name: name,
                owner: owner,
                password: "",
                mobile: mobile,
                addr: addr,
                zipcode: zipcode,
                mid: user,
                role: role,
                rdate: rdate,
                confirm: 0,
            }

            var data = {
                "item": items
            }
            dispList = (resp) => {
                if ('success' in resp) {
                    CallToast('New Branch Manager added successfully!!', "success")
                    table.addRow(items);
                    refreshDest();
                } else
                    CallToast('New Branch Manager added falure!', "error")

            }
            dispErr = (xhr) => {
                CallToast('New Branch Manager DB Call error!', "error")
            }
            jsdata = JSON.stringify(items);
            var options = {
                functionName: 'SRegistermgr2',
                otherData: {
                    items
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);
        };

        function calsum(cell) {
            var row = cell.getRow();
            var rowData = row.getData();
            var sum = Number(rowData.count.replace(',', '')) * Number(rowData.price);
            if (Number(rowData.count) > 0) {
                row.select();
                row.update({
                    total: sum
                });
            } else {
                row.update({
                    count: '',
                    total: ''
                });
            }
            var parent = $(".tabulator-calcs-bottom").find('div:first').html("<p>합계</p>");
        }

        function deleteRow(row) {
            row.delete();
        }

        function mergeAndSum(arr1, arr2) {
            let combined = [...arr1, ...arr2];
            let result = [];

            combined.forEach(item => {
                let existing = result.find(r => r.uid === item.uid);
                if (existing) {
                    existing.count = (parseInt(existing.count) + parseInt(item.count)).toString();
                    existing.price += item.price;
                    existing.total += item.total;
                } else {
                    result.push({
                        ...item,
                        count: item.count.toString(),
                        price: item.price,
                        total: item.total
                    });
                }
            });

            return result;
        }

        // 장바구니: 주문할 상품 선택에서 입력한 수량을 직접 선택 수량으로 저장 → 불러온 교재 신청과 합산
        orderBook = () => {
            table.getData().filter(item => item.count !== undefined && item.total !== undefined).forEach(el => {
                const count = Number(String(el.count).replace(/,/g, ''));
                if (count > 0) manualCartItems[el.uid] = { uid: el.uid, grade: el.grade, title: el.title, price: el.price, count };
                else delete manualCartItems[el.uid];
            });
            rebuildCart();
        }

        var orderOK = () => {

            var result = confirm("구매가 완료되었습니다. \n감사합니다!. \n구매를 계속 하시겠습니까 ?");

            if (result) {
                location.reload();
            } else {
                window.location.href = '../index.php';
            }

        }

        // 저장된 주문(porId)을 카드로 결제 — 금액은 서버가 주문서에서 읽는다 (js/payment.js, Server/payment_policy.php)
        var payOrder = async () => {
            if (!porId) {
                alert("구매내역서를 먼저 확인(저장)해 주세요.");
                return;
            }
            try {
                await gpPay('por', porId, { name: $("#idOwner").val(), phone: $("#idMobile").val() });
            } catch (e) {
                CallToast(e.message, "error");
            }
        }

        var orderModify = () => {

            dispList = (resp) => {
                CallToast('주문 취소 성공!!', "success")
            }
            dispErr = (xhr) => {
                CallToast('주문 취소 실패!!', "error")
            }

            var options = {
                functionName: 'SRemovPorID',
                otherData: {
                    id: porId,
                    pdfname: _pdfname
                }
            };

            location.reload();
        }

        var orderPrint = () => {

            if (!$("#idAgreeOrder").is(":checked") || !$("#idAgreePrivacy").is(":checked")) {
                alert("주문 내용 확인 및 필수 항목에 동의해 주세요.");
                return;
            }
            if (table1.getDataCount() > 0) {
                makePurchasePDFList();

                $("#idConfirmOrder").addClass('disabled');
                $("#idOKOrder").removeClass('disabled');
                $("#idModifyOrder").removeClass('disabled');

                $("#cardMain").toggle();
                $("#cardDest").hide();
                $("#cardPDF").show();
            } else {
                alert("상품 선택을 1권 이상 해야 주문이 가능 합니다.!!")
            }
        }



        var page;
        const black = rgb(0, 0, 0);
        const white = rgb(1, 1, 1);
        const headcol = rgb(0.85, 0.89, 0.95);
        const footcol = rgb(1.00, 0.91, 0.60);

        async function makePurchasePDFList() {
            const pdfDoc = await PDFDocument.create();
            pdfDoc.registerFontkit(fontkit)
            if (!makePurchasePDFList._cachedFontBytes) {
                // NanumBarunGothic.subset.ttf 있으면 우선 사용 (서브셋 생성: pyftsubset NanumBarunGothic.ttf --unicodes="U+0020-007E,U+AC00-D7A3,U+3130-318F" --output-file=NanumBarunGothic.subset.ttf)
                const subsetRes = await fetch('NanumBarunGothic.subset.ttf').catch(() => null);
                const fontFile = (subsetRes && subsetRes.ok) ? subsetRes : await fetch('NanumBarunGothic.ttf');
                makePurchasePDFList._cachedFontBytes = await fontFile.arrayBuffer();
            }
            const fontBytes = makePurchasePDFList._cachedFontBytes;
            const customFont = await pdfDoc.embedFont(fontBytes, { subset: true });
            font = customFont;

            page = pdfDoc.addPage()

            const {
                width,
                height
            } = page.getSize()

            var name = $("#idTableConfirm > div.tabulator-footer > div.tabulator-calcs-holder > div > div:nth-child(1)")
                .html();
            var cnt = $("#idTableConfirm > div.tabulator-footer > div.tabulator-calcs-holder > div > div:nth-child(4)")
                .html();
            var total = $(
                    "#idTableConfirm > div.tabulator-footer > div.tabulator-calcs-holder > div > div:nth-child(5)")
                .html();
            var rest = cvtCurrency(parseFloat(total));

            const fontSize = 14;
            page.setFont(customFont);
            page.setFontSize(fontSize);
            setOrigin(1, 27);
            pwidth = width / cm;
            half = pwidth / 2;

            // 항목이 많으면 페이지를 나눈다 — 아래 여백(cm) 안에 다 들어가지 않으면 새 페이지(머리글 다시 그림)
            const PAGE_BOTTOM = 1.0;   // 기존 1장 레이아웃의 마지막 줄 위치와 같게
            const ensureRoom = (rows, header) => {   // rows: 지금 위치부터 그릴 1cm 줄 수
                if (oriy / cm - (rows - 1) >= PAGE_BOTTOM - 0.01) return;   // 0.01: cm 환산 소수점 오차
                page = pdfDoc.addPage();
                page.setFont(customFont);
                page.setFontSize(fontSize);
                setOrigin(1, 27);
                if (header) header();
            };
            const drawBuyHeader = () => {
                drawRTextBox1(0, 0, 1.5, 1, headcol, "번호", 10, black, "center");
                drawRTextBox(1.5, 0, 1.5, 1, headcol, "단계", 10, black, "center");
                drawRTextBox(3, 0, 5, 1, headcol, "품명", 10, black, "center");
                drawRTextBox(8, 0, 3, 1, headcol, "단가", 10, black, "center");
                drawRTextBox(11, 0, 3, 1, headcol, "수량", 10, black, "center");
                drawRTextBox(14, 0, 4.5, 1, headcol, "공급가격", 10, black, "center");
                moveDown(1);
            };
            const drawCountHeader = () => {
                drawRTextBox1(0, 0, 1.5, 1, headcol, "번호", 10, black, "center");
                drawRTextBox(1.5, 0, 1.5, 1, headcol, "단계", 10, black, "center");
                drawRTextBox(3, 0, 5, 1, headcol, "품명", 10, black, "center");
                drawRTextBox(8, 0, 3, 1, headcol, "", 10, black, "center");
                drawRTextBox(11, 0, 3, 1, headcol, "수량", 10, black, "center");
                drawRTextBox(14, 0, 4.5, 1, headcol, "", 10, black, "center");
                moveDown(1);
            };

            var textwd = customFont.widthOfTextAtSize("구매확인 내역서", fontSize) / cm;
            drawRTexts(half - textwd, 0, 18, black, "구매확인 내역서")
            moveDown(1.0);
            drawRTexts(pwidth - 5, 0, 10, black, formatDate());

            moveDown(1.5);
            acct["acount"]
            // drawRTextBox(0, 0, 18.5, 1, rgb(0.66, 0.82, 0.55), "계좌번호 : 경남은행) 207-0072-6907-01 이상민 (이플렛)", 10, rgb(0, 0,0), "center");
            drawRTextBox(0, 0, 18.5, 1, rgb(0.66, 0.82, 0.55), acct["acount"], 10, rgb(0, 0, 0), "right");

            moveDown(1);
            drawBuyHeader();

            var buyArr = [];
            var item = table1.getData();

            item.forEach(el => {
                if (Number(el['count']) > 0) {
                    var jarr = {
                        "uid": el['uid'],
                        "grade": el['grade'],
                        "title": el['title'],
                        "price": el['price'].toString(),
                        "count": el['count'].toString(),
                        "total": el['total'].toString()
                    }
                    buyArr.push(jarr)
                }
            })
            var k;
            for (let i = 0; i < buyArr.length; i++) {
                k = i + 1;
                ensureRoom(1, drawBuyHeader);
                drawRTextBox(0, 0, 1.5, 1, white, (k).toString(), 10, black, "center");
                drawRTextBox(1.5, 0, 1.5, 1, white, GP_ROLE.courseLabel(buyArr[i]['grade']), 10, black, "center");
                drawRTextBox(3, 0, 5, 1, white, GP_ROLE.courseLabel(buyArr[i]['title']), 10, black, "center");
                drawRTextBox(8, 0, 3, 1, white, cvtCurrency(parseFloat(buyArr[i]['price'])), 10, black, "center");
                drawRTextBox(11, 0, 3, 1, white, buyArr[i]['count'], 10, black, "center");
                drawRTextBox(14, 0, 4.5, 1, white, cvtCurrency(parseFloat(buyArr[i]['total'])), 10, black, "center");
                moveDown(1);
            }
            ensureRoom(4);   // 택배비·총결재금액·배송지·전화번호는 함께
            if (Number(total.replace(/,/g, '')) < 100000) {
                Number(total.replace(/,/g, '')) + 4500;
                total = cvtCurrency(Number(total.replace(/,/g, '')) + 4500);
                drawRTextBox(0, 0, 14, 1, white, "택배비", 12, rgb(0, 0, 0), "center");
                drawRTextBox(14, 0, 4.5, 1, white, "4,500", 12, rgb(0, 0, 0), "center");
                moveDown(1);
            }
            drawRTextBox(0, 0, 14, 1, white, "총결재금액", 12, rgb(0, 0, 0), "center");
            drawRTextBox(14, 0, 4.5, 1, white, total, 12, rgb(0, 0, 0), "center");

            moveDown(1);
            drawRTextBox1(0, 0, 3, 1, footcol, "배송지 ", 10, black, "center");
            drawRTextBox(3, 0, 5, 1, white, $("#idOwner").val()+'/'+$("#idName").val(), 10, black, "left");
            drawRTextBox(8, 0, 10.5, 1, white, $("#idAddr").val(), 10, black, "left");

            moveDown(1);
            drawRTextBox1(0, 0, 3, 1, footcol, "전화번호 ", 10, black, "center");
            drawRTextBox(3, 0, 5, 1, white, $("#idMobile").val(), 10, black, "left");
            drawRTextBox1(8, 0, 3, 1, footcol, "우편번호 ", 10, black, "center");
            drawRTextBox(11, 0, 3, 1, white, $("#idZip").val(), 10, black, "left");
            drawRTextBox(14, 0, 2, 1, footcol, "비고 ", 10, black, "center");
            //drawRTextBox(16, 0, 2.5, 1, white, $("#idName").val(), 10, black, "left");
            drawRTextBox(16, 0, 2.5, 1, white, "", 10, black, "left");

            moveDown(2);
            ensureRoom(6);   // 구매물품 내역서 제목·머리글·첫 줄은 함께

            var textwd = customFont.widthOfTextAtSize("구매물품 내역서", fontSize) / cm;
            drawRTexts(half - textwd, 0, 18, black, "구매물품 내역서")
            moveDown(1.0);
            drawRTexts(pwidth - 5, 0, 10, black, formatDate());

            moveDown(1.5);

            // drawRTextBox(0, 0, 18.5, 1, rgb(0.66, 0.82, 0.55), "계좌번호 : 경남은행) 207-0072-6907-01 이상민 (이플렛)", 10, rgb(0, 0,0), "center");
            drawRTextBox(0, 0, 18.5, 1, rgb(0.9, 0.34, 0.55), "", 10, rgb(0, 0, 0), "right");

            moveDown(1);
            drawCountHeader();

            var k;
            var cot = 0;
            for (let i = 0; i < buyArr.length; i++) {
                k = i + 1;
                ensureRoom(1, drawCountHeader);
                drawRTextBox(0, 0, 1.5, 1, white, (k).toString(), 10, black, "center");
                drawRTextBox(1.5, 0, 1.5, 1, white, GP_ROLE.courseLabel(buyArr[i]['grade']), 10, black, "center");
                drawRTextBox(3, 0, 5, 1, white, GP_ROLE.courseLabel(buyArr[i]['title']), 10, black, "center");
                drawRTextBox(8, 0, 3, 1, white, "", 10, black, "center");
                drawRTextBox(11, 0, 3, 1, white, buyArr[i]['count'], 10, black, "center");
                cot += Number(buyArr[i]['count'])
                drawRTextBox(14, 0, 4.5, 1, white, "", 10, black, "center");
                moveDown(1);
            }
            ensureRoom(3);   // 총수량·배송지·전화번호는 함께
            drawRTextBox(0, 0, 14, 1, white, "총수량", 12, rgb(0, 0, 0), "center");
            drawRTextBox(11, 0, 7.5, 1, white, cot.toString(), 12, rgb(0, 0, 0), "center");

            moveDown(1);
            drawRTextBox1(0, 0, 3, 1, footcol, "배송지 ", 10, black, "center");
            drawRTextBox(3, 0, 5, 1, white, $("#idOwner").val(), 10, black, "left");
            drawRTextBox(8, 0, 10.5, 1, white, $("#idAddr").val(), 10, black, "left");

            moveDown(1);
            drawRTextBox1(0, 0, 3, 1, footcol, "전화번호 ", 10, black, "center");
            drawRTextBox(3, 0, 5, 1, white, $("#idMobile").val(), 10, black, "left");
            drawRTextBox1(8, 0, 3, 1, footcol, "우편번호 ", 10, black, "center");
            drawRTextBox(11, 0, 3, 1, white, $("#idZip").val(), 10, black, "left");
            drawRTextBox(14, 0, 2, 1, footcol, "비고 ", 10, black, "center");
            //drawRTextBox(16, 0, 2.5, 1, white, $("#idName").val(), 10, black, "left");
            drawRTextBox(16, 0, 2.5, 1, white, "", 10, black, "left");

            const pdfBytes = await pdfDoc.save()

            var formData = new FormData();
            formData.append('pdfFile', new Blob([pdfBytes]), 'generated_pdf.pdf');
            item = table1.getData();
            var porList = []
            item.forEach(el => {
                var jarr = {
                    "uid": el['uid'],
                    "grade": el['grade'],
                    "title": el['title'],
                    "price": el['price'],
                    "count": el['count'],
                    "total": el['total']
                }
                porList.push(jarr);
            });
            if (Number(total.replace(/,/g, '')) < 100000) {
                var jarr = {
                    "uid": "",
                    "grade": "",
                    "title": "택배비",
                    "price": "4500",
                    "count": "",
                    "total": "4500"
                }
                porList.push(jarr);
            }

            porList.push({
                "uid": "",
                "grade": "총금액",
                "title": "",
                "price": "",
                "count": cnt,
                "total": total
            });

            formData.append('id', user);
            formData.append('order', $("#idOwner").val())


            formData.append('zip', $("#idZip").val());
            formData.append('addr', $("#idAddr").val());
            formData.append('mobile', $("#idMobile").val());
            formData.append('postlist', JSON.stringify(porList));
            formData.append('home_order_ids', JSON.stringify(homeOrderIds));
            formData.append('agree', '1');   // 확인·동의 (orderPrint 에서 체크)

            dispList = (resp) => {
                porId = resp['porid'];   // 주문번호는 서버에서 생성
                $("#idPayOrder").removeClass('disabled').attr('aria-disabled', 'false');
                CallToast('Upload Pdf successfully!!', "success")
                if (homeOrderIds.length) {   // 서버에서 완료 처리됨 → 신청 목록 갱신
                    homeOrderIds = [];
                    listHomeOrders();
                }
                document.getElementById('pdfDiv').src = resp['url'];
                orderList(null);
            }
            dispErr = (xhr) => {
                CallToast((xhr && xhr.responseJSON && xhr.responseJSON.error) || 'Upload Pdf falure!', "error")
            }

            formData.append('functionName', 'SUploadBoardPDF');
            CallAjax1("SMethods.php", "POST", formData, dispList, dispErr);

        }

        selectAll = () => {
            //var parent = $("#idTableConfirm > div.tabulator-footer > div.tabulator-calcs-holder > div > div:nth-child(1)").val("총합");
            //var parent = $(".tabulator-calcs-bottom").find('div:first').html("<p>합계</p>");
            table1.selectRow();

            var rows = table1.getRows();

            rows.forEach(function(row) {
                if (row.getData().name === "Summary") {
                    // 요약 행 발견
                    var summaryRow = row;
                    // 여기에서 요약 행에 대한 처리 수행
                    console.log("Summary Row:", summaryRow.getData());
                }
            });
        }

        $('#custom-tabs-one-tab a').on('click', function(e) {
            e.preventDefault();

            $('#custom-tabs-one-tab .nav-link').removeClass('active');
            $(this).addClass('active');

            $("#custom-tabs-one-homeorder").removeClass('show active');
            if (this.id == "custom-tabs-one-homeorder-tab") {   // 본사: 홈스쿨 교재 신청
                $("#custom-tabs-one-home, #custom-tabs-one-profile").removeClass('show active');
                $("#custom-tabs-one-homeorder").addClass('show active');
                $("#cardMain").show();
                $('#idSecDiv').hide();
            } else if (this.id == "custom-tabs-one-profile-tab") {
                $("#custom-tabs-one-home").removeClass('show active');
                $("#custom-tabs-one-profile").addClass('show active');
                $("#cardMain").show();
                $('#idSecDiv').hide();
                $("#pdfDiv").remove();
            } else if (this.id == "custom-tabs-one-home-tab") {
                $("#custom-tabs-one-profile").removeClass('show active');
                $("#custom-tabs-one-home").addClass('show active');
                $("#cardMain").show();
                $("#idSecDiv").show();
                $("#pdfDiv").remove();
            }
        });


        document.getElementById("idCbmOrderSearch").addEventListener("change", function() {

            // 선택된 옵션 가져오기
            var selectedOption = this.options[this.selectedIndex];

            // 선택된 옵션의 값(value) 가져오기
            var selectedValue = selectedOption.value;

            // 선택된 옵션의 텍스트 가져오기
            var selectedText = selectedOption.text;

            if (selectedText == "전품목")
                selectedValue = null;
            orderList(selectedValue);

        });

        // 연도 필터 동적 생성 및 이벤트
        function buildYearFilter(resp) {
            var yearSet = new Set();
            resp.forEach(function(el) {
                var match = el['por_id'].match(/^P(\d{2})/);
                if (match) yearSet.add(match[1]);
            });
            var yearSelect = document.getElementById("idYearFilter");
            var currentVal = yearSelect.value;
            yearSelect.innerHTML = '<option value="ALL">전체년도</option>';
            // 내림차순 정렬 (최신 연도 먼저)
            Array.from(yearSet).sort(function(a, b) { return parseInt(b) - parseInt(a); }).forEach(function(y) {
                var opt = document.createElement('option');
                opt.value = y;
                opt.text = "20" + y + "년";
                yearSelect.add(opt);
            });
            // 기존 선택값 복원
            if (currentVal && Array.from(yearSelect.options).some(function(o) { return o.value == currentVal; })) {
                yearSelect.value = currentVal;
            }
        }

        document.getElementById("idYearFilter").addEventListener("change", function() {
            var searchSelect = document.getElementById("idCbmOrderSearch");
            var selectedOption = searchSelect.options[searchSelect.selectedIndex];
            var selectedValue = selectedOption.value;
            if (selectedOption.text == "전품목")
                selectedValue = null;
            orderList(selectedValue);
        });
    </script>

</body>

</html>
