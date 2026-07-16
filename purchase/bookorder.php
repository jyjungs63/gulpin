<!DOCTYPE html>
<html lang="utf-8">
<?php
include "../libs/header.php";
?>

<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/brands.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- <link href="../common.css" rel="stylesheet"> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/alasql/4.5.0/alasql.min.js"></script>
    <style>
        .custom-width {
            width: 50px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            text-align: center;
        }

        th.col1 {
            width: 10%;
        }

        th.col2 {
            width: 305;
        }

        th.col3 {
            width: 15%;
        }

        th.col4 {
            width: 35%;
        }

        th.col5 {
            width: 10%;
        }

        .nb {
            border: none;
            text-align: center;
        }

        .student-item {
            display: inline-block;
            background: #e6f3ff;
            padding: 2px 8px;
            margin: 2px;
            border-radius: 3px;
        }

        .delete-btn {
            color: red;
            margin-left: 5px;
            cursor: pointer;
        }

        .controls {
            margin-bottom: 10px;
        }

        .student-count {
            font-weight: bold;
            color: #2196f3;
        }
    </style>
</head>

<body style="background-color: #f4f6f9">
    <div class="p-3">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 id="idOrdertext">Purchase</h5>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:window.history.back();">이전</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-info card-tabs" id="idCardPurchase">
                        <div class="card-header" style="background-color: #38998580">
                            <h3 class="card-title">

                            </h3>
                            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                                        href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                        aria-selected="true">주문</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill"
                                        href="#custom-tabs-one-profile" role="tab"
                                        aria-controls="custom-tabs-one-profile" aria-selected="false">주문내역</a>
                                </li>
                            </ul>
                            <div class="card-tools">
                                <button id="idCardPurchaseBtn" type="button" class="btn  btn-sm btn-primary"
                                    data-card-widget="collapse" data-toggle="tooltip" title="Collapse">접기/펴기
                                </button>
                            </div>
                        </div>
                        <div class="card-body pad" id="cardMain" style="background-color: #88babe87">
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                                <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-home-tab">

                                    <div class="d-flex align-items-end justify-content-end"
                                        style="margin-bottom: 10px;">
                                        <div class="input-group mb-3">
                                            <span class="d-flex badge bg-light text-dark align-items-center">필터</span>
                                            &nbsp;
                                            <select class="form-select form-control-sm" id="idGrade"
                                                data-placeholder="Choose Items" style="width: 120px">
                                                <option val="va">전체</option>
                                                <option val="v4">한자뚝0</option>
                                                <option val="v5">한자뚝A</option>
                                                <option val="v6">한자뚝B</option>
                                                <option val="v7">한자파닉스</option>
                                                <option val="v8">파자한자1</option>
                                                <option val="v9">파자한자2</option>
                                                <option val="v10">파자한자3</option>
                                                <option val="v11">파자한자4</option>
                                                <option val="v12">파자한자5</option>
                                                <option val="v13">파자한자6</option>
                                                <option val="v14">파자한자7</option>
                                                <option val="v15">어휘의신</option>
                                            </select>
                                        </div>
                                    </div>
                                    <h5>
                                        <b> 주문할 상품 선택</b>
                                    </h5>
                                    <div id="idTable">

                                    </div>
                                    <div class="d-flex align-items-end justify-content-end" style="margin-top: 10px;">
                                        <div align="center">

                                        </div>
                                    </div>

                                    <div class="col-12 text-center">
                                        <a id="anchorRead" href="javascript:orderBook()"
                                            class="btn btn-info align-items-end justify-content-end" role="button"
                                            data-toggle="tooltip" title="Add to Cart " aria-disabled="true"><i
                                                class="fa-solid fa-cart-shopping"></i> 장바구니</a>&nbsp;&nbsp;
                                    </div>
                                    <h5> <b>주문한 상품 확정</b></h5>
                                    <div id="idTableConfirm" style="margin-top: 10px;">

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-profile-tab">
                                    <div class="col-md-12 input-group input-group-sm mb-3">
                                        <!-- <button class="btn btn-outline-primary btn-sm" type="button">개별조회</button> -->
                                        <span class="d-flex badge bg-light text-dark align-items-center">개별조회</span>
                                        &nbsp;&nbsp;
                                        <select class="form-select form-control-sm" id="idPorList"
                                            data-placeholder="주문서선택" style="width: 150px">
                                        </select>
                                        &nbsp;&nbsp;
                                        <span id="idSpfind"
                                            class="d-flex badge bg-light text-dark align-items-center">지사별조회</span>
                                        &nbsp;&nbsp;
                                        <select class="form-select form-control-sm" id="idPorBranch"
                                            data-placeholder="지사선택" style="width: 150px">
                                        </select>
                                        &nbsp;&nbsp;
                                        <!-- <button class="btn btn-outline-primary btn-sm" type="button">월별조회</button> -->
                                        <span class="d-flex badge bg-light text-dark align-items-center">월별조회</span>
                                        &nbsp;&nbsp;
                                        <input type="month" class="form-control form-control-sm" id="monthPicker"
                                            name="month" style="width: 120px">
                                        </input>
                                        &nbsp;
                                        <span id="idLbParcel"
                                            class="d-flex badge bg-light text-dark align-items-center">택배비</span>
                                        &nbsp;&nbsp;
                                        <input class="form-control form-control-sm custom-width" id="idParcel"
                                            type="text" placeholder="택배비" style="width: 100px;">
                                        &nbsp;&nbsp;
                                        <button id="idBtParcel" class="btn btn-outline-success btn-sm disabled"
                                            type="button" onclick="AddParcel()">택배비추가
                                        </button>&nbsp;&nbsp;
                                        <button id="idBtDelever" class="btn btn-outline-primary btn-sm disabled"
                                            type="button" onclick="AddDelever()">배송완료
                                        </button>
                                    </div>

                                    <div id="porTableDiv"></div>
                                    <table id="porTable" style="width: 100%;border: 1px solid black;">
                                        <thead>
                                            <tr>
                                                <th style="height: 50px" class="col1"><a
                                                        href="javascript:globalsort('지사/유치원명')">지사/유치원명<i
                                                            class="fa-solid fa-sort"></i></a></th>
                                                <th style="height: 50px" class="col1"><a
                                                        href="javascript:globalsort('날짜')">날짜<i
                                                            class="fa-solid fa-sort"></i></a></th>
                                                <th class="col2">내역</th>
                                                <th class="col3">금액</th>
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
                </div>
            </div>
        </section>
    </div>
    <div class="p-3" id="idSecDiv">
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header" style="background-color: #38998580">
                            <h3 class="card-title">

                                <p> <b>배송지 확정</b></p>
                            </h3>
                            <div class="input-group mb-3">

                                <span class="d-flex badge bg-light text-dark align-items-center">주소지</span>
                                &nbsp;

                                <input class="form-control form-control-sm custom-width" id="idName" type="text"
                                    placeholder="이름">
                                &nbsp;
                                <input class="form-control form-control-sm custom-width" id="idOwner" type="text"
                                    placeholder="원명">&nbsp;

                                &nbsp;
                                <input class="form-control form-control-sm custom-width" id="idMobile" type="text"
                                    placeholder="전화" style="width: 20px;">
                                &nbsp;
                                <input class="form-control form-control-sm" id="idAddr" type="text" placeholder="주소"
                                    style="width: 300px;">
                                &nbsp;
                                <input class="form-control form-control-sm custom-width" id="idZip" type="text"
                                    placeholder="우편번호" style="width: 20px;">
                                &nbsp;
                                <button class="btn btn-outline-primary btn-sm" type="button"
                                    onclick="execDaumPostcode( 'idZip','idAddr')">
                                    주소찾기</button>
                                &nbsp;
                                <button class="btn btn-outline-success btn-sm" type="button" onclick="AddBranch()">배송지추가
                                </button>
                            </div>
                            </h3>
                            <div class="card-tools" id="idCardAddress">

                                &nbsp;&nbsp;
                                <button id="idCardAddressBtn" type="button" class="btn  btn-sm btn-primary"
                                    data-card-widget="collapse" data-toggle="tooltip" title="Collapse">접기/펴기</button>

                            </div>
                        </div>
                        <div class="card-body " id="cardDest" style="background-color: #88babe87">
                            <p> <b>배송지 선택</b></p>
                            <div id="idTableDest" style="margin-top: 10px;">

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="card card-outline card-secondary" id="idCardPDF">
                        <div class="card-header" style="background-color: #38998580">
                            <h3 class="card-title">
                                <p> <b>구매내역서</b></p>

                            </h3>
                            <div class="text-center">
                                <a id="idConfirmOrder" href="javascript:orderPrint()" class="btn btn-warning disabled"
                                    role="button" data-toggle="tooltip" title="구매내역서확인" aria-disabled="true"><i
                                        class="fa-solid fa-cart-shopping"></i>구매내역서확인</a>
                                <a id="idModifyOrder" href="javascript:orderModify()" class="btn btn-warning disabled"
                                    role="button" data-toggle="tooltip" title="수정" aria-disabled="true">수정(재작성)</a>
                                <a id="idOKOrder" href="javascript:orderOK()" class="btn btn-warning disabled"
                                    role="button" data-toggle="tooltip" title="구매완료" aria-disabled="true"></i>구매완료</a>
                            </div>
                            <div class="card-tools">
                                <button id="idCardPDFBtn" type="button" class="btn btn-sm btn-primary"
                                    data-card-widget="collapse" data-toggle="tooltip" title="Collapse">접기/펴기</button>

                            </div>
                        </div>
                        <div class="card-body " id="cardPDF" style="background-color: #88babe87">

                            <iframe id="pdfDiv" style="width: 100%; height: 900px"></iframe>
                        </div>

                    </div>
                </div>
            </div>

        </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
    <script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/luxon/3.4.4/luxon.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script src="https://unpkg.com/@pdf-lib/fontkit@0.0.4/dist/fontkit.umd.min.js"></script>
    <script src="https://unpkg.com/downloadjs@1.4.7"></script>
    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>
    <script src="https://raw.githack.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js">
    </script>

    <script src="../js/common.js"></script>
    <script src="../js/header.js"></script>
    <script src="../js/libpdf.js"></script>
    <script src="bookorder.js"></script>
    <script src="acount.js"></script>


    <script>
        let listprice2;
        let data;
        let sele = "가맹관리";
        var porId = "";

        // if (role != '1' && role != '2' && role != '3' && role != '9') {
        //     CallToast("지사 관리 권한으로 로긴 하세요", "error");
        //     window.location.href = "../login/login.php";
        // }

        cardWidgetManage($('#idCardPurchase'), $('#idCardPurchaseBtn')); //idCardAddressBtn
        cardWidgetManage($('#idCardAddress'), $('#idCardAddressBtn')); //idCardAddressBtn
        cardWidgetManage($('#idCardPDF'), $('#idCardPDFBtn')); //idCardAddressBtn
        if (user == "chaitalk")
            user = "admin";
        if (user == 'admin')
            $("#idBtDelever").css('visibility', 'visible');
        else
            $("#idBtDelever").css('visibility', 'hidden');

        async function updateTableData(response) {
            try {
                // const response = await fetch(`${url}?t=${new Date().getTime()}`);
                // data = await response.json();

                data = JSON.parse(response['success'][0]['price'])

                if (role == "1")
                    sele = "지사관리"
                else if (role == "2")
                    sele = "원관리"

                listprice2 = [...data[sele]["한자뚝0"], ...data[sele]["한자뚝A"], ...data[sele]["한자뚝B"], ...data[sele]["한자파닉스"],
                    ...data[sele]["파자한자1"], ...data[sele]["파자한자2"], ...data[sele]["파자한자3"], ...data[sele]["파자한자4"],
                    ...data[sele]["파자한자5"], ...data[sele]["파자한자6"], ...data[sele]["파자한자7"], ...data[sele]["어휘의신"]
                ];

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
            //updateTableData('price.json')
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

        }

        let rolename = "지사장";
        if (role == 2)
            rolename = "선생님"
        else if (role == 3)
            rolename = "가맹점"
        document.getElementById("idOrdertext").innerHTML = (user + ` ${rolename}/구매`);

        const {
            PDFDocument,
            rgb
        } = PDFLib;

        orderList = () => {
            items = [];
            blist = [];

            dispList = (resp) => {
                $('#idPorList').empty();
                let select = document.getElementById('idPorList');
                let option = document.createElement('option');
                option.text = ""; // Set the text of the new option
                option.value = ""; // Set the value attribute (if needed)
                select.add(option);

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
                    // Create a new option element
                    option = document.createElement('option');

                    if (el['confirm'] != 1) { // 배송이 안된 por list 만 selection box에 추가한다.
                        option.text = el['por_id']; // Set the text of the new option
                        option.value = el['por_id']; // Set the value attribute (if needed)
                        select.add(option);
                    }

                    if (user == "admin") {
                        //blist.push([el['id'], el['bname']])
                        blist.push([el['id'], el['id']])
                    } else {
                        blist.push([el['id'], el['order']])
                    }

                })
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
                    $("#idParcel").hide();
                    $("#idBtParcel").hide();
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
            selectedRows.forEach(function(row) {});


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
            $("#idConfirmOrder").removeClass('disabled');
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
                    count: ''
                });
            }
            var parent = $(".tabulator-calcs-bottom").find('div:first').html("<p>합계</p>");
        }

        function deleteRow(row) {
            row.delete();
        }

        orderBook = () => {
            var sum = 0;
            var item = table.getSelectedData();
            item.forEach(el => {
                if (Number(el['count']) > 0) {
                    const rowId = el['uid'];
                    const students = rowStudents[rowId] || [];
                    var jarr = {
                        "uid": el['uid'],
                        "grade": el['grade'],
                        "title": el['title'],
                        "price": el['price'],
                        "count": el['count'],
                        "total": el['total'],
                        "selectedStudents": students
                    }
                    table1.addRow(jarr);
                    sum += Number(el['total'])
                }
            })

            if (Number(sum) < 100000) {
                var jarr = {
                    "uid": "",
                    "grade": "",
                    "title": "택배비",
                    "price": "4500",
                    "count": "",
                    "total": "4500"
                }
                table1.addRow(jarr);
            }

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
            const fontBytes = await fetch('NanumBarunGothic.ttf').then((res) => res.arrayBuffer());
            const customFont = await pdfDoc.embedFont(fontBytes);
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
                        "total": el['total'].toString(),
                        "selectedStudents": el['selectedStudents']
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
                drawRTextBox(0, 0, 14, 1, white, "택배비", 12, rgb(0, 0, 0), "center");
                drawRTextBox(14, 0, 4.5, 1, white, "4,500", 12, rgb(0, 0, 0), "center");
                moveDown(1);
            }
            drawRTextBox(0, 0, 14, 1, white, "총결재금액", 12, rgb(0, 0, 0), "center");
            drawRTextBox(14, 0, 4.5, 1, white, total, 12, rgb(0, 0, 0), "center");

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

            moveDown(2);

            var textwd = customFont.widthOfTextAtSize("구매물품 내역서", fontSize) / cm;
            drawRTexts(half - textwd, 0, 18, black, "구매물품 내역서")
            moveDown(1.0);
            drawRTexts(pwidth - 5, 0, 10, black, formatDate());

            moveDown(1.5);

            drawRTextBox(0, 0, 18.5, 1, rgb(0.9, 0.34, 0.55), "", 10, rgb(0, 0, 0), "right");

            moveDown(1);
            drawRTextBox1(0, 0, 1.5, 1, headcol, "번호", 10, black, "center");
            drawRTextBox(1.5, 0, 1.5, 1, headcol, "단계", 10, black, "center");
            drawRTextBox(3, 0, 5, 1, headcol, "품명", 10, black, "center");
            drawRTextBox(8, 0, 3, 1, headcol, "수량", 10, black, "center");
            drawRTextBox(11, 0, 7.5, 1, headcol, "학생명", 10, black, "center");
            // drawRTextBox(14, 0, 4.5, 1, headcol, "", 10, black, "center");
            moveDown(1);

            var k;
            var cot = 0;
            for (let i = 0; i < buyArr.length; i++) {
                k = i + 1;
                drawRTextBox(0, 0, 1.5, 1, white, (k).toString(), 10, black, "center");
                drawRTextBox(1.5, 0, 1.5, 1, white, buyArr[i]['grade'], 10, black, "center");
                drawRTextBox(3, 0, 5, 1, white, buyArr[i]['title'], 10, black, "center");
                drawRTextBox(8, 0, 3, 1, white, buyArr[i]['count'], 10, black, "center");
                let students = buyArr[i]['selectedStudents'];
                if (students.length > 9) {
                    drawRTextBox(11, 0, 7.5, 1, white, students.slice(1, 10).join(","), 8, black, "center");
                    moveDown(1);
                    drawRTextBox(0, 0, 1.5, 1, white, "", 10, black, "center");
                    drawRTextBox(1.5, 0, 1.5, 1, white, "", 10, black, "center");
                    drawRTextBox(3, 0, 5, 1, white, "", 10, black, "center");
                    drawRTextBox(8, 0, 3, 1, white, "", 10, black, "center");
                    drawRTextBox(11, 0, 7.5, 1, white, students.slice(10).join(","), 8, black, "center");
                } else
                    drawRTextBox(11, 0, 7.5, 1, white, students.join(","), 8, black, "center");

                cot += Number(buyArr[i]['count'])
                //drawRTextBox(14, 0, 4.5, 1, white, "", 10, black, "center");
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
                    "selectedStudents": el['selectedStudents'],
                    "total": el['total']
                }
                porList.push(jarr);
            });

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

            if (this.id == "custom-tabs-one-profile-tab") {
                $("#cardMain").show();
                $('#idSecDiv').hide();

                var newDiv = $('<iframe id="pdfDiv" style="width: 100%; height: 900px"></iframe>');

                $("#idCardPurchase").append(newDiv)
            }
        });
        $('#custom-tabs-one-tab a').on('click', function(e) {
            e.preventDefault();
            //alert(this)
            if (this.id == "custom-tabs-one-home-tab") {
                $("#idSecDiv").show();
                $("#pdfDiv").remove();
            }

        });
    </script>

</body>

</html>