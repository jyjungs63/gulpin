let table;

var _pdfname = "";
var selectedPurchasePorId = "";
let bsort = 0,
  dsort = 0,
  ssort = 0;

function escapeAttr(value) {
  return String(value ?? "")
    .replace(/&/g, "&amp;")
    .replace(/"/g, "&quot;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}
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

    // var newDiv = $('<iframe id="pdfDiv" style="width: 100%; height: 900px"></iframe>');

    // $("#idCardPurchase").append(newDiv)

    //$('#custom-tabs-one-profile-tab').addClass('active');
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
});

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

table = new Tabulator("#idTable", {
  // 주문 선택 테이블 정의
  height: "350px",
  //data: listprice2,
  layout: "fitColumns",
  rowHeight: 40, //set rows to 40px height
  selectable: true, //make rows selectable
  columns: [
    // { title: "ID", field: "uid", width: 1lhs, editor: "input", editor: false, cellEdited: function (cell) { recal(cell); }, },
    {
      title: "단계",
      field: "grade",
      width: "15%",
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
      width: "25%",
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
      width: "15%",
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
      width: "15%",
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
      width: "20%",
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
      formatter: deleteIcon,
      width: "10%",
      hozAlign: "center",
      cellClick: function (e, cell) {
        deleteRow(cell.getRow());
      },
    },
  ],
});

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
      width: "15%",
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
      width: "25%",
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
      width: "15%",
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
      width: "15%",
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
      width: "20%",
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
      formatter: deleteIcon,
      width: "10%",
      hozAlign: "center",
      cellClick: function (e, cell) {
        //deletePlist(cell.getRow())
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
      title: "지사명",
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
      title: "배송지명",
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
      title: "배송지주소",
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
  selectedPurchasePorId = por_id;

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

    document.getElementById("idInvoieNo").value = res[0].invoice ?? "";
    document.getElementById("idRefund").value = res[0].refund ?? "";

    $("#idOrderStatus").val(res[0].confirm);

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
      ? window.origin + ""
      : window.origin;
    document.getElementById("pdfDiv").src = loc + "/assets/por/uploads/" + _pdfname;
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
  var monthPickerEnd = document.getElementById("monthPickerEnd");
  let startMoment = moment(monthPicker.value);
  let endMoment = moment(monthPickerEnd.value);
  let endOfMonth = endMoment
    .endOf("month")
    .add(1, "days")
    .format("YYYY-MM-DD");
  let startOfMonth = startMoment.startOf("month").format("YYYY-MM-DD");

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

// addPurcharseList = (res, id) => {
//   // 구매 내역을 월별 지사별 summary

//   var tbody = $("#porTable tbody");

//   $("#porTable tbody").empty();

//   var sum = 0;
//   // Create a new row
//   var arr;
//   var pricev = 0; //택배비
//   var totcnt = 0;
//   if (res.length == 2) arr = res[0]["list"];
//   else arr = res;
//   let refund;
//   let totrefund = 0;
//   let negativeValues = 0;

//   arr.forEach((ell) => {
//     var newRow = $("<tr style='margin-top:10px'>");

//     var json = JSON.parse(ell["json"]);
//     var dat = ell["rdate"];

//     if (user == "admin")
//       newRow.append("<td > " + ell["id"] + "</td>"); // branch name
//     else newRow.append("<td > " + ell["order"] + "</td>"); // 유치원 이름

//     var jarr = "";
//     var total = 0;
//     newRow.append("<td > " + dat.slice(0, 11) + "</td>");

//     var i = 1;
//     json.forEach((el) => {
//       if (el["uid"] != "") {
//         total += Number(el["total"]);
//         totcnt += Number(el["count"]);
//         jarr +=
//           "<tr><td class='nb'>" +
//           i +
//           ". &nbsp;</td>  <td class='nb'>" +
//           el["title"] +
//           "</td> <td class='nb'>" +
//           cvtCurrency(parseInt(el["price"])) +
//           "원</td> <td class='nb'>" +
//           el["count"] +
//           "개</td></tr>";
//       } else if (el["title"] == "택배비") {
//         jarr +=
//           "<tr><td class='nb'>" +
//           i +
//           ". &nbsp;</td>  <td class='nb'>" +
//           el["title"] +
//           "</td> <td class='nb'>" +
//           cvtCurrency(parseInt(el["price"])) +
//           "원</td> <td class='nb'></td></tr>";
//         pricev += Number(el["price"]);
//       }
//       i++;
//     });
//     newRow.append(
//       "<td><table class='nb' style='width:100%; margin-top:0px'>" +
//         jarr +
//         "</table></td>"
//     );
//     // 환불액

//     if (ell["refund"] == null) {
//       newRow.append("<td>" + cvtCurrency(total) + "원</td>"); // 단가
//       refund = 0;
//     } else {
//       let parts = ell["refund"].split(/\s+/);
//       refund = Number(parts[0]);
//       const bracketsContent = parts.slice(1).join(" ");

//       const regex = /:-(\d+)/g;
//       let match;

//       while ((match = regex.exec(bracketsContent)) !== null) {
//         negativeValues = negativeValues - parseInt(match[1]);
//       }

//       newRow.append(
//         "<td>" +
//           cvtCurrency(total) +
//           "원/ <br/> -" +
//           cvtCurrency(refund) +
//           "원/ <br/> " +
//           bracketsContent +
//           "</td>"
//       ); // 단가
//       totrefund = totrefund + refund;
//     }
//     newRow.append(
//       "<td> <div> " +
//         ell["addr"] +
//         "</div> <br/> <div>" +
//         ell["order"] +
//         "</div></td>"
//     ); //주소
//     let statusMap = {
//       0: "물품준비중",
//       2: "입금완료",
//       10: "배송중",
//       1: "배송완료",
//       99: "환불처리",
//       999: "환불완료",
//     };
//     // let stat = ell["confirm"] == "0" ? "물품준비중" : "발송완료";
//     let stat = statusMap[ell["confirm"]] || "물품준비중";

//     if (stat == "물품준비중" && id == "") {
//       $("#idBtDelever").removeClass("disabled");

//       newRow.append("<td> <div style='color: red'>" + stat + "</div> <br/>");
//     } else {
//       $("#idBtDelever").addClass("disabled");
//       newRow.append("<td> <div style='color: blue'>" + stat + "</div> <br/>");
//     }
//     if (user == "admin") $("#idBtDelever").removeClass("disabled");

//     //if (user == "admin" && id == "" && stat == "물품준비중")
//     if (id == "" && stat == "물품준비중")
//       newRow.append(
//         "<td><div> <a href='javascript:cancelOrder()'>구매취소<a></div></td>"
//       );
//     tbody.append(newRow);
//     sum += total;

//     if (pricev < 4500 && total > 100000)
//       $("#idBtParcel").removeClass("disabled");
//     else $("#idBtParcel").addClass("disabled");
//   });

//   //  add 택배비

//   if (res.length == 2) {
//     var newRow = $("<tr  style='background-color: yellow'>");
//     var i = 0;

//     if (id == "전지사") {
//       // 전체 조회

//       jarr = "";

//       res[1]["parcel"].forEach((el) => {
//         pricev += Number(el["price"]);

//         jarr +=
//           "<tr><td class='nb'>" +
//           i +
//           ". &nbsp;</td> <td class='nb'>" +
//           el["name"] +
//           ". &nbsp;</td> <td class='nb'>" +
//           cvtCurrency(Number(el["price"])) +
//           "원</td colspan='3'></tr>";
//         i++;
//       });

//       newRow.append("<td colspan='3'> <div><h5>택배비<h5></div> </td>");
//       newRow.append(
//         "<td><table class='nb' style='width:100%; margin-top:0px'>" +
//           jarr +
//           "</table><td colspan='2'></td></td>"
//       );
//       $("#idParcel").val(cvtCurrency(pricev));
//     } // 지사별 조회
//     else {
//       if (res[1]["parcel"].length > 0)
//         pricev = Number(res[1]["parcel"][0]["price"]);
//       $("#idParcel").val(cvtCurrency(pricev));

//       newRow.append(
//         "<td colspan='2'> <div><h7>택배비<h5></div> </td> <td> <div> <b>" +
//           cvtCurrency(pricev) +
//           "원</div><td colspan='3'></td></td>"
//       );
//     }
//     tbody.append(newRow);
//   }

//   var newRow = $("<tr  style='background-color: steelblue'>");
//   newRow.append(
//     "<td colspan='2'> <div><h5>합계<h5></div> </td> <td> <div> <b>" +
//       cvtCurrency(sum - totrefund) +
//       "원 (" +
//       cvtCurrency(totcnt + negativeValues) +
//       "   개)</div><td ><h5>총합(택배비포함)<h5></td></td>"
//   );
//   // if ( pricev == undefined)
//   //     pricev = 0;
//   // if ( sum < 100000 && pricev == 0) {
//   //     pricev += 4500;
//   // }
//   newRow.append(
//     "<td> <div> <b>" +
//       cvtCurrency(sum + pricev - refund) +
//       "원/택배비" +
//       cvtCurrency(pricev) +
//       "원(포함)</div><td colspan='2'></td></td>"
//   );

//   // Append the new row to the table body
//   tbody.append(newRow);
// };

addPurcharseList = (res, id) => {

  const tbody = $("#porTable tbody");
  const btDeliver = $("#idBtDelever");
  const btParcel = $("#idBtParcel");
  const parcelInput = $("#idParcel");
  const fallbackPorId = selectedPurchasePorId;
  selectedPurchasePorId = "";

  tbody.empty();

  let html = "";

  let sum = 0;
  let sumFiltered = 0;
  let pricev = 0;
  let totcnt = 0;
  let totrefund = 0;
  let totrefundFiltered = 0;
  let negativeValues = 0;

  let arr = (res.length == 2) ? res[0]["list"] : res;

  arr.forEach((ell) => {

    let json = JSON.parse(ell.json);
    let dat = ell.rdate;

    let jarr = "";
    let total = 0;
    let i = 1;

    json.forEach((el) => {

      if (el.uid != "") {

        total += Number(el.total);
        totcnt += Number(el.count);

        jarr += `
        <tr>
          <td class='nb'>${i}.&nbsp;</td>
          <td class='nb'>${el.title}</td>
          <td class='nb'>${cvtCurrency(parseInt(el.price))}원</td>
          <td class='nb'>${el.count}개</td>
        </tr>`;

      } else if (el.title == "택배비") {

        jarr += `
        <tr>
          <td class='nb'>${i}.&nbsp;</td>
          <td class='nb'>${el.title}</td>
          <td class='nb'>${cvtCurrency(parseInt(el.price))}원</td>
          <td class='nb'></td>
        </tr>`;

        pricev += Number(el.price);
      }

      i++;
    });

    let refund = 0;
    let refundHtml = "";
    let displayTotal = total;
    if (total < 100000) {
      displayTotal = total + 4500;
    }

    if (ell.refund == null) {

      refundHtml = cvtCurrency(displayTotal) + "원";

    } else {

      let parts = ell.refund.split(/\s+/);
      refund = Number(parts[0]);
      const bracketsContent = parts.slice(1).join(" ");

      const regex = /:-(\d+)/g;
      let match;

      while ((match = regex.exec(bracketsContent)) !== null) {
        negativeValues -= parseInt(match[1]);
      }

      refundHtml =
        cvtCurrency(displayTotal) +
        "원 / <br/> -" +
        cvtCurrency(refund) +
        "원 / <br/> " +
        bracketsContent;

      totrefund += refund;
    }

    let statusMap = {
      0: "물품준비중",
      3: "결제확인중",
      2: "입금완료",
      10: "배송중",
      1: "배송완료",
      99: "환불처리",
      999: "환불완료",
    };

    let stat = statusMap[ell.confirm] || "물품준비중";
    let statColor = stat == "물품준비중" ? "red" : "blue";

    if (stat == "물품준비중" && id == "")
      btDeliver.removeClass("disabled");
    else
      btDeliver.addClass("disabled");

    if (user == "admin")
      btDeliver.removeClass("disabled");

    const porId = ell.por_id || fallbackPorId || "";

    html += `
      <tr class="purchase-row" data-por-id="${escapeAttr(porId)}" data-status="${escapeAttr(ell.confirm)}" data-invoice="${escapeAttr(ell.invoice)}" data-refund="${escapeAttr(ell.refund)}" style="margin-top:10px; cursor:pointer">

        <td>${user == "admin" ? ell.id : ell.order}</br><span style="color:blue">(${ell.owner || ''})</span></td>

        <td>${dat.slice(0,11)}</td>

        <td>
          <table class='nb' style='width:100%'>
            ${jarr}
          </table>
        </td>

        <td>${refundHtml}</td>

        <td>
          <div>${ell.addr}</div>
          <br/>
          <div>${ell.order}</div>
        </td>

        <td>
          <div style="color:${statColor}">${stat}</div>
        </td>

        ${
          (stat == "물품준비중" && (id == "" || user == "admin"))
          ? `<td><a href='javascript:cancelOrder("${escapeAttr(porId)}")'>구매취소</a></td>`
          : "<td></td>"
        }

      </tr>
    `;

    sum += total;

    // 비admin: 물품준비중(0), 환불처리(99) 항목만 합산
    if (String(ell.confirm) == "0" || String(ell.confirm) == "99") {
      if (String(ell.confirm) == "0") {
              sumFiltered += displayTotal;
      }
      else
        totrefundFiltered += (displayTotal-refund);
    }

    if (pricev < 4500 && total > 100000)
      btParcel.removeClass("disabled");
    else
      btParcel.addClass("disabled");

  });

  // 택배비 영역

  if (res.length == 2) {

    let jarr = "";
    let i = 0;

    if (id == "전지사") {

      res[1].parcel.forEach((el) => {

        pricev += Number(el.price);

        jarr += `
        <tr>
          <td class='nb'>${i}.&nbsp;</td>
          <td class='nb'>${el.name}</td>
          <td class='nb'>${cvtCurrency(Number(el.price))}원</td>
        </tr>`;

        i++;
      });

      html += `
      <tr style="background-color:yellow">
        <td colspan="3"><h5>택배비</h5></td>
        <td>
          <table class='nb' style='width:100%'>
          ${jarr}
          </table>
        </td>
        <td colspan="2"></td>
      </tr>`;

      parcelInput.val(cvtCurrency(pricev));

    } else {

      if (res[1].parcel.length > 0)
        pricev = Number(res[1].parcel[0].price);

      parcelInput.val(cvtCurrency(pricev));

      html += `
      <tr style="background-color:yellow">
        <td colspan="2"><h5>택배비</h5></td>
        <td><b>${cvtCurrency(pricev)}원</b></td>
        <td colspan="3"></td>
      </tr>`;
    }
  }

  // if (user == "admin") {
    html += `
    <tr style="background-color:steelblue">
      <td colspan="2">
        <h5>합계</h5>
      </td>
      <td>
        <b>
        ${cvtCurrency(sum - totrefund)}원
        (${cvtCurrency(totcnt + negativeValues)}개)
        </b>
      </td>
      <td>
        <h5>총합(택배비포함)</h5>
      </td>
      <td>
        <b>
        ${cvtCurrency(sumFiltered + totrefundFiltered )}원
        </b>
      </td>
      <td colspan="2"></td>
    </tr>`;
  // } else {
  //   html += `
  //   <tr style="background-color:steelblue">
  //     <td colspan="2">
  //       <h5>합계</h5>
  //     </td>
  //     <td>
  //       <b>
  //       ${cvtCurrency(sum - totrefund)}원
  //       (${cvtCurrency(totcnt + negativeValues)}개)
  //       </b>
  //     </td>
  //     <td>
  //       <h5>총합</h5>
  //     </td>
  //     <td>
  //       <b>
  //       ${cvtCurrency(sumFiltered + totrefundFiltered )}원
  //       </b>
  //     </td>
  //     <td colspan="2"></td>
  //   </tr>`;
  // }

  tbody.html(html);

  tbody.find(".purchase-row").on("click", function () {
    const row = $(this);

    tbody.find(".purchase-row").css("background-color", "");
    row.css("background-color", "#d9edf7");

    selectedPurchasePorId = row.data("por-id") || "";

    if (selectedPurchasePorId) {
      $("#idPorList").val(selectedPurchasePorId);
    }

    $("#idOrderStatus").val(String(row.data("status")));
    $("#idInvoieNo").val(row.data("invoice") || "");
    $("#idRefund").val(row.data("refund") || "");
    document.getElementById("idBtDelever").disabled = false;
  });

  if (tbody.find(".purchase-row").length == 1) {
    tbody.find(".purchase-row").trigger("click");
  }
};

function AddDelever() {
  const selectOpt = $("#idPorList").find(":selected");
  const selectArr = selectOpt
    .map(function () {
      return $(this).text();
    })
    .get();
  const porId = selectedPurchasePorId || selectArr.join(", ");

  if (!porId) {
    CallToast("주문처리할 항목을 선택해 주세요.", "error");
    return;
  }

  const selectElement = document.getElementById("idOrderStatus");
  // 입력된 텍스트 값 가져오기
  const invoice = document.getElementById("idInvoieNo");
  const invoiceText = invoice.value;
  const refund = document.getElementById("idRefund");
  const refundText = refund.value;

  // 선택된 값 가져오기
  const selectedValue = selectElement.value;
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
      status: selectedValue,
      invoice: invoiceText,
      refund: refundText,
    },
  };

  CallAjax("SMethods.php", "POST", options, dispList, dispErr);

  //orderList();
  listPor(porId);
  document.getElementById("idBtDelever").disabled = true;
}

function cancelOrder(rowPorId) {
  const porId = rowPorId || $("#idPorList").find(":selected").map(function () {
    return $(this).text();
  }).get().join(", ");

  dispList = (resp) => {
    CallToast("주문 취소 성공!!", "success");
    if (document.getElementById("pdfDiv"))
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

  listPor(porId);
}

document.getElementById("idPorList").addEventListener("change", function () {
  // 개별 구매 의뢰서 내용 보기
  // 선택된 옵션 가져오기

  document.getElementById("idInvoieNo").value = "";
  document.getElementById("idRefund").value = "";
  document.getElementById("idParcel").value = "";

  var selectedOption = this.options[this.selectedIndex];

  // 선택된 옵션의 값(value) 가져오기
  var selectedValue = selectedOption.value;

  // 선택된 옵션의 텍스트 가져오기
  var selectedText = selectedOption.text;

  listPor(selectedText);
  document.getElementById("idBtDelever").disabled = false;
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

  let startMoment = moment(monthPicker.value);
  let endMoment = moment(document.getElementById("monthPickerEnd").value);

  if (endMoment.isBefore(startMoment)) {
    document.getElementById("monthPickerEnd").value = monthPicker.value;
    endMoment = startMoment.clone();
  }

  let startOfMonth = startMoment.startOf("month").format("YYYY-MM-DD");
  let endOfMonth = endMoment.endOf("month").format("YYYY-MM-DD");

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

  document.getElementById("idInvoieNo").value = "";
  document.getElementById("idRefund").value = "";
  document.getElementById("idParcel").value = "";

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
document.getElementById("monthPickerEnd").value = formatMonth();

var monthPickerEnd = document.getElementById("monthPickerEnd");
monthPickerEnd.addEventListener("input", function (evt) {
  let startMoment = moment(monthPicker.value);
  let endMoment = moment(monthPickerEnd.value);

  if (endMoment.isBefore(startMoment)) {
    CallToast("종료월은 시작월 이후여야 합니다.", "error");
    monthPickerEnd.value = monthPicker.value;
    return;
  }

  let startOfMonth = startMoment.startOf("month").format("YYYY-MM-DD");
  let endOfMonth = endMoment.endOf("month").format("YYYY-MM-DD");

  var selectElement = document.getElementById("idPorBranch");
  var selectedOption = selectElement.options[selectElement.selectedIndex];
  var bname = selectedOption.text;

  if (bname == "") {
    if (getUser() == "admin") bname = "전지사";
    else bname = "전유치원";
  }

  listPorRange(startOfMonth, endOfMonth, bname, getUser());
});

// ═══════════════════════════════════════════════════
// 구매 내역 테이블 → Excel 저장
// ═══════════════════════════════════════════════════
function exportPorTableExcel() {
  var table = document.getElementById("porTable");
  if (!table || table.rows.length <= 1) {
    CallToast("출력할 데이터가 없습니다.", "error");
    return;
  }

  var rows = table.querySelectorAll("tr");
  var data = [];

  rows.forEach(function (row) {
    var cells = row.querySelectorAll("th, td");
    var rowData = [];
    var colCount = cells.length > 1 ? cells.length - 1 : cells.length;
    for (var i = 0; i < colCount; i++) {
      var text = cells[i].innerText.replace(/\s+/g, " ").trim();
      rowData.push(text);
    }
    if (rowData.some(function (c) { return c !== ""; })) {
      data.push(rowData);
    }
  });

  var ws = XLSX.utils.aoa_to_sheet(data);

  if (data.length > 0) {
    var colWidths = data[0].map(function (_, ci) {
      var maxLen = 0;
      data.forEach(function (row) {
        var len = (row[ci] || "").length;
        if (len > maxLen) maxLen = len;
      });
      return { wch: Math.max(maxLen + 2, 12) };
    });
    ws["!cols"] = colWidths;
  }

  var wb = XLSX.utils.book_new();
  var monthVal = document.getElementById("monthPicker").value || "전체";
  XLSX.utils.book_append_sheet(wb, ws, "구매내역");
  XLSX.writeFile(wb, "구매내역_" + monthVal + ".xlsx");
}

// ═══════════════════════════════════════════════════
// 구매 내역 테이블 → PDF 저장
// ═══════════════════════════════════════════════════
function exportPorTablePdf() {
  var table = document.getElementById("porTable");
  if (!table || table.rows.length <= 1) {
    CallToast("출력할 데이터가 없습니다.", "error");
    return;
  }

  var btn = document.getElementById("idBtExportPdf");
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> 생성중...';

  html2canvas(table, {
    scale: 2,
    useCORS: true,
    backgroundColor: "#ffffff",
    scrollY: -window.scrollY
  }).then(function (canvas) {
    var jsPDF = window.jspdf.jsPDF;
    var imgW = 297;
    var imgH = (canvas.height * imgW) / canvas.width;
    var pageH = 210;

    var pdf = new jsPDF({ unit: "mm", format: "a4", orientation: "landscape" });

    var posY = 5;
    var remaining = imgH;

    while (remaining > 0) {
      var sliceH = Math.min(pageH - posY - 5, remaining);
      var srcY = (imgH - remaining) * (canvas.height / imgH);
      var srcH = sliceH * (canvas.height / imgH);

      var sliceCanvas = document.createElement("canvas");
      sliceCanvas.width = canvas.width;
      sliceCanvas.height = srcH;
      sliceCanvas.getContext("2d").drawImage(canvas, 0, srcY, canvas.width, srcH, 0, 0, canvas.width, srcH);

      pdf.addImage(sliceCanvas.toDataURL("image/jpeg", 0.95), "JPEG", 5, posY, imgW - 10, sliceH);
      remaining -= sliceH;

      if (remaining > 0) {
        pdf.addPage();
        posY = 5;
      }
    }

    var monthVal = document.getElementById("monthPicker").value || "전체";
    pdf.save("구매내역_" + monthVal + ".pdf");
  }).catch(function (e) {
    CallToast("PDF 생성 오류: " + e.message, "error");
  }).finally(function () {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-file-pdf"></i> PDF';
  });
}
