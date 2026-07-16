<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>매칭게임</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/js/bootstrap.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat');

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #eee;
            color: #111;
            background-image: url('../../assets/game/matching-game/game_backgrd.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-color: #f0f0f0;
            background-position: center;
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
            height: 600px;
            border-collapse: separate;
            border-spacing: 5px;
            table-layout: fixed;
        }

        td {
            width: 150px;
            height: 150px;
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
                height: 300px;
                border-collapse: separate;
                border-spacing: 4px;
            }

            td {
                width: 75px;
                height: 75px;
                border: 1px solid #ffd966;
                background-color: #ffd966;
                border-radius: 3px;
                text-align: center;
                font-size: 1.1rem;
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
            transform: translate(-50%, -50%);
            border-radius: 5px;
            box-shadow: 0 25px 50px rgb(33 33 33 / 25%);
            transition: transform .6s cubic-bezier(0.4, 0.0, 0.2, 1);
            backface-visibility: hidden;
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
            cursor: pointer;
            pointer-events: auto;
        }

        .dragged {
            user-select: none;
            opacity: 0;
            cursor: default;
        }

        .drag:hover {
            opacity: 0.5;
        }

        .container>img {
            object-fit: cover;
        }

        .image-container {
            position: relative;
        }

        .image-container::after {
            content: "";
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .droppable.droppable-hover {
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
            <tbody>
                <tr>
                    <td id="drag-1" class="drag-1 drag" oncontextmenu="return false;"></td>
                    <td id="drag-2" class="drag-2 drag" oncontextmenu="return false;"></td>
                    <td id="drag-3" class="drag-3 drag" oncontextmenu="return false;"></td>
                    <td id="drag-4" class="drag-4 drag" oncontextmenu="return false;"></td>
                </tr>
                <tr>
                    <td id="drag-5" class="drag-5 drag" oncontextmenu="return false;"></td>
                    <td id="drag-6" class="drag-6 drag" oncontextmenu="return false;"></td>
                    <td id="drag-7" class="drag-7 drag" oncontextmenu="return false;"></td>
                    <td id="drag-8" class="drag-8 drag" oncontextmenu="return false;"></td>
                </tr>
                <tr>
                    <td id="match-1" class="match-1" oncontextmenu="return false;"></td>
                    <td id="match-2" class="match-2" oncontextmenu="return false;"></td>
                    <td id="match-3" class="match-3" oncontextmenu="return false;"></td>
                    <td id="match-4" class="match-4" oncontextmenu="return false;"></td>
                </tr>
                <tr>
                    <td id="match-5" class="match-5" oncontextmenu="return false;"></td>
                    <td id="match-6" class="match-6" oncontextmenu="return false;"></td>
                    <td id="match-7" class="match-7" oncontextmenu="return false;"></td>
                    <td id="match-8" class="match-8" oncontextmenu="return false;"></td>
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