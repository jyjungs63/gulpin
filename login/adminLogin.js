var table, table1, porTable;
var items = [];
var myModal;
var deleteIcon = function (cell, formatterParams) {
  //plain text value
  return "<i class='fa fa-trash'></i>";
};

document.addEventListener("DOMContentLoaded", function () {
  // Get the modal element by its ID
  myModal = new bootstrap.Modal(document.getElementById("exampleModal"));

  table = new Tabulator("#idTable", {
    height: "490px",
    //data: kgardenlist,
    //layout: "fitColumns",
    rowHeight: 40, //set rows to 40px height
    selectable: true, //make rows selectable
    responsiveLayout: true,
    columns: [
      {
        formatter: "rowSelection",
        field: "check",
        width: "3%",
        titleFormatter: "rowSelection",
        hozAlign: "left",
        headerSort: false,
        cellClick: function (e, cell) {
          cell.getRow().toggleSelect();
        },
      },
      {
        title: "구분",
        field: "role",
        editor: "select",
        responsive: 0,
        width: "8%",
        editorParams: {
          // 에디터 파라미터 설정
          values: ["지사장", "원관리","가맹점"], // 콤보박스에 표시될 값들
        },
      },
      {
        title: "아이디",
        field: "id",
        width: "10%",
        editor: "input",

        editor: false,
      },
      {
        title: "이름",
        field: "name",
        width: "12%",
        editor: "input",
        responsive: 0,
        editor: false,
      },
      {
        title: "전화번호",
        field: "mobile",
        width: "10%",

        editor: "list",
        editor: false,
      },
      {
        title: "주소",
        field: "addr",
        sorter: "input",

        width: "25%",
        editor: true,
      },
      {
        title: "우편번호",
        field: "zipcode",

        width: "5%",
        editor: true,
      },
      {
        title: "비밀번호",
        field: "password",

        width: "10%",
        editor: false,
      },
      {
        title: "등록일",
        field: "rdate",

        width: "10%",
        editor: false,
      },
      {
        title: "승인",
        field: "confirm",
        editor: "select",
        responsive: 0,
        width: "8%",
        editorParams: {
          // 에디터 파라미터 설정
          values: ["승인", "미승인"], // 콤보박스에 표시될 값들
        },
      },
    ],
    responsiveLayout: true,
    responsiveLayoutCollapseStart: 768, // Hide columns when the screen width is less than 768 pixels
    responsiveLayoutCollapseEnd: 0, // Show all columns when the screen width is less than 0 pixels (never)
    responsiveLayoutCollapseFormatter: function (data) {
      // Define which columns to hide in responsive layout
      return data.columns.filter(
        (column) =>
          column.field !== "name" &&
          column.field !== "id" &&
          column.field !== "confirm"
      );
    },
  });

  porTable = new Tabulator("#porTableDiv", {
    height: "490px",
    layout: "fitColumns",
    rowHeight: 40, //set rows to 40px height
    selectable: true, //make rows selectable
    columns: [
      {
        title: "Grade",
        field: "grade",
        width: 150,
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
        title: "품명",
        field: "title",
        sorter: "number",
        width: 350,
        editor: false,
        bottomCalcParams: {
          precision: 0,
        },
      },
      {
        title: "단가",
        field: "price",
        sorter: "number",
        width: 150,
        editor: false,
        hozAlign: "right",
        formatterParams: {
          thousand: ",",
          precision: 0,
        },
      },
      {
        title: "Count",
        field: "count",
        editor: "input",
        width: 150,
        hozAlign: "right",
        validator: "min:0",
        editorParams: {
          min: 0,
          max: 1000, // Adjust min and max values as needed
          step: 2,
          elementAttributes: {
            type: "number",
          },
        },
        cellEdited: function (cell) {
          calsum(cell);
        },
        bottomCalc: "sum",
      },
      {
        title: "Total",
        field: "total",
        editor: "input",
        formatter: "money",
        hozAlign: "right",
        editor: false,
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
        bottomCalcFormatterParams: {
          formatter: "money",
          precision: 0,
          thousand: ",",
        },
      },
      {
        title: "rdate",
        field: "rdate",
        editor: "input",
        hozAlign: "right",
        editor: false,
      },
      // {
      //     formatter: deleteIcon,
      //     width: 40,
      //     hozAlign: "center",
      //     cellClick: function(e, cell) {
      //         deleteRow(cell.getRow())
      //     }
      // },
    ],
  });

  confirmList(null);
  //orderList(null);
  // Show the modal

  myModal.show();

  document.addEventListener("DOMContentLoaded", function () {
    // Your code here
    //hideAgeColumn();
    // Execute functions or perform actions that need the DOM to be ready
  });

  function hideAgeColumn() {
    //porTable.toggleColumn("grade");
    table.toggleColumn("mobile");
    table.toggleColumn("rdate");
  }

  myModal._element.addEventListener("shown.bs.modal", function () {
    // Your code here
    alert("Modal is fully loaded and shown.");
    // Execute functions or perform actions that need the modal to be ready
  });
  //   table1.on("rowClick", function (e, row) {
  //     listPor(row._row.data["por_id"]);
  //     $("#exampleModal").modal("hide");
  //   });
});
