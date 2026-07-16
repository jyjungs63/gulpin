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
    <style>
    </style>
</head>

<body class="kgarden-page">
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
                            </ul>
                        </div>
                        <div class="kgarden-body" id="cardMain">
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                                <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-home-tab">

                                    <div class="kgarden-form">
                                        <div class="kgarden-field">
                                            <label for="idGrade">필터</label>
                                            <select id="idGrade" data-placeholder="Choose Items">
                                                <option val="va">전체</option>
                                                <option val="v4">한자뚝0</option>
                                                <option val="v5">한자뚝A</option>
                                                <option val="v6">한자뚝B</option>
                                            </select>
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
                    </div>
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
    <script src="../js/header.js"></script>
    <script src="../js/libpdf.js"></script>

    <script src="order.js" ></script>
    <script src="acount.js"></script>

    <script>
        let listprice2;
        let data;
        let sele = "가맹관리";
        var porId = "";


        if (user == "chaitalk")
            user = "admin";
        if (user == 'admin')
            $("#idBtDelever").css('visibility', 'visible');
        else
            $("#idBtDelever").css('visibility', 'hidden');

        async function updateTableData(response) {
            try {
                //const response = await fetch(`${url}?t=${new Date().getTime()}`);
                // data = await response.json();

                data = JSON.parse(response['success'][0]['price'])

                if (role == "1")
                    sele = "지사관리"
                else if (role == "2")
                    sele = "원관리"

                listprice2 = [...data[sele]["한자뚝0"], ...data[sele]["한자뚝A"], ...data[sele]["한자뚝B"]];

                table.setData(listprice2)
                    .then(() => {
                        console.log("테이블 데이터 업데이트 완료");
                    })
                    .catch(error => {
                        console.error("데이터 설정 중 오류:", error);
                    });
            } catch (error) {
                console.error("데이터 가져오기 실패:", error);
            }
        }

        dispErr = (xhr) => {
            CallToast("price list 조회 실패!!", "error");
        };

        document.addEventListener('DOMContentLoaded', () => {
            // 테이블 초기화
            var options = {
                functionName: "SGetListPrice",
                otherData: {
                    id: ""
                },
            };
            CallAjax("SMethods.php", "POST", options, updateTableData, dispErr);
        });
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

        let rolename = "지사장";
        if (role == 2)
            rolename = "선생님"
        else
            rolename = "가맹점"
        document.getElementById("idOrdertext").innerHTML = (user + ` ${rolename}/구매`);

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
                        //blist.push([el['id'], el['bname']])
                        blist.push([el['id'], el['id']])
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
                    if (user == 'admin')
                        option2.value = 'admin'; // 지사명
                    else
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

        function mergeAndUpdate(arr1, arr2) {
            let result = [...arr2];

            arr1.forEach(item => {
                let existingIndex = result.findIndex(r => r.uid === item.uid);
                if (existingIndex !== -1) {
                    // 기존 데이터 업데이트
                    result[existingIndex] = {
                        ...result[existingIndex],
                        ...item
                    };
                } else {
                    // 새로운 데이터 추가
                    result.push(item);
                }
            });

            return result;
        }

        orderBook = () => {
            var sum = 0;
            let item = table.getData().filter(item => item.count !== undefined && item.total !== undefined);
            var itemcart = table1.getData();
            if (itemcart.length > 1)
                table1.clearData();
            let newitem = mergeAndUpdate(item, itemcart);

            newitem.forEach(el => {
                if (Number(el['count']) > 0) {
                    var jarr = {
                        "uid": el['uid'],
                        "grade": el['grade'],
                        "title": el['title'],
                        "price": el['price'],
                        "count": el['count'],
                        "total": el['total']
                    }
                    table1.addRow(jarr);
                    sum += Number(el['total'])
                }
            })

            table1.selectRow();

            var parent = $(
                    "#idTableConfirm > div.tabulator-footer > div.tabulator-calcs-holder > div > div:nth-child(1)"
                )
                .html("합계");

        }

        var orderOK = () => {

            var result = confirm("구매가 완료되었습니다. \n감사합니다!. \n구매를 계속 하시겠습니까 ?");

            if (result) {
                location.reload();
            } else {
                window.location.href = '../index.php';
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

            var textwd = customFont.widthOfTextAtSize("구매확인 내역서", fontSize) / cm;
            drawRTexts(half - textwd, 0, 18, black, "구매확인 내역서")
            moveDown(1.0);
            drawRTexts(pwidth - 5, 0, 10, black, formatDate());

            moveDown(1.5);
            acct["acount"]
            // drawRTextBox(0, 0, 18.5, 1, rgb(0.66, 0.82, 0.55), "계좌번호 : 경남은행) 207-0072-6907-01 이상민 (이플렛)", 10, rgb(0, 0,0), "center");
            drawRTextBox(0, 0, 18.5, 1, rgb(0.66, 0.82, 0.55), acct["acount"], 10, rgb(0, 0, 0), "right");

            moveDown(1);
            drawRTextBox1(0, 0, 1.5, 1, headcol, "번호", 10, black, "center");
            drawRTextBox(1.5, 0, 1.5, 1, headcol, "단계", 10, black, "center");
            drawRTextBox(3, 0, 5, 1, headcol, "품명", 10, black, "center");
            drawRTextBox(8, 0, 3, 1, headcol, "단가", 10, black, "center");
            drawRTextBox(11, 0, 3, 1, headcol, "수량", 10, black, "center");
            drawRTextBox(14, 0, 4.5, 1, headcol, "공급가격", 10, black, "center");
            moveDown(1);

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
                drawRTextBox(0, 0, 1.5, 1, white, (k).toString(), 10, black, "center");
                drawRTextBox(1.5, 0, 1.5, 1, white, buyArr[i]['grade'], 10, black, "center");
                drawRTextBox(3, 0, 5, 1, white, buyArr[i]['title'], 10, black, "center");
                drawRTextBox(8, 0, 3, 1, white, cvtCurrency(parseFloat(buyArr[i]['price'])), 10, black, "center");
                drawRTextBox(11, 0, 3, 1, white, buyArr[i]['count'], 10, black, "center");
                drawRTextBox(14, 0, 4.5, 1, white, cvtCurrency(parseFloat(buyArr[i]['total'])), 10, black, "center");
                moveDown(1);
            }
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

            var textwd = customFont.widthOfTextAtSize("구매물품 내역서", fontSize) / cm;
            drawRTexts(half - textwd, 0, 18, black, "구매물품 내역서")
            moveDown(1.0);
            drawRTexts(pwidth - 5, 0, 10, black, formatDate());

            moveDown(1.5);

            // drawRTextBox(0, 0, 18.5, 1, rgb(0.66, 0.82, 0.55), "계좌번호 : 경남은행) 207-0072-6907-01 이상민 (이플렛)", 10, rgb(0, 0,0), "center");
            drawRTextBox(0, 0, 18.5, 1, rgb(0.9, 0.34, 0.55), "", 10, rgb(0, 0, 0), "right");

            moveDown(1);
            drawRTextBox1(0, 0, 1.5, 1, headcol, "번호", 10, black, "center");
            drawRTextBox(1.5, 0, 1.5, 1, headcol, "단계", 10, black, "center");
            drawRTextBox(3, 0, 5, 1, headcol, "품명", 10, black, "center");
            drawRTextBox(8, 0, 3, 1, headcol, "", 10, black, "center");
            drawRTextBox(11, 0, 3, 1, headcol, "수량", 10, black, "center");
            drawRTextBox(14, 0, 4.5, 1, headcol, "", 10, black, "center");
            moveDown(1);

            var k;
            var cot = 0;
            for (let i = 0; i < buyArr.length; i++) {
                k = i + 1;
                drawRTextBox(0, 0, 1.5, 1, white, (k).toString(), 10, black, "center");
                drawRTextBox(1.5, 0, 1.5, 1, white, buyArr[i]['grade'], 10, black, "center");
                drawRTextBox(3, 0, 5, 1, white, buyArr[i]['title'], 10, black, "center");
                drawRTextBox(8, 0, 3, 1, white, "", 10, black, "center");
                drawRTextBox(11, 0, 3, 1, white, buyArr[i]['count'], 10, black, "center");
                cot += Number(buyArr[i]['count'])
                drawRTextBox(14, 0, 4.5, 1, white, "", 10, black, "center");
                moveDown(1);
            }
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
            porId = 'P' + formatDate().slice(2) + $("#idOwner").val() + Math.floor(Math.random() * 10) + 1;
            formData.append('porid', porId)

            dispList = (resp) => {
                CallToast('Upload Pdf successfully!!', "success")
                document.getElementById('pdfDiv').src = resp['url'];
                orderList(null);
            }
            dispErr = (xhr) => {
                CallToast('Upload Pdf falure!', "error")
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

            if (this.id == "custom-tabs-one-profile-tab") {
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
