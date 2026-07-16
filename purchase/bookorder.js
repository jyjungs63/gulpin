let table;

var _pdfname = "";
let bsort = 0,
  dsort = 0,
  ssort = 0;
let students = [
  // "all",
  // "김민준",
  // "박서준",
  // "이도윤",
  // "최예준",
  // "정시우",
  // "윤하준",
  // "장지호",
  // "조주원",
  // "한지후",
];
$(document).ready(function (e) {
  //$("#cardDest").hide();
  $("#cardPDF").hide();

  if (user == "admin") {
    $("#custom-tabs-one-home-tab").parent().hide();
    $("#custom-tabs-one-home").hide();
    $("#custom-tabs-one-profile-tab").tab("show");
    $("#cardMain").show();

    $("#cardDest").remove(); // remove 주문 창
    $("#cardPDF").remove();
    $("#idSecDiv").empty();
  }
});

var deleteIcon = function (cell, formatterParams) {
  //plain text value
  return "<i class='fa fa-trash'></i>";
};

document.addEventListener("DOMContentLoaded", function () {
  //table2.hideColumn("No");
  orderList(null);
  addrList(); // display 주소록
  studentList();
});

function studentList() {
  dispList = (resp) => {
    students.push("all");
    if (resp.length > 0) {
      resp.forEach((el) => {
        students.push(el["name"]);
      });

      CallToast("SstudentList success!!", "success");
    } else CallToast("SstudentList Not exist", "error");
  };
  dispErr = () => {
    //alert(error);
    CallToast("SstudentList Error", "error");
  };

  var options = {
    functionName: "SstudentList",
    otherData: {
      role: 30, // not using current
      tid: user,
    },
  };

  CallAjax("SMethods.php", "POST", options, dispList, dispErr);
}

function addrList() {
  var items = [];
  var data = {
    role: 2,
    id: user,
  };

  dispList = (resp) => {
    var i = 1;
    var items = [];
    if ("success" in resp) {
      resp["success"].forEach((el) => {
        var jarr = {
          No: el["id"],
          name: el["name"],
          owner: el["owner"],
          mobile: el["mobile"],
          addr: el["addr"],
          zipcode: el["zipcode"],
          rdate: el["rdate"],
        };
        items.push(jarr);
        i++;
      });
      table2.clearData();
      table2.setData(items);
      CallToast("SShowAddr success!!", "success");
    } else CallToast("SShowAddr Error", "error");
  };
  dispErr = () => {
    //alert(error);
    CallToast("SShowAddr Error", "error");
  };

  var options = {
    functionName: "SShowAddr",
    otherData: {
      role: 2, // not using current
      id: user,
    },
  };

  CallAjax("SMethods.php", "POST", options, dispList, dispErr);
}

let rowStudents = {};

function deleteStudent(rowId, student) {
  if (rowStudents[rowId]) {
    rowStudents[rowId] = rowStudents[rowId].filter((s) => s !== student);
    const row = table.getRow(rowId);
    if (row) {
      row.reformat();
    } else {
      console.error(`Row with ID ${rowId} not found.`);
    }
    updateStudentCount(rowId);
  }
}

table = new Tabulator("#idTable", {
  // 주문 선택 테이블 정의
  height: "350px",
  //data: listprice2,
  layout: "fitColumns",
  rowHeight: 40, //set rows to 40px height
  selectable: true, //make rows selectable
  index: "uid", // "uid" 필드를 고유 키로 설정
  columns: [
    // { title: "ID", field: "uid", width: 1lhs, editor: "input", editor: false, cellEdited: function (cell) { recal(cell); }, },
    { title: "ID", field: "uid", visible: false },
    {
      title: "단계",
      field: "grade",
      width: "10%",
      editor: "list",
      editor: false,
      headerHozAlign: "center",
      editorParams: {
        autocomplete: "true",
        allowEmpty: true,
        listOnEmpty: true,
        valuesLookup: true,
      },
    },
    {
      title: "품명",
      field: "title",
      sorter: "number",
      width: "15%",
      editor: false,
      headerHozAlign: "center",
      bottomCalcParams: {
        precision: 0,
      },
    },
    {
      title: "단가(원)",
      field: "price",
      sorter: "number",
      width: "10%",
      editor: false,
      headerHozAlign: "center",
      formatter: "money",
      formatterParams: {
        thousand: ",",
        precision: 0,
      },
    },
    {
      title: "수량(개)",
      field: "count",
      editor: "input",
      width: "10%",
      headerHozAlign: "center",
      validator: "min:0",
      editor: false,
      editorParams: {
        min: 0,
        max: 500,
        step: 1,
        elementAttributes: {
          type: "number",
        },
      },
      bottomCalc: "sum", // sum을 사용하여 합계를 계산
      bottomCalcFormatter: "money",
      bottomCalcFormatterParams: {
        formatter: "money",
        precision: 0,
        thousand: ",",
      },
      formatter: function (cell) {
        const rowId = cell.getRow().getData().uid;
        let count = (rowStudents[rowId] || []).length; // 학생 수를 카운트하여 표시
        if (count < 1) count = "";
        return `<span class="student-count">${count}</span>`;
      },
    },
    {
      title: "합계(원)",
      field: "total",
      editor: "input",
      formatter: "money",
      headerHozAlign: "center",
      editor: false,
      width: "10%",
      formatterParams: {
        thousand: ",",
        precision: 0,
      },
      editorParams: {
        elementAttributes: {
          type: "number",
        },
      },
      bottomCalc: "sum",
      bottomCalcFormatter: "money",
      bottomCalcFormatterParams: {
        formatter: "money",
        precision: 0,
        thousand: ",",
      },
    },
    {
      title: "학생 선택",
      field: "studentSelect",
      width: "10%",
      editor: "select",
      width: 120,
      editorParams: {
        values: students,
        defaultValue: "학생선택", // 첫 번째 값을 기본값으로 설정
      },
    },
    {
      title: "선택된 학생 목록",
      field: "selectedStudents",
      //width: "auto",
      width: "30%",
      formatter: function (cell) {
        const rowId = cell.getRow().getData().uid;
        const selectedStudents = rowStudents[rowId] || [];

        return selectedStudents
          .map(
            (student) =>
              `<span class="student-item" style="font-size: 9px;">
                        ${student}
                        <span class="delete-btn" style="font-size: 9px;" onclick="deleteStudent('${rowId}', '${student}')">×</span>
                    </span>`
          )
          .join("");
      },
    },
  ],
});

table.on("cellEdited", function (cell) {
  if (cell.getField() === "studentSelect") {
    const value = cell.getValue();
    const rowId = cell.getRow().getData().uid;

    if (!rowStudents[rowId]) {
      rowStudents[rowId] = [];
    }

    if (value === "all") {
      students.forEach((student) => {
        if (student !== "all" && !rowStudents[rowId].includes(student)) {
          rowStudents[rowId].push(student);
        }
      });
    } else if (value && !rowStudents[rowId].includes(value)) {
      rowStudents[rowId].push(value);
    }

    //cell.setValue("");
    cell.getRow().reformat();
    console.log(`Row ${rowId} 학생 목록:`, rowStudents[rowId]);
    updateStudentCount(rowId);
    table.selectRow(rowId);
  }
});

function updateStudentCount(rowId) {
  const row = table.getRow(rowId);
  var rowData = row.getData();
  let studentCount = (rowStudents[rowId] || []).length;
  let dividedValue = (studentCount * Number(rowData.price)).toFixed(1);

  // Update the row data
  if (studentCount < 1) {
    studentCount = "";
    dividedValue = "";
    table.updateData([
      {
        uid: rowId,
        count: studentCount,
        total: dividedValue,
        studentSelect: "",
      },
    ]);
  } else {
    table.updateData([
      {
        uid: rowId,
        count: studentCount,
        total: dividedValue,
      },
    ]);
  }
}

var table1 = new Tabulator("#idTableConfirm", {
  //구매 확정된 Table
  height: "500px",
  layout: "fitColumns",
  rowHeight: 40, //set rows to 40px height
  selectable: true, //make rows selectable
  columns: [
    //{ title: "ID", field: "uid", visible: false , editor: "input", editor: false, cellEdited: function (cell) { recal(cell); }, },
    {
      title: "단계",
      field: "grade",
      width: "10%",
      editor: "list",
      editor: false,
      headerHozAlign: "center",
      editorParams: {
        autocomplete: "true",
        allowEmpty: true,
        listOnEmpty: true,
        valuesLookup: true,
      },
    },
    {
      title: "품명",
      field: "title",
      width: "15%",
      editor: false,
      headerHozAlign: "center",
      bottomCalcParams: {
        precision: 0,
      },
      rowFormatter: function (row) {
        const data = row.getData();
        // 조건에 따라 배경색 변경
        if (data.title == "택배비") {
          row.getElement().style.backgroundColor = "#ffcccc"; // 빨간색
        }
      },
    },
    {
      title: "단가(원)",
      field: "price",
      sorter: "number",
      width: "10%",
      editor: false,
      headerHozAlign: "center",
      formatter: "money",
      formatterParams: {
        thousand: ",",
        precision: 0,
      },
    },
    {
      title: "수량(개)",
      field: "count",
      editor: "input",
      width: "10%",
      headerHozAlign: "center",
      validator: "min:0",
      editorParams: {
        min: 0,
        max: 5000, // Adjust min and max values as needed
        step: 2,
        elementAttributes: {
          type: "number",
        },
      },
      formatter: "money",
      formatterParams: {
        thousand: ",",
        precision: 0,
      },
      bottomCalc: "sum",
      bottomCalcFormatter: "money",
      bottomCalcFormatterParams: {
        formatter: "money",
        precision: 0,
        thousand: ",",
      },
      cellEdited: function (cell) {
        calsum(cell);
      },
    },
    {
      title: "합계(원)",
      field: "total",
      editor: "input",
      formatter: "money",
      headerHozAlign: "center",
      editor: false,
      width: "10%",
      formatterParams: {
        thousand: ",",
        precision: 0,
      },
      editorParams: {
        elementAttributes: {
          type: "number",
        },
      },
      bottomCalc: "sum",
      bottomCalcFormatter: "money",
      bottomCalcFormatterParams: {
        formatter: "money",
        precision: 0,
        thousand: ",",
      },
    },
    {
      title: "선택된 학생 목록",
      field: "selectedStudents",
      width: "30%",
    },
    {
      formatter: deleteIcon,
      width: "10%",
      hozAlign: "center",
      cellClick: function (e, cell) {
        deleteRow(cell.getRow());
      },
    },
  ],
});

deletePlist = (cell) => {
  var result = confirm("주문한 확정 상품을 삭제 하시겠습까?");

  var id = cell._row.data["No"];

  dispList = (resp) => {
    CallToast("주소록 삭제 성공!!", "success");
    cell.delete();
  };
  dispErr = (xhr) => {
    CallToast("주소록 삭제 실패!!", "error");
  };

  var options = {
    functionName: "SRemoveAddress",
    otherData: {
      id: id,
    },
  };

  if (result) {
    CallAjax("SMethods.php", "POST", options, dispList, dispErr);
  } else CallToast("주소지 삭제 취소 !!", "error");
};

var table2 = new Tabulator("#idTableDest", {
  // 주소 리스트 table 생성
  height: "490px",
  //data: kgardenlist,
  layout: "fitColumns",
  rowHeight: 40, //set rows to 40px height
  selectable: 1, //make rows selectable
  rowClick: function (e, row) {
    // deselect previously selected rows
    table2.deselectRow();

    // select the clicked row
    row.toggleSelect();
  },
  columns: [
    {
      title: "No",
      field: "No",
      width: "0%",
      editor: "input",
      editor: false,
      cellEdited: function (cell) {
        recal(cell);
      },
    },
    {
      title: "이름",
      field: "name",
      width: "15%",
      editor: "list",
      editor: false,
      editorParams: {
        autocomplete: "true",
        allowEmpty: true,
        listOnEmpty: true,
        valuesLookup: true,
      },
    },
    {
      title: "지사명/원명",
      field: "owner",
      width: "20%",
      editor: "list",
      editor: false,
      editorParams: {
        autocomplete: "true",
        allowEmpty: true,
        listOnEmpty: true,
        valuesLookup: true,
      },
    },
    {
      title: "주소",
      field: "addr",
      sorter: "number",
      width: "35%",
      editor: false,
      bottomCalcParams: {
        precision: 0,
      },
    },
    {
      title: "우편번호",
      field: "zipcode",
      sorter: "number",
      width: "10%",
      editor: false,
      bottomCalcParams: {
        precision: 0,
      },
    },
    {
      title: "전화",
      field: "mobile",
      sorter: "number",
      width: "7%",
      editor: false,
      bottomCalcParams: {
        precision: 0,
      },
    },
    // { title: "password", field: "password", sorter: "number", width: 250, editor: false, bottomCalcParams: { precision: 0 } },
    {
      title: "등록일",
      field: "rdate",
      sorter: "number",
      width: "7%",
      editor: false,
      bottomCalcParams: {
        precision: 0,
      },
    },
    {
      formatter: deleteIcon,
      width: "5%",
      hozAlign: "center",
      cellClick: function (e, cell) {
        deleteAddr(cell.getRow());
        //deleteRow(cell.getRow())
      },
    },
  ],
});

function deleteAddr(cell) {
  var result = confirm("주소록에서 선택한 주소를 삭제 하시겠습니까 ?");
  var id = cell._row.data["No"];

  dispList = (resp) => {
    cell.delete();
    refreshDest();
  };
  dispErr = (xhr) => {
    alert("SRemoveAddress Error " + xhr.statusText);
  };

  var options = {
    functionName: "SRemoveAddress",
    otherData: {
      id: id,
    },
  };

  if (result) {
    CallAjax("SMethods.php", "POST", options, dispList, dispErr);
  } else console.log("delete address list cancel");
}

table.on("rowSelected", function (row) {
  var rowData = row.getData();
  if (Number(rowData.count) > 0) {
    row.select();
  } else {
    row.deselect();
  }
});

listPor = (por_id) => {
  var options = {
    functionName: "SPorDetailList",
    otherData: {
      id: por_id,
    },
  };
  dispList = (res) => {
    var js = "";
    if (res.length > 0) {
      js = res[0]["json"];
    }

    addPurcharseList(res, "");

    if ($("#pdfDiv").length > 0) {
      console.log("  ");
    } else {
      var newDiv = $(
        '<iframe id="pdfDiv" style="width: 100%; height: 900px"></iframe>'
      );
      $("#idCardPurchase").append(newDiv);
    }

    _pdfname = res[0]["pdfname"];
    let loc = window.origin.includes("localhost")
      ? window.origin + "/chaitalk_home"
      : window.origin;
    document.getElementById("pdfDiv").src = loc + "/Server/uploads/" + _pdfname;
  };
  dispErr = (error) => {
    CallToast("SPorDetailList falure!", "error");
  };

  CallAjax("SMethods.php", "POST", options, dispList, dispErr);
};

globalsort = (sortobj) => {
  // 달별 구매 목록

  if ("지사/유치원명" == sortobj) bsort += 1;
  if ("날짜" == sortobj) dsort += 1;
  if ("주문상태" == sortobj) ssort += 1;

  var monthPicker = document.getElementById("monthPicker");
  let thisMoment = moment(monthPicker.value);
  let endOfMonth = moment(thisMoment)
    .endOf("month")
    .add(1, "days")
    .format("YYYY-MM-DD");
  let startOfMonth = moment(thisMoment).startOf("month").format("YYYY-MM-DD");

  var selectElement = document.getElementById("idPorBranch"); // 지사 또는 원관리
  var selectedOption = selectElement.options[selectElement.selectedIndex];
  var name = selectedOption.text;

  if (name == "") {
    name = "전지사";
    if (getUser() != "admin") name = "전유치원";
  }

  //listPorRange( startOfMonth, endOfMonth, name, "" )

  var options = {
    functionName: "SPorDetailListRangeSort",
    otherData: {
      start: startOfMonth,
      end: endOfMonth,
      id: getUser(),
      name: name,
      bsort: bsort,
      dsort: dsort,
      ssort: ssort,
      sortobj: sortobj,
    },
  };
  dispList = (res) => {
    addPurcharseList(res, id);
  };
  dispErr = (error) => {
    CallToast("SPorDetailListRangeSort falure!", "error");
  };

  CallAjax("SMethods.php", "POST", options, dispList, dispErr);
};

listPorRange = (start, end, name, id) => {
  // 달별 구매 목록

  var options = {
    functionName: "SPorDetailListRange",
    otherData: {
      start: start,
      end: end,
      id: id,
      name: name,
      //start: start.slice(0,7), end: end, id: id, name: name
    },
  };
  dispList = (res) => {
    addPurcharseList(res, id);
  };
  dispErr = (error) => {
    CallToast("SPorDetailList falure!", "error");
  };

  CallAjax("SMethods.php", "POST", options, dispList, dispErr);
};

listPorID = (id, start, end) => {
  // 달별 구매 목록

  var options = {
    functionName: "SPorDetailListRange",
    otherData: {
      start: start,
      end: end,
      id: id,
    },
  };
  dispList = (res) => {
    addPurcharseList(res["list"], id);
  };
  dispErr = (error) => {
    CallToast("SPorDetailList falure!", "error");
  };

  CallAjax("SMethods.php", "POST", options, dispList, dispErr);
};

addPurcharseList = (res, id) => {
  // 구매 내역을 월별 지사별 summary

  var tbody = $("#porTable tbody");

  $("#porTable tbody").empty();

  var sum = 0;
  // Create a new row
  var arr;
  var pricev = 0; //택배비
  var totcnt = 0;
  if (res.length == 2) arr = res[0]["list"];
  else arr = res;

  arr.forEach((ell) => {
    //res[0]['list'].forEach( ell =>  {

    var newRow = $("<tr style='margin-top:10px'>");

    var json = JSON.parse(ell["json"]);
    var dat = ell["rdate"];

    if (user == "admin")
      newRow.append("<td > " + ell["id"] + "</td>"); // branch name
    else newRow.append("<td > " + ell["order"] + "</td>"); // 유치원 이름

    var jarr = "";
    var total = 0;
    newRow.append("<td > " + dat.slice(0, 11) + "</td>");

    var i = 1;
    json.forEach((el) => {
      if (el["uid"] != "") {
        total += Number(el["total"]);
        totcnt += Number(el["count"]);
        jarr +=
          "<tr><td class='nb'>" +
          i +
          ". &nbsp;</td>  <td class='nb'>" +
          el["title"] +
          "</td> <td class='nb'>" +
          cvtCurrency(parseInt(el["price"])) +
          "원</td> <td class='nb'>" +
          el["count"] +
          "개</td></tr>";
      } else if (el["title"] == "택배비") {
        jarr +=
          "<tr><td class='nb'>" +
          i +
          ". &nbsp;</td>  <td class='nb'>" +
          el["title"] +
          "</td> <td class='nb'>" +
          cvtCurrency(parseInt(el["price"])) +
          "원</td> <td class='nb'></td></tr>";
        pricev += Number(el["price"]);
      }
      i++;
    });
    newRow.append(
      "<td><table class='nb' style='width:100%; margin-top:0px'>" +
        jarr +
        "</table></td>"
    );
    newRow.append("<td>" + cvtCurrency(total) + "원</td>"); // 단가
    newRow.append(
      "<td> <div> " +
        ell["addr"] +
        "</div> <br/> <div>" +
        ell["order"] +
        "</div></td>"
    ); //주소
    let stat = ell["confirm"] == "0" ? "발송미완료" : "발송완료";

    if (stat == "발송미완료" && id == "") {
      $("#idBtDelever").removeClass("disabled");

      newRow.append("<td> <div style='color: red'>" + stat + "</div> <br/>");
    } else {
      $("#idBtDelever").addClass("disabled");
      newRow.append("<td> <div style='color: blue'>" + stat + "</div> <br/>");
    }

    if (user == "admin" && id == "" && stat == "발송미완료")
      newRow.append(
        "<td><div> <a href='javascript:cancelOrder()'>구매취소<a></div></td>"
      );
    tbody.append(newRow);
    sum += total;

    if (pricev < 4500 && total > 100000)
      $("#idBtParcel").removeClass("disabled");
    else $("#idBtParcel").addClass("disabled");
  });

  //  add 택배비

  if (res.length == 2) {
    var newRow = $("<tr  style='background-color: yellow'>");
    var i = 0;

    if (id == "전지사") {
      // 전체 조회

      jarr = "";

      res[1]["parcel"].forEach((el) => {
        pricev += Number(el["price"]);

        jarr +=
          "<tr><td class='nb'>" +
          i +
          ". &nbsp;</td> <td class='nb'>" +
          el["name"] +
          ". &nbsp;</td> <td class='nb'>" +
          cvtCurrency(Number(el["price"])) +
          "원</td colspan='3'></tr>";
        i++;
      });

      newRow.append("<td colspan='3'> <div><h5>택배비<h5></div> </td>");
      newRow.append(
        "<td><table class='nb' style='width:100%; margin-top:0px'>" +
          jarr +
          "</table><td colspan='2'></td></td>"
      );
      $("#idParcel").val(cvtCurrency(pricev));
    } // 지사별 조회
    else {
      if (res[1]["parcel"].length > 0)
        pricev = Number(res[1]["parcel"][0]["price"]);
      $("#idParcel").val(cvtCurrency(pricev));

      newRow.append(
        "<td colspan='2'> <div><h7>택배비<h5></div> </td> <td> <div> <b>" +
          cvtCurrency(pricev) +
          "원</div><td colspan='3'></td></td>"
      );
    }
    tbody.append(newRow);
  }

  var newRow = $("<tr  style='background-color: steelblue'>");
  newRow.append(
    "<td colspan='2'> <div><h5>합계<h5></div> </td> <td> <div> <b>" +
      cvtCurrency(sum) +
      "원 (" +
      cvtCurrency(totcnt) +
      "   개)</div><td ><h5>총합(택배비포함)<h5></td></td>"
  );
  // if ( pricev == undefined)
  //     pricev = 0;
  // if ( sum < 100000 && pricev == 0) {
  //     pricev += 4500;
  // }
  newRow.append(
    "<td> <div> <b>" +
      cvtCurrency(sum + pricev) +
      "원/택배비" +
      cvtCurrency(pricev) +
      "원(포함)</div><td colspan='2'></td></td>"
  );

  // Append the new row to the table body
  tbody.append(newRow);
};

function AddDelever() {
  const selectOpt = $("#idPorList").find(":selected");
  const selectArr = selectOpt
    .map(function () {
      return $(this).text();
    })
    .get();
  const porId = selectArr.join(", ");

  dispList = (resp) => {
    CallToast("배송 처리 완료!!", "success");
    listPor(porId);
  };
  dispErr = (xhr) => {
    CallToast("배송 처리 실패!!", "error");
  };

  var options = {
    // functionName: 'SRemovPorID',
    functionName: "SAddDelever",
    otherData: {
      id: porId,
    },
  };

  CallAjax("SMethods.php", "POST", options, dispList, dispErr);

  orderList();
  listPor(porId);
}

function cancelOrder() {
  const selectOpt = $("#idPorList").find(":selected");
  const selectArr = selectOpt
    .map(function () {
      return $(this).text();
    })
    .get();
  const porId = selectArr.join(", ");

  dispList = (resp) => {
    CallToast("주문 취소 성공!!", "success");
    document.getElementById("pdfDiv").src = "";
  };
  dispErr = (xhr) => {
    CallToast("주문 취소 실패!!", "error");
  };

  var options = {
    functionName: "SRemovPorID",
    otherData: {
      id: porId,
      pdfname: _pdfname,
    },
  };

  CallAjax("SMethods.php", "POST", options, dispList, dispErr);

  orderList();
  listPor(porId);
}

document.getElementById("idPorList").addEventListener("change", function () {
  // 개별 구매 의뢰서 내용 보기
  // 선택된 옵션 가져오기
  var selectedOption = this.options[this.selectedIndex];

  // 선택된 옵션의 값(value) 가져오기
  var selectedValue = selectedOption.value;

  // 선택된 옵션의 텍스트 가져오기
  var selectedText = selectedOption.text;

  listPor(selectedText);
  $("#idBtDelever").removeClass("disabled");
  //$("#idBtParcel").addClass('disabled');
});

function refreshDest() {
  var sql;

  var items = [];
  var data = {
    role: 2,
    id: user,
  };

  dispList = (resp) => {
    var i = 1;
    var items = [];
    if ("success" in resp) {
      resp["success"].forEach((el) => {
        var jarr = {
          No: el["id"],
          name: el["name"],
          owner: el["owner"],
          mobile: el["mobile"],
          addr: el["addr"],
          zipcode: el["zipcode"],
          rdate: el["rdate"],
        };
        items.push(jarr);
        i++;
      });
      table2.clearData();
      table2.setData(items);
      CallToast("SShowAddr success!!", "success");
    } else CallToast("SShowAddr Error", "error");
  };
  dispErr = () => {
    //alert(error);
    CallToast("SShowAddr Error", "error");
  };

  var options = {
    functionName: "SShowAddr",
    otherData: {
      role: 2, // not using current
      id: user,
    },
  };

  CallAjax("SMethods.php", "POST", options, dispList, dispErr);
}

document.getElementById("idGrade").addEventListener("change", function () {
  // 구매 목록 선택
  // 선택된 옵션 가져오기
  var selectedOption = this.options[this.selectedIndex];

  // 선택된 옵션의 값(value) 가져오기
  var selectedValue = selectedOption.value;

  // 선택된 옵션의 텍스트 가져오기
  var selectedText = selectedOption.text;

  // 결과 출력
  console.log("Selected Value:", selectedValue);
  console.log("Selected Text:", selectedText);

  var items = [];
  var sql;
  // if ("전체" == selectedText)
  //   sql = "select uid, grade,title,price from ?  order by uid ";
  // else
  //   sql =
  //     'select uid, grade,title,price from ? where grade="' +
  //     selectedText +
  //     '" order by uid asc';
  // var res = alasql(sql, [listprice2]);
  if (selectedText == "전체") {
    items = listprice2;
  } else items = data[sele][selectedText];

  // res.forEach((el) => {
  //   var jarr = {
  //     uid: el["uid"],
  //     grade: el["grade"],
  //     title: el["title"],
  //     price: el["price"],
  //   };
  //   items.push(jarr);
  // });
  //table.clearData();
  var selectedRows = table.getSelectedData();
  table.clearData();
  //selectedRows.forEach(function(row) {});
  table.setData(selectedRows);
  table.selectRow();
  items.forEach(function (row) {
    table.addRow(row);
  });
  console.log(items);
});

var monthPicker = document.getElementById("monthPicker");

monthPicker.addEventListener("input", function (evt) {
  // 월을 선택할 경우 List 출력

  let thisMoment = moment(monthPicker.value);
  let endOfMonth = moment(thisMoment).endOf("month").format("YYYY-MM-DD");
  let startOfMonth = moment(thisMoment).startOf("month").format("YYYY-MM-DD");

  var selectElement = document.getElementById("idPorBranch"); // 지사 또는 원관리
  var selectedOption = selectElement.options[selectElement.selectedIndex];
  var bname = selectedOption.text;

  if (bname == "") {
    if (getUser() == "admin") bname = "전지사";
    else bname = "전유치원";
  }

  listPorRange(startOfMonth, endOfMonth, bname, getUser());
});

document.getElementById("idPorBranch").addEventListener("change", function () {
  // 개별 구매 의뢰서 내용 보기
  // 선택된 옵션 가져오기
  var selectedOption = this.options[this.selectedIndex];

  var name = selectedOption.text;
  var id = selectedOption.value; // admin or 지사명

  let thisMoment = moment(monthPicker.value);
  let endOfMonth = moment(thisMoment)
    .endOf("month")
    .add(1, "days")
    .format("YYYY-MM-DD");
  let startOfMonth = moment(thisMoment).startOf("month").format("YYYY-MM-DD");

  listPorRange(startOfMonth, endOfMonth, name, id);

  if (selectedOption.text != "전지사") {
    if (user == "admin") $("#idBtParcel").removeClass("disabled");
    else $("#idBtParcel").addClass("disabled");
  }
  $("#pdfDiv").remove();
  $("#idBtDelever").addClass("disabled");
});

AddParcel = () => {
  // 택배비 월에 해당하는 지사에 추가

  let thisMoment = moment(monthPicker.value);
  let start = moment(thisMoment).endOf("month").format("YYYY-MM-DD");
  let endOfMonth = moment(thisMoment).endOf("month").format("YYYY-MM-DD");

  var selectElement = document.getElementById("idPorBranch"); // 지사 또는 원관리
  var selectedOption = selectElement.options[selectElement.selectedIndex];
  var bname = selectedOption.text;
  var id = selectElement.value;

  var options = {
    functionName: "SPorAddParcel",
    otherData: {
      start: start,
      id: id,
      name: bname,
      price: $("#idParcel").val(),
    },
  };
  dispList = (res) => {
    listPorRange(start, endOfMonth, bname, id);
    CallToast("SPorAddParcel success!", "success");
  };
  dispErr = (error) => {
    CallToast("SPorAddParcel falure!", "error");
  };

  CallAjax("SMethods.php", "POST", options, dispList, dispErr);
};

let date = new Date();
monthPicker.value = formatMonth();
