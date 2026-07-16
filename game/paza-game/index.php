<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>파자게임</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/js/bootstrap.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat');

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #eee;
            color: #111;
            /* 배경 이미지 경로 설정 */
            background-image: url('../../assets/game/paza-game/game_backgrd.jpg');

            /* 배경 이미지를 꽉 채우도록 설정 */
            background-size: cover;

            /* 배경 이미지를 고정시키거나 스크롤에 따라 이동시킬 수 있음 */
            background-attachment: fixed;

            /* 배경 색상을 설정할 수도 있음 (이미지 로딩 중이거나 설정한 이미지가 로딩되지 않았을 때 보여짐) */
            background-color: #f0f0f0;

            /* 배경 이미지를 가운데 정렬할 수도 있음 */
            background-position: center;

            /* 다양한 설정을 적용할 수 있음 */
            /* 예를 들면, background-repeat: no-repeat; 는 배경 이미지를 반복하지 않도록 설정 */
        }

        .score {
            font-family: monospace;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 0.25rem;
            margin: 1rem;
            position: relative;
            transition: opacity 0.2s;
        }

        table {
            width: 600px;
            /* 테이블의 전체 너비를 설정합니다. */
            height: 600px;
            /* 테이블의 전체 높이를 설정합니다. */
            border-collapse: separate;
            border-spacing: 5px;
            /* 셀 사이의 간격을 설정합니다. */
            table-layout: fixed;
        }

        td {
            width: 150px;
            /* 각 셀의 너비를 설정합니다. */
            height: 150px;
            /* 각 셀의 높이를 설정합니다. */
            border: 1px solid #ffd966;
            background-color: #ffd966;
            border-radius: 7px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            font-family: monospace;
            word-break: break-all;
        }



        @media (max-width: 767px) {
            table {
                width: 300px;
                /* 테이블의 전체 너비를 설정합니다. */
                height: 300px;
                /* 테이블의 전체 높이를 설정합니다. */
                border-collapse: separate;
                border-spacing: 4px;
                /* 셀 사이의 간격을 설정합니다. */

            }

            td {
                width: 75px;
                /* 각 셀의 너비를 설정합니다. */
                height: 75px;
                /* 각 셀의 높이를 설정합니다. */
                border: 1px solid #ffd966;
                background-color: #ffd966;
                border-radius: 3px;
                text-align: center;
                font-size: 1.1rem;
                /* 텍스트를 가운데 정렬합니다. */
            }

            .score {
                font-family: monospace;
                text-align: center;
                font-size: 1.2rem;
                font-weight: bold;
                letter-spacing: 0.25rem;
                margin: 1rem;
                position: relative;
                transition: opacity 0.2s;
            }


        }

        #game {
            position: absolute;
            top: 50%;
            left: 50%;
            /* background-color: rgb(255, 239, 66, 0.7); */
            transform: translate(-50%, -50%);
            border-radius: 5px;
            box-shadow: 0 25px 50px rgb(33 33 33 / 25%);
            /* background: linear-gradient(135deg, #6f00fc 0%, #fc7900 50%, #fcc700 100%); */
            transition: transform .6s cubic-bezier(0.4, 0.0, 0.2, 1);
            backface-visibility: hidden;
            /* background-color: transparent; */
        }

        #play-again-btn {
            position: absolute;
            top: -0.5rem;
            left: 50%;
            margin-left: -50px;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background-color: #aa3f5f;
            border: 5px double #fff;
            border-radius: 14px;
            padding: 8px 10px;
            outline: none;
            letter-spacing: .05em;
            cursor: pointer;
            display: none;
            opacity: 0;
            transition: opacity 0.5s, transform 0.5s, background-color 0.2s;
        }

        #play-again-btn:hover {
            background-color: #333;
        }

        #play-again-btn:active {
            background-color: #555;
        }

        #play-again-btn.play-again-btn-entrance {
            opacity: 1;
            transform: translateX(6rem);
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .dragged {
            user-select: none;
            opacity: 0.3;
            cursor: default;
        }

        .drag:hover {
            opacity: 0.5;
        }

        .container>img {
            /* width: 100%;
            height: 100%; */
            object-fit: cover;
            /* This ensures the image fills the container without stretching */
        }

        .image-container {
            position: relative;
        }

        .image-container::after {
            content: "";
            display: none;
            /* 이 부분이 클릭 시 팝업 메뉴가 나타나는 것을 막는 역할을 합니다. */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* 클릭시 나타나는 팝업 메뉴를 가리는 레이어의 배경색입니다. */
            z-index: 9999;
        }

        .droppable.droppable-hover {
            /* background-color: #bee3f0; */
            border-width: 5px;
            transform: scale(1.5);
        }
    </style>
</head>

<body>

    <div id="game" class="table-responsive">
        <section class="score">
            <span class="correct">0</span>/<span class="total">0</span>
            <button id="play-again-btn">Play Again</button>
        </section>
        <table class="table">
            <!-- <tbody>
                <tr>
                    <td id="match-1" class="match-1"></td>
                    <td id="match-2" class="match-2"></td>
                    <td id="match-3" class="match-4"></td>
                    <td id="match-4" class="match-4"></td>
                </tr>
                <tr>
                    <td id="match-5" class="match-5"></td>
                    <td id="idcover" class="table-active" colspan="2" rowspan="2"></td>
                    <td id="match-6" class="match-6" colspan="2"></td>
                </tr>

                <tr>
                    <td id="drag-5" class="drag-5 drag"></td>
                    <td id="drag-6" class="drag-6 drag"></td>
                </tr>

                <tr>
                    <td id="drag-4" class="drag-4 drag"></td>
                    <td id="drag-3" class="drag-3 drag"></td>
                    <td id="drag-2" class="drag-2 drag"></td>
                    <td id="drag-1" class="drag-1 drag"></td>
                </tr>

            </tbody> -->
            <tbody>
                <tr>
                    <td id="drag-1" class="drag-1 drag" oncontextmenu="return false;"></td>
                    <td id="drag-2" class="drag-2 drag" oncontextmenu="return false;"></td>
                    <td id="drag-3" class="drag-3 drag" oncontextmenu="return false;"></td>
                    <td id="drag-4" class="drag-4 drag" oncontextmenu="return false;"></td>
                </tr>
                <tr>
                    <td id="drag-5" class="drag-5 drag" oncontextmenu="return false;"></td>
                    <td id="idcover" class="table-active" colspan="2" rowspan="2" oncontextmenu="return false;">
                    </td>
                    <td id="drag-6" class="drag-6 drag" colspan="2 oncontextmenu=" return false;"></td>
                </tr>
                <tr>
                    <td id="match-1" class="match-1" oncontextmenu="return false;"></td>
                    <td id="match-2" class="match-2" colspan="2" oncontextmenu="return false;"></td>
                </tr>
                <tr>
                    <td id="match-3" class="match-3" oncontextmenu="return false;"></td>
                    <td id="match-4" class="match-4" oncontextmenu="return false;"></td>
                    <td id="match-5" class="match-5" oncontextmenu="return false;"></td>
                    <td id="match-6" class="match-6" oncontextmenu="return false;"></td>
                </tr>
            </tbody>
        </table>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/UAParser.js/0.7.31/ua-parser.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alasql/4.2.2/alasql.min.js"></script>
    <script src="../js/common.js"></script>
    <script src="items.js"></script>
    <script src="game.js"></script>

</body>
<script>
    // if (getUser() == "" || getUser() == undefined)
    //     window.location.href = "../index.php";
</script>

</html>