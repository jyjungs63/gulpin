<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Document</title>

    <?php
    include '../include.php';
    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/alasql/4.2.2/alasql.min.js"></script> -->
    <!-- json util like sql -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/brands.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <link href="../common.css" rel="stylesheet">
</head>

<body style="background-color: #f4f6f9">
    <div class="p-3">
        <section class="content-header" style="background-color: #f4f6f9">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 id="brachname">Admin관리창</h5>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:window.history.back();">Prev</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <!-- <div class="row">
                <div class="card card-outline card-primary">
                    <div class="card-header" style="background-color: #38998580">
                        <h3 class="card-title">
                            <div class="input-group mb-3">
                                &nbsp;
                                <input class="form-control form-control-sm" id="idPorID" type="text"
                                    placeholder="POR ID">
                                &nbsp;
                                <input class="form-control form-control-sm" id="idName" type="text" placeholder="Name">
                                &nbsp;
                                <input class="form-control form-control-sm" id="idOwner" type="text"
                                    placeholder="Owner">&nbsp;

                                <input class="form-control form-control-sm" id="idMobile" type="text"
                                    placeholder="Mobile">
                                &nbsp;
                                <input class="form-control form-control-sm" id="idAddr" type="text"
                                    placeholder="Address" style="width: 150px;">
                                &nbsp;
                                <input class="form-control form-control-sm" id="idRdate" type="text" placeholder="구매일"
                                    style="width: -5px;">
                                &nbsp;
                                <button class="btn btn-outline-success btn-sm" type="button" onclick="ConfirmPOR()">구매처리
                                </button>
                            </div>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse"
                                data-toggle="tooltip" title="Collapse">
                                <i class="fas fa-minus"></i></button>
                            <button type="button" class="btn btn-tool btn-sm" data-card-widget="remove"
                                data-toggle="tooltip" title="Remove">
                                <i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="card-body " id="cardDest" style="background-color: #88babe87">

                        <div id="porTableDiv"></div>
                    </div>

                </div>

            </div> -->
        </section>
    </div>

    <div class="modal " id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        style="background-color: #f4f6f9">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #f4f6f9">
                    <h5 class="modal-title" id="exampleModalLabel">회원가입승인</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #f4f6f9">
                    <section class="content">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-outline card-info">
                                    <div class="card-header" style="background-color: #38998580">
                                        <h4 class="card-title">
                                            <div class="input-group mb-3">
                                                <button class="btn btn-outline-secondary" type="button">필터
                                                </button>
                                                <select class="form-select" id="idGrade"
                                                    data-placeholder="Choose Items">
                                                    <option val="va">전체</option>
                                                    <option val="v4">승인</option>
                                                    <option val="v5">미승인</option>
                                                </select>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <select class="form-select form-control-sm" id="idGrade2"
                                                data-placeholder="Choose Items" style="width: 120px;">
                                                    <option value="va">전체</option>
                                                    <option value="v4">지사관리</option>
                                                    <option value="v5" selected>원관리</option>
                                                    <option value="v6">가맹관리</option>
                                                </select>&nbsp;&nbsp;
                                            </div>
                                        </h4>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool btn-sm"
                                                data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                                                <i class="fas fa-minus"></i></button>
                                            <button type="button" class="btn btn-tool btn-sm" data-card-widget="remove"
                                                data-toggle="tooltip" title="Remove">
                                                <i class="fas fa-times"></i></button>
                                        </div>
                                    </div>
                                    <div class="card-body pad" style="background-color: #88babe87">
                                        <div class="d-flex align-items-end justify-content-end"
                                            style="margin-bottom: 10px;">
                                            <a id="anchorRead" href="javascript:updateItem()" class="btn btn-primary"
                                                role="button" aria-disabled="true" data-toggle="tooltip"
                                                title="Update Branch Manager status">저장<i
                                                    class="fa-solid fa-database"></i></a>
                                        </div>
                                        <div id="table-header" style="font-weight: bold;">총 지사 수: <span id="row-count">0</span></div>
                                        <div id="idTable">

                                        </div>
                                        <div id="idTableConfirm" style="margin-top: 20px;">

                                        </div>

                                        <div id="idOrderConfirm" style="margin-top: 20px;">

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-toggle="tooltip"
                        title="Update Branch Manager status" onclick="updateItem()">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-toggle="tooltip"
                        title="Exit ">Close</button>
                </div> -->
            </div>
        </div>
    </div>
    <script src="../purchase/kgardenlist.js"></script>
    <?php
    include '../includescr.php';
    ?>
    <script src="../common.js"></script>
    <script src="../header.js"></script>
    <script src="adminLogin.js"></script>
    <script>
        $("#exampleModal").on("hidden.bs.modal", function() {
            // window.location.href = "https://www.eplat.co.kr/index.php";
        });
        listPor = (por_id) => {

            dispList = (res) => {
                var js = res[0]['json']
                porTable.setData(JSON.parse(js));
                $("#idPorID").val(por_id);
                $("#idName").val(res[0]['order']);
                $("#idAddr").val(res[0]['addr']);
                $("#idRdate").val(res[0]['rdate']);
                var cnt = $(
                        "#porTableDiv > div.tabulator-footer > div.tabulator-calcs-holder > div > div:nth-child(4)")
                    .html()
                var sum = $(
                        "#porTableDiv > div.tabulator-footer > div.tabulator-calcs-holder > div > div:nth-child(5)")
                    .html()
                $("#porTableDiv > div.tabulator-footer > div.tabulator-calcs-holder > div > div:nth-child(1)").html(
                    "총합")
                $("#porTableDiv > div.tabulator-footer > div.tabulator-calcs-holder > div > div:nth-child(4)").html(
                    cvtCurrency(parseInt(cnt / 2)));
                $("#porTableDiv > div.tabulator-footer > div.tabulator-calcs-holder > div > div:nth-child(5)").html(
                    cvtCurrency(parseInt(sum / 2)));
                CallToast('New Branch Manager Updated successfully!!', "success")
            }
            dispErr = (xhr) => {
                CallToast('SPorDetailList falure!', "error")
            }
            var data = {
                id: por_id
            };
            var options = {
                functionName: 'SPorDetailList',
                otherData: {
                    id: por_id
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);
        }


        document.getElementById("idGrade").addEventListener("change", function() {
            // 선택된 옵션 가져오기
            var selectedOption = this.options[this.selectedIndex];

            // 선택된 옵션의 값(value) 가져오기
            var selectedValue = selectedOption.value;

            // 선택된 옵션의 텍스트 가져오기
            var selectedText = selectedOption.text;

            confirmList(selectedText);
        });
            document.getElementById("idGrade2").addEventListener("change", function() {
            // 선택된 옵션 가져오기
            var selectedOption = this.options[this.selectedIndex];

            // 선택된 옵션의 값(value) 가져오기
            var selectedValue = selectedOption.value;

            // 선택된 옵션의 텍스트 가져오기
            var selectedText = selectedOption.text;

            confirmList2(selectedText);
        });

        function updateRowCount(cnt) {
            // var count = tab.getDataCount();
            //document.getElementById("row-count").innerText = `총 학생 수: ${cnt} 명`;
            document.getElementById("table-header").innerText = `총 지사 수: ${cnt} `;
        }
        

        confirmList = (value) => {
            var item = [];
            var sel = 0;
            if (value != null) {
                if (value == "승인")
                    sel = 1;
                else if (value == "미승인")
                    sel = 2;
                else
                    sel = 3;
            }

            var data = {
                num: sel
            };

            dispList3 = (resp) => {
                resp.forEach(el => {
                    var jarr = {
                        "id": el['id'],
                        "name": el['name'],
                        "role": el['role'] == 1 ? "지사장" : (el['role'] == 2 ? "원관리" : "가맹관리"),
                        "mobile": el['mobile'],
                        "addr": el['addr'],
                        "zipcode": el['zipcode'],
                        "password": el['password'],
                        "rdate": el['rdate'],
                        "confirm": el['confirm'] == 1 ? "승인" : "미승인",
                    }
                    item.push(jarr);
                });
                table.clearData()
                table.setData(item);
                var count = table.getDataCount();
                updateRowCount(count);


                CallToast('SShowConfirm successfully !!', "success")
            }
            dispErr3 = (xhr) => {
                CallToast('SShowConfirm falure!', "error")
            }

            var options = {
                functionName: 'SShowConfirm',
                otherData: {
                    num: sel
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList3, dispErr3);

            var deleteIcon = function(cell, formatterParams) { //plain text value
                return "<i class='fa fa-trash'></i>";
            };
        }

        confirmList2 = (value) => {
            var item = [];
            var sel = 0;
            if (value != null) {
                if (value == "전체")
                    sel = 0;
                else if (value == "지사관리")
                    sel = 1;
                else if (value == "원관리" )
                    sel = 2;
                else if (value == "가맹관리" )  
                    sel = 3;
            }

            var data = {
                num: sel
            };

            dispList3 = (resp) => {
                resp.forEach(el => {
                    var jarr = {
                        "id": el['id'],
                        "name": el['name'],
                        "role": el['role'] == 1 ? "지사장" : (el['role'] == 2 ? "원관리" : "가맹관리"),
                        "mobile": el['mobile'],
                        "addr": el['addr'],
                        "zipcode": el['zipcode'],
                        "password": el['password'],
                        "rdate": el['rdate'],
                        "confirm": el['confirm'] == 1 ? "승인" : "미승인",
                    }
                    item.push(jarr);
                });
                table.clearData()
                table.setData(item);
                var count = table.getDataCount();
                updateRowCount(count);


                CallToast('SShowConfirm successfully !!', "success")
            }
            dispErr3 = (xhr) => {
                CallToast('SShowConfirm falure!', "error")
            }

            var options = {
                functionName: 'SShowConfirm2',
                otherData: {
                    num: sel
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList3, dispErr3);

            var deleteIcon = function(cell, formatterParams) { //plain text value
                return "<i class='fa fa-trash'></i>";
            };
        }

        orderList = () => {
            items = [];
            var data = {
                id: "manager"
            };

            dispList = (resp) => {
                CallToast('New Branch Manager Updated successfully!!', "success")
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
                });
                table1.clearData()
                table1.setData(items);
            }
            dispErr = (xhr) => {
                CallToast('New Branch Manager Update falure!', "error")
            }

            var options = {
                functionName: 'SShowOrderList',
                otherData: {
                    data
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);

            var deleteIcon = function(cell, formatterParams) { //plain text value
                return "<i class='fa fa-trash'></i>";
            };
        }

        updateItem = () => {
            items = [];
            var item = table.getSelectedData();

            item.forEach(el => {

                var jarr = {
                    "id": el['id'],
                    "name": el['name'],
                    "mobile": el['mobile'],
                    "addr": el['addr'],
                    "zipcode": el['zipcode'],
                    "password": el['password'],
                    "rdate": el['rdate'],
                    "confirm": el['confirm'] == "승인" ? 1 : 0,
                }
                items.push(jarr);

            })

            var data = {
                "item": items
            }

            dispList = (resp) => {
                confirmList("전체");
                CallToast('New Branch Manager Update Success!', "success")
            }
            dispErr = (xhr) => {
                CallToast('New Branch Manager Update falure!', "error")
            }

            var options = {
                functionName: 'SShowConfirmUpdate',
                otherData: {
                    items
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);

        }

        ConfirmPOR = () => {

            var data = {
                porid: $("#idPorID").val(),
                id: user
            };

            dispList = (resp) => {
                confirmList("전체");
                CallToast('구매의뢰서 처리  완료!', "success")
            }
            dispErr = (xhr) => {
                CallToast('구매의뢰서 처리  !', "error")
            }

            var options = {
                functionName: 'SShowConfirmUpdatePOR',
                otherData: {
                    data
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);
        }
    </script>
</body>

</html>