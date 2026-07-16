<!DOCTYPE html>
<html lang="utf-8">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <?php
    include "../header.php";
    ?>
    <?php
    include "../include.php";
    ?>
    <link
        href="https://unpkg.com/tabulator-tables@5.5.0/dist/css/tabulator.min.css"
        rel="stylesheet" />
    <script
        type="text/javascript"
        src="https://unpkg.com/tabulator-tables@5.5.0/dist/js/tabulator.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/alasql/4.5.0/alasql.min.js"></script>

    <title>학생등록관리</title>
</head>

<body style="background-color: #f4f6f9">
    <div class="p-3">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 id="idHead">학원생등록 관리리스트</h5>
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
                                    <button class="btn btn-primary" type="button" style="width: 100px"
                                        onclick="listChild()">전체조회
                                        &nbsp;&nbsp;
                                    </button>
                                    <input class="form-control " id="idSchool" type="text" placeholder="학교">
                                    &nbsp;&nbsp;

                                    <input class="form-control " id="idGrade" type="text" placeholder="학년">
                                    &nbsp;&nbsp;

                                    <input class="form-control " id="idName" type="text" placeholder="학생명">
                                    &nbsp;&nbsp;
                                    <input class="form-control " id="idNick" type="text" placeholder="시작아이디(영어)"
                                        style="width: 120px" pattern="^[a-zA-Z\s]*$" required>
                                    &nbsp;&nbsp;
                                    <input class="form-control " id="idNumstudent" type="text" placeholder="원아수">
                                    &nbsp;&nbsp;
                                    <input class="form-control " id="idStartNum" type="text" placeholder="시작번호"
                                        value=1></input>
                                    &nbsp;&nbsp;
                                    <button class="btn btn-outline-primary" type="button" onclick="addChild()">원아아이디생성
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
                            <div class="d-flex align-items-end justify-content-end" style="margin-bottom: 10px;">
                                &nbsp;&nbsp;
                                <div class="form-check">
                                    <input class="form-check-input student-check" type="checkbox" id="idstudent" value="student">
                                    <label class="form-check-label" for="idstudent">전체학생보기</label>
                                </div> &nbsp;&nbsp;&nbsp;&nbsp;
                                <button class="btn btn-outline-primary" type="button" data-toggle="tooltip"
                                    title="원아 추가 하기" onclick="addClassMember(1)"><i class="fa-solid fa-user"></i>DB저장
                                </button> &nbsp;&nbsp;&nbsp;&nbsp;
                                <!-- <button class="btn btn-outline-success" type="button" data-toggle="tooltip"
                                    title="원아 추가 하기" onclick="addClassMember(2)"><i class="fa-solid fa-user"></i>DB수정
                                </button> &nbsp;&nbsp;&nbsp;&nbsp; -->
                                <!-- <button class="btn btn-success" type="button" data-toggle="tooltip" title="원아 프린트 하기"
                                    onclick="printClassMember()"><i class="fa-solid fa-print"></i>출력
                                </button> -->
                            </div>

                            <div id="idTable">

                            </div>
                        </div>
                    </div>
                    <!-- Bootstrap Modal -->
                    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">구매 리스트</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Modal 내용 -->
                                    <div id="idPurchaselist">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                                    <button type="button" class="btn btn-primary" id="listBtn">저장</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <input type="hidden" id="idZip" name="idZip" value="">
            <input type="hidden" id="idAddr" name="idAddr" value=""> -->
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
    <script src="../libpdf.js"></script>
    <script>
        var tab, tabpurchase;

        document.getElementById("idHead").innerHTML = "[ " + user + " ]" + " 학원생등록 관리리스트";

        window.addEventListener('resize', function() {
            //drawTable();
        })

        function drawTable() {

            var deleteIcon = function(cell, formatterParams) { //plain text value
                return `
                            <button class="action-btn edit-btn" title="구매리스트조회">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" title="삭제" style="color: red">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        `;
            };
            var post = function(cell, formatterParams) {
                return `
                            <button class="action-btn post-btn" title="우편">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        `;
            }
            tab = new Tabulator("#idTable", {
                height: "700px",
                layout: "fitColumns",
                selectable: false,
                columns: [{
                        title: "No",
                        formatter: "rownum",
                        hozAlign: "center",
                        width: 70
                    }, // 시리얼 번호 컬럼
                    {
                        title: "상태",
                        hozAlign: "center",
                        field: "status",
                        width: 70,
                        editor: "select", // 콤보박스 추가
                        editorParams: {
                            values: {
                                1: "등원",
                                0: "퇴원"
                            }, // 콤보박스 옵션
                        },
                        formatter: function(cell) {
                            const valueMap = {
                                1: "등원",
                                0: "퇴원"
                            };
                            const value = cell.getValue();
                            const element = cell.getElement();

                            // 값에 따라 배경색 설정
                            if (value === "1") {
                                element.style.backgroundColor = "seagreen"; // Male = 파란색
                                element.style.color = "white"; // 텍스트 색상
                            } else if (value === "0") {
                                element.style.backgroundColor = "pink"; // Female = 분홍색
                                element.style.color = "black"; // 텍스트 색상
                            } else {
                                element.style.backgroundColor = "gray"; // Other = 회색
                                element.style.color = "white"; // 텍스트 색상
                            }
                            return valueMap[cell.getValue()] || ""
                        }
                    }, // 시리얼 번호 컬럼
                    {
                        title: "학교",
                        field: "classnm",
                        width: "10%",
                        editor: "input",
                        headerHozAlign: "center"
                    },
                    {
                        title: "학년",
                        field: "step",
                        width: "5%",
                        headerHozAlign: "center",
                        editor: "input"
                    },
                    {
                        title: "학생명",
                        field: "name",
                        width: "5%",
                        headerHozAlign: "center",
                        editor: "input"
                    },
                    {
                        title: "아이디",
                        field: "id",
                        width: "5%",
                        editor: false,
                        headerHozAlign: "center"
                    },
                    {
                        title: "비밀번호",
                        field: "passwd",
                        width: "10%",
                        editor: false,
                        headerHozAlign: "center",
                        visible: false
                    },
                    {
                        title: "학부모폰",
                        field: "p_mobile",
                        width: "7%",
                        editor: "input",
                        headerHozAlign: "center"
                    },
                    {
                        title: "휴대폰",
                        field: "mobile",
                        width: "7%",
                        editor: "input",
                        headerHozAlign: "center"
                    },
                    {
                        title: "등록일",
                        field: "rdate",
                        width: "7%",
                        editor: "input",
                        headerHozAlign: "center"
                    },
                    {
                        title: "회비정보",
                        field: "etc",
                        width: "10%",
                        editor: "input",
                        headerHozAlign: "center"
                    },
                    {
                        title: "주소",
                        field: "addr",
                        width: "25%",
                        editor: "input",
                        headerHozAlign: "center",
                    },
                    {
                        title: "zip",
                        field: "zip",
                        width: "3%",
                        formatter: post,
                        editor: "input",
                        headerHozAlign: "center",
                        cellClick: function(e, cell) {
                            const postBtn = e.target.closest(".post-btn");
                            if (postBtn) {
                                execDaumPostcode1(getPreviousCell(cell));
                            }
                        }
                    },
                    {
                        title: "관리",
                        formatter: deleteIcon,
                        width: "10%",
                        headerHozAlign: "center",
                        hozAlign: "center",
                        cellClick: function(e, cell) {
                            const editBtn = e.target.closest(".edit-btn");
                            const deleteBtn = e.target.closest(".delete-btn");

                            if (editBtn) {
                                // 모달 열기
                                const name = cell.getRow().getData().name;
                                listPorStudent('2025-01-01', '2025-01-31', name, id);

                            } else if (deleteBtn) {
                                var result = confirm("Are you sure to delete ??");
                                if (result)
                                    ChildDelete(cell.getRow());
                                else
                                    alert('취소했습니다!')
                            }
                        }
                    },
                ],
                cellClick: function(e, cell) {
                    let cellname = cell.getField();
                    if (cellname == "addr")
                        execDaumPostcode('idZip', 'idAddr');
                },
            });
            // tab.on("cellClick", function(e, cell) {
            //     var fieldName = cell.getColumn().getField();
            //     if (fieldName == "addr")
            //         execDaumPostcode1(cell);
            // });



        }

        function getPreviousCell(cell) {
            const column = cell.getColumn(); // 현재 셀의 열 객체
            const prevColumn = column.getPrevColumn(); // 바로 앞 열 객체
            if (prevColumn) {
                const row = cell.getRow(); // 현재 셀의 행 객체
                const prevCell = row.getCell(prevColumn.getField()); // 앞 열의 셀 가져오기
                return prevCell // 셀 값 반환
            }
            return null; // 이전 열이 없을 경우 null 반환
        }

        tabpurchase = new Tabulator("#idPurchaselist", {
            height: "300px", // Table height
            layout: "fitColumns", // Fit columns to width of table
            columns: [{
                    title: "No",
                    field: "no",
                    formatter: "rownum",
                    hozAlign: "center",
                    width: 70
                },
                {
                    title: "Name",
                    field: "name",
                    hozAlign: "left",
                    editor: "input"
                },
                {
                    title: "Date",
                    field: "date",
                    hozAlign: "center",
                    editor: "input"
                },
                {
                    title: "List",
                    field: "list",
                    hozAlign: "left",
                    editor: "input"
                },
            ],
            //data: tableData, // Assign data to table
        });

        function execDaumPostcode1(cell) {
            new daum.Postcode({
                    oncomplete: function(data) {
                        // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다.

                        // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                        // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                        let addr = ''; // 주소 변수

                        //사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                        if (data.userSelectedType === 'R') {
                            // 사용자가 도로명 주소를 선택했을 경우
                            addr = data.roadAddress;
                        } else {
                            // 사용자가 지번 주소를 선택했을 경우(J)
                            addr = data.jibunAddress;
                        }
                        // $("#" + zip).val(data.zonecode);
                        // $("#" + addrs).val(addr);
                        // $("#" + addrs).focus();
                        cell.setValue(addr + " (" + data.zonecode + ")")
                    }
                }

            ).open();
        }

        const hobbyChecks = document.querySelectorAll('.student-check');
        hobbyChecks.forEach(check => {
            check.addEventListener('change', function() {
                const selectedHobbies = Array.from(hobbyChecks)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.nextElementSibling.textContent);

                if (selectedHobbies.length > 0) {
                    listStudent("부분", 4);
                    document.querySelector('label[for="idstudent"]').textContent = "등원학생보기";
                } else {
                    document.querySelector('label[for="idstudent"]').textContent = "전체학생보기";
                    listStudent("전체", 4);
                }
            });
        });

        var myModal = new bootstrap.Modal(document.getElementById('editModal'));

        function listPorStudent(start, end, name, id) {
            // 달별 구매 목록

            var options = {
                functionName: "SPorDetailListRange",
                otherData: {
                    start: start,
                    end: end,
                    id: user,
                    name: "전유치원",
                },
            };
            dispList = (res) => {
                data = [];
                res[0].list.forEach(list => {
                    let json = JSON.parse(list.json)
                    json.forEach(item => {
                        // if (item.selectedStudents != undefined) {
                        if (item.selectedStudents?.includes(name)) {
                            let tmp = {
                                date: list.rdate.slice(0, 11),
                                name: name,
                                list: item.title
                            }
                            data.push(tmp);
                        }
                    })
                })
                if (!myModal) {
                    myModal = new bootstrap.Modal(document.getElementById('editModal'));
                }
                tabpurchase.setData(data);
                myModal.show();

                document.getElementById('editModal').addEventListener('shown.bs.modal', () => {
                    // Update data when modal is shown
                    tabpurchase.clearData();
                    tabpurchase.setData(data);
                    myModal.show();
                });
            };
            dispErr = (error) => {
                CallToast("SPorDetailList falure!", "error");
            };

            CallAjax("SMethods.php", "POST", options, dispList, dispErr);
        };


        document.addEventListener("DOMContentLoaded", function() {
            drawTable();
            //showClassMembers("teacher1");
            //showClass(user);
        });

        function listChild() {
            listStudent("전체", 1);
        }

        function ChildDelete(cell) {

            var result = confirm("Are you sure to delete ??");

            var id = cell._row.data['id'];

            dispList = (resp) => {
                CallToast('원아 삭제 successfully!!', "success")
                cell.delete();
            }
            dispErr = (xhr) => {
                CallToast('원아 삭제 Error!!', "error")
            }

            var options = {
                functionName: 'SRemoveChild',
                otherData: {
                    id: id
                }
            };

            if (result) {
                CallAjax("SMethods.php", "POST", options, dispList, dispErr);
            } else
                CallToast("원아 삭제 취소 !!", "error");
        }

        // document.getElementById("idStudent").addEventListener("change", function() {
        //     // 선택된 옵션 가져오기
        //     var selectedOption = this.options[this.selectedIndex];

        //     // 선택된 옵션의 값(value) 가져오기
        //     var selectedValue = selectedOption.value;

        //     // 선택된 옵션의 텍스트 가져오기
        //     var selectedText = selectedOption.text;

        //     listStudent(selectedText, 1);
        // });

        document.getElementById("idClass").addEventListener("change", function() {
            // 선택된 옵션 가져오기
            var selectedOption = this.options[this.selectedIndex];

            // 선택된 옵션의 값(value) 가져오기
            var selectedValue = selectedOption.value;

            // 선택된 옵션의 텍스트 가져오기
            var classText = selectedOption.text;

            listStudent(classText, 2);
            //$("#idClass").val("v0");
            $("#idName").val(classText);
        });

        function listStudent(step, classText) {

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
                    sel: classText,
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);

        }

        function checkExistID(id) {
            var js
            dispList = (res) => {
                js = res['result'];
            }
            dispErr = (xhr) => {
                CallToast('Student list Error!!', "error")
            }
            var options = {
                functionName: 'SCheckStudentID',
                otherData: {
                    id: id,
                }
            };
            CallAjax2("SMethods.php", "POST", options, dispList, dispErr);
            return js;
        }

        document.getElementById('idNick').addEventListener('input', function(e) {
            const englishOnly = /^[a-zA-Z\s]*$/;
            if (!englishOnly.test(e.target.value)) {
                e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
            }
        });

        function addChild() {
            tab.clearData();
            var school = $("#idSchool").val();
            var grade = $("#idGrade").val();
            var name = $("#idName").val();
            var nickname = $("#idNick").val();
            var num = $("#idNumstudent").val();
            var start = Number($("#idStartNum").val());

            let isexist = checkExistID(nickname);
            if (isexist > 0) {
                start = Number(isexist) + 1;
                $("#idStartNum").val((start.toString()));
            }

            // if (isexist < 1) {
            if (school != "" && grade != "" && name != "" && nickname != "" && num != "") {

                var no = 0;
                var arr = [...Array(Number(num)).keys()];
                arr.forEach(el => {
                    no = Number(start) + el;
                    var data = {
                        classnm: school,
                        step: grade,
                        id: nickname + no,
                        passwd: nickname + no,
                        status: "1",    
                    }
                    tab.addRow(data);
                })
            } else {
                if (selectedValue == "")
                    alert(" 생성 할 학생의 정보를 입력하세요/ Step [4세-Basic, 5세-Step1, 6세-Step2, 7세-Step3]  선택해주세요 !!.");
                if (name == "")
                    alert(" 생성 할 학생의 정보를 입력하세요/  반을 입력 입력해 주세요 !!.");
                if (nickname == "")
                    alert(" 생성 할 학생의 정보를 입력하세요/ 생성할 ID를 입력해 주세요 !!.");
                if (num == "")
                    alert(" 생성 할 학생의 정보를 입력하세요/ 생성할 원아 총 수를 입력해주세요 !!.");
            }
        };

        function addClassMember(chos) { // 학생등록
            // var fun = "SinsertStudent";
            // if (chos == 2)
            //     fun = "SupdateStudent"
            fun = "SmanageStudent";
            var items = [];
            var item = tab.getRows();
            let clsname = "";
            item.forEach(el => {
                clsname = el._row.data['classnm'];
                var jarr = {
                    "classnm": el._row.data['classnm'], // 학교
                    "step": el._row.data['step'], // 학년
                    "id": el._row.data['id'],
                    "name": el._row.data['name'],
                    "passwd": el._row.data['passwd'],
                    "tid": user, //
                    "role": "30", //
                    "p_mobile": el._row.data['p_mobile'],
                    "mobile": el._row.data['mobile'],
                    "addr": el._row.data['addr'],
                    "status": el._row.data['status'],
                    "etc": el._row.data['etc'],
                }
                items.push(jarr);
            })
            var data = {
                "item": items
            };

            dispList = (resp) => {
                // let option = document.createElement('option');
                // option.text = clsname; // Set the text of the new option
                // option.value = clsname; // Set the value attribute (if needed)
                //showClass(user);
                CallToast(resp['message'], "success")
            }
            dispErr = (xhr) => {
                CallToast('New Student  Update falure !!', "error")
            }

            var options = {
                functionName: fun,
                otherData: {
                    items
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);

        }

        function showClass(tid) {

            var data = {
                id: tid
            };

            dispList = (resp) => {
                $("#idClass").empty();
                let select = document.getElementById('idClass');
                let option = document.createElement('option');
                option.text = ""; // Set the text of the new option
                option.value = ""; // Set the value attribute (if needed)
                select.add(option);

                resp['success'].forEach(el => {
                    var jarr = {
                        "classnm": el['classnm'],
                    }

                    let option = document.createElement('option');
                    option.text = el['classnm']; // Set the text of the new option
                    option.value = el['classnm']; // Set the value attribute (if needed)

                    select.add(option);
                })
                //CallToast('Disply student list successfully!!', "success")
            }

            dispErr = (xhr) => {
                //CallToast('Disply student list falure !!', "error")
            }

            var options = {
                functionName: 'SShowClassList',
                otherData: {
                    id: tid,
                    kgarden: ""
                }
            };
            CallAjax("SMethods.php", "POST", options, dispList, dispErr);
        };


        var page;

        const {
            PDFDocument,
            rgb,
            StandardFonts
        } = PDFLib;
        const black = rgb(0, 0, 0);
        const white = rgb(1, 1, 1);
        const headcol = rgb(0.85, 0.89, 0.95);
        const footcol = rgb(1.00, 0.91, 0.60);


        async function printClassMember() {
            const pdfDoc = await PDFDocument.create();
            pdfDoc.registerFontkit(fontkit)
            const fontBytes = await fetch('NanumBarunGothic.ttf').then((res) => res
                .arrayBuffer());
            const customFont = await pdfDoc.embedFont(fontBytes);
            font = customFont;

            // const customFont = await pdfDoc.embedFont(StandardFonts.TimesRoman);
            var item = tab.getData();
            var Arr = [];
            item.forEach(el => {

                var jarr = {
                    "step": el['step'],
                    "classnm": el['classnm'],
                    "name": el['name'],
                    "id": el['id'],
                    "passwd": el['passwd']
                }
                Arr.push(jarr)
            })

            let loop = 1;

            if (Arr.length > 24)
                loop = 2;
            let m = 0;
            for (var j = 0; j < loop; j++) {

                page = pdfDoc.addPage()

                const {
                    width,
                    height
                } = page.getSize()

                const fontSize = 14;

                page.setFont(customFont);
                page.setFontSize(fontSize);

                setOrigin(0.5, 27);
                pwidth = width / cm;
                half = pwidth / 2;
                let xm = 3.25;
                let ym = 1.5;
                let hcol = rgb(0.37, 0.66, 0.62); // header color
                let tcol = rgb(1.00, 0.97, 0.82); // row color

                let fs = 10;

                for (let k = 1; k < 9; k++) {
                    for (let i = 0; i < 3; i++) {
                        // if (m >= Arr.length) {
                        //     m = 0;
                        // }
                        if (m < Arr.length) {
                            drawRTextBox(0, 0, xm, ym, hcol, Arr[m]['classnm'], fs, white, "center");
                            drawRTextBox(xm, 0, xm, ym, hcol, Arr[m]['name'], fs, white, "center");

                            moveDown(ym)
                            drawRTextBox(0, 0, xm, ym, tcol, "ID: " + Arr[m]['id'], fs, black, "center");
                            drawRTextBox(xm, 0, xm, ym, tcol, "PW: " + Arr[m]['passwd'], fs, black, "center");
                            moveUp(ym)
                            moveRight(6.8);
                        }
                        m++;
                    }
                    moveLeft(6.8 * 3);
                    moveDown(ym * 2 + 0.5);
                }
            }

            const pdfBytes = await pdfDoc.save()
            const pdfBlob = new Blob([pdfBytes], {
                type: 'application/pdf'
            });

            // 생성된 PDF 다운로드
            const downloadLink = document.createElement('a');
            downloadLink.href = window.URL.createObjectURL(pdfBlob);
            downloadLink.download = 'output.pdf';
            downloadLink.click();

        }
    </script>
</body>

</html>