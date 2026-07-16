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
    include "../libs/header.php";
    include "../libs/include.php";
    ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/Chart.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css"> -->

    <!-- <link href="../common.css" rel="stylesheet"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script src="https://cdn.jsdelivr.net/npm/alasql@4"></script>
    <?php
    include '../libs/includescr.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js">
    </script>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="../js/common.js"></script>
    <script src="../js/header.js"></script>    

    <title>학생등록관리</title>
    <style>
        :root {
            --kg-bg: #f3f6fb;
            --kg-panel: #ffffff;
            --kg-line: #d9e2ef;
            --kg-text: #1f2937;
            --kg-muted: #6b7280;
            --kg-primary: #2563eb;
        }

        body.kgarden-page {
            background: var(--kg-bg) !important;
            color: var(--kg-text);
        }

        .kgarden-shell {
            max-width: 1560px;
            margin: 0 auto;
            padding: 18px;
        }

        .kgarden-heading {
            margin-bottom: 14px;
        }

        .kgarden-heading h5 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .kgarden-heading .breadcrumb {
            margin-bottom: 0;
            background: transparent;
            font-size: 14px;
            justify-content: flex-end;
        }

        .kgarden-card {
            border: 1px solid var(--kg-line);
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            background: var(--kg-panel);
        }

        .kgarden-toolbar {
            background: linear-gradient(135deg, #ffffff 0%, #eef6ff 100%) !important;
            border-bottom: 1px solid var(--kg-line);
            padding: 18px;
        }

        .kgarden-form {
            display: grid;
            grid-template-columns: minmax(220px, 1.2fr) minmax(150px, 0.8fr) minmax(150px, 0.8fr);
            gap: 12px;
            align-items: end;
            width: 100%;
        }

        .kgarden-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .kgarden-field label {
            margin: 0;
            color: var(--kg-muted);
            font-size: 12px;
            font-weight: 700;
        }

        .kgarden-field input,
        .kgarden-field select {
            width: 100%;
            height: 38px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 7px 10px;
            font-size: 14px;
            background-color: #fff;
        }

        .kgarden-field input:focus,
        .kgarden-field select:focus {
            border-color: var(--kg-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.16);
        }

        .kgarden-btn {
            height: 38px;
            border-radius: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .kgarden-body {
            background: var(--kg-panel) !important;
            padding: 18px;
        }

        .kgarden-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .kgarden-chart-panel {
            border: 1px solid var(--kg-line);
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
        }

        .kgarden-chart-header {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 46px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--kg-line);
            background: #f8fafc;
            color: #111827;
            font-weight: 800;
        }

        .kgarden-chart-body {
            position: relative;
            height: 500px;
            padding: 16px;
        }

        .kgarden-chart-body canvas {
            height: 100% !important;
            width: 100% !important;
        }

        @media (max-width: 767px) {
            .kgarden-shell {
                padding: 12px;
            }

            .kgarden-heading .row {
                gap: 8px;
            }

            .kgarden-heading h5 {
                font-size: 17px;
            }

            .kgarden-heading .breadcrumb {
                float: none !important;
                justify-content: flex-end;
            }

            .kgarden-toolbar,
            .kgarden-body {
                padding: 14px;
            }

            .kgarden-form {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .kgarden-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body class="kgarden-page">
    <div class="kgarden-shell">
        <section class="kgarden-heading">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 id="idStudyStatus">학생학습현황</h5>
                    </div>
                    <div class="col-sm-6 text-right">
                        <ol class="breadcrumb justify-content-end">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:window.history.back()">이전</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="kgarden-card">
                <div class="kgarden-toolbar">
                    <div class="kgarden-form">
                        <div class="kgarden-field">
                            <label for="reportrange">기간선택</label>
                            <input id="reportrange" type="text">
                        </div>
                        <div class="kgarden-field">
                            <label for="idStep">Step선택</label>
                            <select id="idStep" data-placeholder="Choose Items">
                                <option value="v0"></option>
                                <option value="전체">전체</option>
                                <option value="basic">4세-Basic</option>
                                <option value="step1">5세-Step1</option>
                                <option value="step2">6세-Step2</option>
                                <option value="step3">7세-Step3</option>
                            </select>
                        </div>
                        <div class="kgarden-field">
                            <label for="idClass">반선택</label>
                            <select id="idClass" data-placeholder="Choose Items">
                                <option value="v0"></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="kgarden-body">
                    <div class="kgarden-actions">
                        <button class="btn btn-success kgarden-btn" type="button" data-toggle="tooltip" title="원아 프린트 하기" onclick="orderToPdf()">
                            <i class="fa-solid fa-print"></i> 출력
                        </button>
                    </div>

                    <div id="idTable" class="kgarden-chart-panel">
                        <div class="kgarden-chart-header">
                            <i class="fas fa-chart-pie"></i>
                            학습현황
                        </div>
                        <div class="kgarden-chart-body" id="revenue-chart">
                            <canvas id="revenue-chart-canvas" height="500"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <script>
    var salesChart;
    var salesChartCanvas = document.getElementById('revenue-chart-canvas').getContext('2d');
    var chartState = 0;

    var startDate;
    var endDate;
    var step;
    var clas = "";

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("idStudyStatus").textContent = getUser() + "학생학습현황";
        showClass(user);
        CallToast(name + "님 방문을 환영합니다!.", "success");
    });

    showClass = (tid) => {
        dispList = (resp) => {
            let select = document.getElementById('idClass');

            resp['success'].forEach(el => {
                let option = document.createElement('option');
                option.text = el['classnm'];
                option.value = el['classnm'];
                select.add(option);
            })
        }

        dispErr = () => {}

        var options = {
            functionName: 'SShowClassList',
            otherData: {
                id: tid
            }
        };
        CallAjax("SMethods.php", "POST", options, dispList, dispErr);
    };

    var selectElement = document.getElementById('idStep');
    selectElement.addEventListener("change", function() {

        step = selectElement.value;

        var dateRangePickerValue = $('#reportrange').val();
        var selectedDates = dateRangePickerValue.split(' ~ ');
        startDate = selectedDates[0];
        endDate = selectedDates[1];

        listStudent(step, startDate, endDate);
    });

    document.getElementById("idClass").addEventListener("change", function() {
        // 선택된 옵션 가져오기
        var selectedOption = this.options[this.selectedIndex];
        var dateRangePickerValue = $('#reportrange').val();
        var selectedDates = dateRangePickerValue.split(' ~ ');
        startDate = selectedDates[0].trim();
        endDate = selectedDates[1].trim();


        // 선택된 옵션의 값(value) 가져오기
        clas = selectedOption.value;

        // 선택된 옵션의 텍스트 가져오기
        var selectedText = selectedOption.text;

        listStudent2(clas, startDate, endDate);
    });


    listStudent = (step, start, end) => {

        dispList = (res) => {
            drawChart(res['json']);
            CallToast('Student list successfully!!', "success")
        }
        dispErr = (xhr) => {
            CallToast('Student list Error!!', "error")
        }

        var options = {
            functionName: 'SShowStudyList',
            otherData: {
                id: user,
                step: step,
                start: start,
                end: end
            }
        };
        CallAjax("SMethods.php", "POST", options, dispList, dispErr);
    }

    listStudent2 = (classnm, start, end) => {

        dispList = (res) => {
            drawChart(res['json']);
            CallToast('Student list successfully!!', "success")
        }
        dispErr = (xhr) => {
            CallToast('Student list Error!!', "error")
        }

        var options = {
            functionName: 'SShowStudyList2',
            otherData: {
                id: user,
                classnm: classnm,
                start: start,
                end: end
            }
        };
        CallAjax("SMethods.php", "POST", options, dispList, dispErr);
    }

    drawChart = (res) => {
        var lb = [];
        var bk = [];
        var dt = [];

        res.forEach(el => {
            var color = getRandomColor();
            lb.push([el['name'], '(' + el['cnt'] + ')'])
            bk.push(color);
            dt.push(el['cnt']);
        })

        var data = {
            labels: lb,
            datasets: [{
                label: '',
                data: dt,
                backgroundColor: bk,
                borderColor: bk,
                borderWidth: 1
            }]
        };
        if (step == undefined) step = "";
        if (clas == undefined) clas = "";
        if (chartState == 0) {
            salesChart = new Chart(salesChartCanvas, {
                type: 'bar',
                data: data,
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    showTooltips: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        y: {
                            min: 0
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: [getOwner() + " 학습 현황", " ", "(" + step + " - " + clas + " ) 기간 : " +
                                startDate + "~" + endDate + ""
                            ],
                            font: {
                                size: 14,
                            },
                            padding: {
                                top: 10,
                                bottom: 30
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            formatter: Math.round,
                            font: {
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: false
                        }
                    },

                },
            })
            chartState = 1;
        } else {
            salesChart.data = data;
            salesChart.options.plugins.title = {
                display: true,
                text: [getOwner() + " 학습 현황", " ", "(" + step + " - " + clas + ") 기간 : " +
                    startDate + "~" + endDate + ""
                ],
                font: {
                    size: 14,
                },
                padding: {
                    top: 10,
                    bottom: 30
                }
            };
            salesChart.update();
        }
    }

    orderToPdf = () => {

        var element = document.getElementById('revenue-chart');
        var opt = {
            //margin: [3, 0, 0, 0],
            margin: [5, 0, 0, 0],
            filename: 'myfile.pdf',
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 1
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

    var start = moment().startOf('month');
    var end = moment().endOf('month');

    function cb(start, end) {
        $('#reportrange span').html(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        locale: {
            format: 'YYYY-MM-DD', // 날짜 표시 형식
            separator: ' ~ ', // 날짜 범위 구분자
            applyLabel: '적용', // 적용 버튼 레이블
            cancelLabel: '취소', // 취소 버튼 레이블
            fromLabel: '부터', // 시작일 레이블
            toLabel: '까지', // 종료일 레이블
            customRangeLabel: '직접 선택', // 사용자 정의 범위 레이블
            weekLabel: '주', // 주 레이블
            daysOfWeek: ['일', '월', '화', '수', '목', '금', '토'], // 요일 배열
            monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'], // 월 배열
            firstDay: 0 // 주의 시작 요일 (0: 일요일, 1: 월요일, ...)
        },
        ranges: {
            '지난달': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                'month')],
            '이번달': [moment().startOf('month'), moment().endOf('month')]
        }
    }, cb);

    </script>
</body>

</html>
