<?php
$room_name = isset($_GET['room']) ? $_GET['room'] : '';
$player_name = isset($_GET['player']) ? $_GET['player'] : '';
$room_info_file = "rooms/$room_name-info.json";
$players_file = "rooms/$room_name.json";

$host_name = '';
$turn = 0;
$turns = 0;
$topic = '???';
$time_limit = 10; // デフォルトの制限時間
$players = [];

// 部屋情報の読み込み
if (file_exists($room_info_file)) {
    $room_info = json_decode(file_get_contents($room_info_file), true);
    $host_name = isset($room_info['host_name']) ? $room_info['host_name'] : '';
    $turn = isset($room_info['turn']) ? $room_info['turn'] : 0;
    $turns = isset($room_info['turns']) ? $room_info['turns'] : 0;
    $topic = isset($room_info['topic']) ? $room_info['topic'] : '';
    $time_limit = isset($room_info['time_limit']) ? $room_info['time_limit'] : 10;
}


$your_topic = '???';
if ($turn == 0) {
    $your_topic = $topic;
}



// プレイヤーリストの読み込み
if (file_exists($players_file)) {
    $players = json_decode(file_get_contents($players_file), true);
}

// 現在のプレイヤーを取得
$current_player = isset($players[$turn % count($players)]) ? $players[$turn % count($players)] : '';

// 描画中のプレイヤーか確認
$is_current_player = ($player_name === $current_player);

// ターンが終了したらview_drawings.phpにリダイレクト
if ($turn >= $turns * count($players)) {
    header("Location: view_drawings.php?room=$room_name");
    exit();
}

include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>オンライン絵しりとりゲーム</title>
    <style>
        body {
            text-align: center;
            margin: 0;
            width: 100vw;
            height: 100vh;
            background-image: url('background.png');
            background-size: cover;
            background-repeat: no-repeat;
        }


        #drawingCanvas {
            border: 2px solid #333;
            background-color: #fff;
        }

        #controls {
            margin-top: 10px;
        }

        #timer {
            font-size: 24px;
            color: red;
            margin-top: 10px;
        }

        .drawing-info {
            margin-bottom: 10px;
        }

        .drawing-info img {
            display: block;
            margin-top: 5px;
            max-width: 200px;
            max-height: 200px;
        }
    </style>
</head>

<body>
    <div id="gameContainer">
        <h1>現在のプレイヤー: <?php echo htmlspecialchars($current_player); ?> が描画中</h1>
        <p>頭文字: <?php echo htmlspecialchars($your_topic); ?></p>
        <?php if ($is_current_player) : ?>
            <p>あなたの番です！絵を描いてください。</p>
            <canvas id="drawingCanvas" width="800" height="600"></canvas>
            <div id="controls">
                <button onclick="clearCanvas()">クリア</button>
                <button onclick="setWhite()">消しゴム</button>
                <button onclick="setPenColor()">ペン</button>
                <label for="colorPicker">色選択:</label>
                <input type="color" id="colorPicker" value="#000000">
                <label for="brushSize">ブラシサイズ:</label>
                <input type="range" id="brushSize" min="1" max="50" value="5">
                <label for="drawingTitle">描いた絵:</label>
                <input type="text" id="drawingTitle" autocomplete="off">
                <h3 id="validationResult"></h3>

            </div>
            <button onclick="saveAndNext()">次のプレイヤーへ</button>
            <p id="timer"></p>
        <?php else : ?>
            <p>順番を待っています...</p>
            <script>
                // 定期的にチェックして、順番を待っている間は画面をリロードする
                setInterval(function() {
                    window.location.reload();
                }, 3000); // 3秒ごとにチェック
            </script>
        <?php endif; ?>
        <p>これまでの描画:</p>
        <?php
        $drawings_file = "rooms/{$room_name}/{$room_name}-drawings.json";
        $drawings = [];
        if (file_exists($drawings_file)) {
            $drawings = json_decode(file_get_contents($drawings_file), true);
        }

        if (!empty($drawings)) {
            foreach ($drawings as $drawing) {

                echo '<div class="gallery" style="border: 1px solid black; border-radius: 10px;">';
                echo '<p>プレイヤー: ' . htmlspecialchars($drawing['player']) . '</p>';
                echo '<p>描いた絵: ' . htmlspecialchars($drawing['description']) . '</p>';
                echo '<img src="' . htmlspecialchars($drawing['drawing']) . '" alt="描いた絵" style="max-width: 200px; max-height: 200px;">';
                echo '</div>';
            }
        } else {
            echo '<p>まだ絵がありません。</p>';
        }
        ?>
        <form action="submit_drawing.php" method="POST">
            <input type="hidden" name="drawing_data" id="drawing_data">
            <input type="hidden" name="drawing_title" id="drawing_title">
        </form>
    </div>
    <script>
        const canvas = document.getElementById('drawingCanvas');
        const ctx = canvas.getContext('2d');
        const colorPicker = document.getElementById('colorPicker');
        const brushSize = document.getElementById('brushSize');
        const timerElement = document.getElementById('timer');
        let validationHiraganaResult = false;
        let drawing = false;
        let drawingPermission = true;
        let timeLeft = <?php echo $time_limit; ?>;

        function preventScroll(event) {
            event.preventDefault();
        }

        canvas.addEventListener('mousedown', (e) => {
            console.log("ドロパミ");
            console.log(drawingPermission);
            if (drawingPermission) {

                drawing = true;
                ctx.beginPath();
                ctx.moveTo(e.offsetX, e.offsetY);
            }
        });
        canvas.addEventListener('touchstart', (e) => {
            if (drawingPermission) {
                drawing = true;
                ctx.beginPath();
                touchX = event.changedTouches[0].pageX;
                touchY = event.changedTouches[0].pageY;
                preventScroll(e);
                ctx.moveTo(touchX, touchY);
            }
        });


        canvas.addEventListener('mousemove', (e) => {
            // if (drawingPermission) {

                if (drawing) {
                    ctx.lineTo(e.offsetX, e.offsetY);
                    ctx.stroke();
                // }
            }
        });
        canvas.addEventListener('touchmove', (e) => {
            // if (drawingPermission) {

                if (drawing) {
                    touchX = event.changedTouches[0].pageX;
                    touchY = event.changedTouches[0].pageY;
                    preventScroll(e);
                    ctx.lineTo(touchX, touchY);
                    ctx.stroke();

                // }
            }
        });

        canvas.addEventListener('mouseup', () => {
            drawing = false;
            ctx.closePath();
        });
        canvas.addEventListener('mouseout', () => {
            drawing = false;
            ctx.closePath();
        });
        canvas.addEventListener('touchend', () => {
            drawing = false;
            ctx.closePath();
        });

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = "#fff";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }

        function setWhite() {
            ctx.strokeStyle = "#fff";
        }

        function setPenColor() {
            color = colorPicker.value
            console.log(color);
            ctx.strokeStyle = color;

        }


        colorPicker.addEventListener('change', (e) => {
            ctx.strokeStyle = e.target.value;
        });

        brushSize.addEventListener('input', (e) => {
            ctx.lineWidth = e.target.value;
        });

        ctx.strokeStyle = colorPicker.value;
        ctx.lineWidth = brushSize.value;



        document.getElementById('drawingTitle').addEventListener('input', () => {
            let validationResult = document.getElementById('validationResult');
            validationHiraganaResult = validateDrawingTitle();
            if (validationHiraganaResult) {
                let drawingTitle = document.getElementById('drawingTitle').value;
                validationResult.innerText = 'あなたは「' + drawingTitle + '」を描きました！';
            } else {
                validationResult.innerText = 'タイトルはひらがなで入力してね';
            }
        });


        function validateDrawingTitle() {
            let drawingTitle = document.getElementById('drawingTitle').value;
            if (/^[\u3040-\u3090ーん]+$/.test(drawingTitle)) {
                validationHiraganaResult = true;
            } else {
                validationHiraganaResult = false;
            }
            return validationHiraganaResult;
        };

        function saveAndNext() {
            validationHiraganaResult = validateDrawingTitle();
            if (validationHiraganaResult) {
                const dataURL = canvas.toDataURL();
                const drawingTitle = document.getElementById('drawingTitle').value;
                document.getElementById('drawing_data').value = dataURL;
                document.getElementById('drawing_title').value = drawingTitle;

                let room_name = '<?php echo $room_name; ?>';
                let player_name = '<?php echo $player_name; ?>';

                let xhr = new XMLHttpRequest();
                xhr.open('POST', 'save_drawing.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        window.location.href = 'next_player.php?room=<?php echo $room_name; ?>&player=<?php echo $player_name; ?>';
                    }
                };
                xhr.send('room=' + room_name + '&player=' + player_name + '&drawing=' + encodeURIComponent(dataURL) + '&title=' + encodeURIComponent(drawingTitle));
            }
        }

        function startTimer() {
            const countdown = setInterval(() => {
                if (timeLeft <= 0) {
                    validationHiraganaResult = validateDrawingTitle();
                    clearInterval(countdown);
                    drawingPermission = false

                    if (validationHiraganaResult) {
                        saveAndNext();
                    } else {
                        let validationResult = document.getElementById('validationResult');
                        validationResult.innerText = '何を描いたのかひらがなで入力してね！';
                    };
                } else {
                    timerElement.textContent = `残り時間: ${timeLeft}秒`;
                    timeLeft--;
                }
            }, 1000);
        }

        // Initialize canvas background and start timer
        clearCanvas();
        startTimer();
    </script>
</body>

</html>
<?php include 'includes/footer.php'; ?>