<!DOCTYPE html>
<html lang="utf-8">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    include "../header.php";
    ?>
    <?php
    include "../include.php";
    ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css">

    <link href="../common.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/alasql@4"></script>

    <title>학생등록관리</title>
</head>

<body style="background-color: #f4f6f9">
    <div class="p-3">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 id="idHead">학생등록 관리리스트</h5>
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
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-info">
                        <div class="card-header" style="background-color: #ec8a95">
                            <h3 class="card-title">
                                <div class="input-group input-group-sm mb-3">

                                    <span class="d-flex badge bg-light text-dark align-items-center">유치원 선택</span>
                                    &nbsp;
                                    <select class="form-select form-control-sm" id="idKgarden"
                                        data-placeholder="Choose Items" style="width: 100px;">
                                        <option val="v0"></option>
                                        <option val="va">전체</option>

                                    </select>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span class="d-flex badge bg-light text-dark align-items-center">반선택</span>
                                    &nbsp;

                                    <select class="form-select form-control-sm" id="idClass"
                                        data-placeholder="Choose Items" style="width: 100px;">
                                        <option val="v0"></option>
                                    </select>&nbsp;&nbsp;&nbsp;&nbsp;

                                    <span class="d-flex badge bg-light text-dark align-items-center">Step 선택</span>
                                    &nbsp;
                                    <select class="form-select form-control-sm" id="idStep"
                                        data-placeholder="Choose Items" style="width: 100px;">
                                        <option val="v0"></option>
                                        <option val="va">전체</option>
                                        <option val="sb">4세-Basic</option>
                                        <option val="s1">5세-Step1</option>
                                        <option val="s2">6세-Step2</option>
                                        <option val="s3">7세-Step3</option>
                                    </select>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <!-- <button class="btn btn-outline-secondary btn-sm" type="button">반선택</button> -->

                                    &nbsp;&nbsp;&nbsp;
                                    <button class="btn btn-outline-primary" type="button" onclick="findChild()">조회
                                    </button>
                                </div>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool btn-sm m-3" data-card-widget="collapse"
                                    data-toggle="tooltip" title="Collapse">
                                    <i class="fas fa-minus"></i></button>
                                <button type="button" class="btn btn-tool btn-sm m-3" data-card-widget="remove"
                                    data-toggle="tooltip" title="Remove">
                                    <i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="card-body pad" style="background-color: #f8d7da">

                            <div id="idTable">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>
    </div>

    <?php
    include '../includescr.php';
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script src="https://unpkg.com/@pdf-lib/fontkit@0.0.4/dist/fontkit.umd.min.js"></script>
    <script src="https://unpkg.com/downloadjs@1.4.7"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script src="../common.js"></script>
    <script src="../header.js"></script>
    <!-- <script src="../libpdf.js"></script> -->
    <script>
    var tab;

    document.getElementById("idHead").innerHTML = "[ " + user + " ]" + " 학생등록 현황리스트";

    window.addEventListener('resize', function() {
        drawTable();
    })

    function drawTable() {
        var deleteIcon = function(cell, formatterParams) { //plain text value
            return "<i class='fa fa-trash'></i>";
        };

        tab = new Tabulator("#idTable", {
            height: "700px",
            layout: "fitColumns",
            selectable: true,
            columns: [{
                    title: "번호",
                    formatter: "rownum",
                    align: "center",
                    width: "10%"
                },
                {
                    title: "원명",
                    field: "owner",
                    width: "15%",
                    editor: "input",
                    headerHozAlign: "center",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true
                    }
                },
                {
                    title: "스텝",
                    field: "step",
                    width: "15%",
                    editor: "input",
                    headerHozAlign: "center",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true
                    }
                },
                {
                    title: "반이름",
                    field: "classnm",
                    width: "15%",
                    headerHozAlign: "center",
                    editor: "input",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true
                    }
                },
                {
                    title: "원아명",
                    field: "name",
                    width: "15%",
                    headerHozAlign: "center",
                    editor: "input",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true
                    }
                },
                {
                    title: "아이디",
                    field: "id",
                    width: "15%",
                    editor: false,
                    headerHozAlign: "center",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true
                    }
                },
                {
                    title: "비밀번호",
                    field: "passwd",
                    width: "15%",
                    editor: "input",
                    headerHozAlign: "center",
                    editorParams: {
                        autocomplete: "true",
                        allowEmpty: true,
                        listOnEmpty: true,
                        valuesLookup: true
                    }
                },
                // {
                //     title: "삭제",
                //     formatter: deleteIcon,
                //     width: "10%",
                //     headerHozAlign: "center",
                //     hozAlign: "center",
                //     cellClick: function(e, cell) {
                //         ChildDelete(cell.getRow())
                //     }
                // },
            ],
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        drawTable();
        showKgarden(user);
    });

    showClass = (kgarden) => {
        var data = {
            id: user,
            kgarden: kgarden
        };

        dispList = (resp) => {

            document.getElementById("idClass").innerHTML = "";

            let select = document.getElementById('idClass');
            let option = document.createElement('option');
            option.text = ''; // Set the text of the new option
            option.value = ''; // Set the value attribute (if needed)
            select.add(option);

            resp['success'].forEach(el => {
                var jarr = {
                    "classnm": el['classnm'],
                }
                let option = document.createElement('option');
                option.text = el['classnm']; // Set the text of the new option
                option.value = el['classnm']; // Set the value attribute (if needed)
                // Append the new option to the select element
                select.add(option);
            })
        }
        dispErr = (xhr) => {

        }
        var options = {
            functionName: 'SShowClassList',
            otherData: {
                id: user,
                kgarden: kgarden
            }
        };
        CallAjax("SMethods.php", "POST", options, dispList, dispErr);
    };

    showKgarden = (tid) => {
        var data = {
            id: tid
        };

        dispList = (resp) => {
            let select = document.getElementById('idKgarden');
            let option = document.createElement('option');

            resp['success'].forEach(el => {
                var jarr = {
                    "owner": el['owner'],
                }
                let option = document.createElement('option');
                option.text = el['owner']; // Set the text of the new option
                option.value = el['owner']; // Set the value attribute (if needed)
                // Append the new option to the select element
                select.add(option);
            })
            CallToast('유치원 보여주기  성공 !!', "success")
        }
        dispErr = (xhr) => {
            CallToast('유치원 보여주기 실패 !!', "error")
        }
        var options = {
            functionName: 'SShowKgardenList',
            otherData: {
                id: tid
            }
        };
        CallAjax("SMethods.php", "POST", options, dispList, dispErr);
    };

    document.getElementById("idStep").addEventListener("change", function() {
        // 선택된 옵션 가져오기
        var selectedOption = this.options[this.selectedIndex];

        // 선택된 옵션의 값(value) 가져오기
        var selectedValue = selectedOption.value;

        // 선택된 옵션의 텍스트 가져오기
        var selectedText = selectedOption.text;

        const selectOpt = $("#idKgarden").find(":selected");
        const selectArr = selectOpt.map(function() {
            return $(this).text();
        }).get();
        const kgardenmn = selectArr.join(', ')

        listStudent(kgardenmn, selectedText, 1);
        //$("#idStep").val("v0");
    });

    document.getElementById("idClass").addEventListener("change", function() {
        // 선택된 옵션 가져오기
        var selectedOption = this.options[this.selectedIndex];

        // 선택된 옵션의 값(value) 가져오기
        var selectedValue = selectedOption.value;

        // 선택된 옵션의 텍스트 가져오기
        var selectedText = selectedOption.text;

        const selectOpt = $("#idKgarden").find(":selected");
        const selectArr = selectOpt.map(function() {
            return $(this).text();
        }).get();
        const kgardenmn = selectArr.join(', ')


        listStudent(kgardenmn, selectedText, 2);
        //$("#idClass").val("v0");  
        //$("#idClassname").val(selectedText);
    });

    document.getElementById("idKgarden").addEventListener("change", function() {
        // 선택된 옵션 가져오기
        var selectedOption = this.options[this.selectedIndex];

        // 선택된 옵션의 값(value) 가져오기
        var selectedValue = selectedOption.value;

        // 선택된 옵션의 텍스트 가져오기
        var kgardenmn = selectedOption.text;

        listStudent('', kgardenmn, 3);
        showClass(kgardenmn);
        //$("#idClass").val("v0");  idKgarden
        //$("#idClassname").val(selectedText);
    });

    listStudent = (kgarden, step, sel) => {

        dispList = (res) => {
            var js = res['json'];
            tab.setData(js);

            CallToast('Student list successfully!!', "success")
        }
        dispErr = (xhr) => {
            CallToast('Student list Error!!', "error")
        }

        var options = {
            functionName: 'SShowStudentList',
            otherData: {
                id: user,
                step: step,
                sel: sel,
                kgarden: kgarden,
            }
        };
        CallAjax("SMethods.php", "POST", options, dispList, dispErr);

    }
    </script>
</body>

</html>