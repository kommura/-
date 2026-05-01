<?php
$room_name = isset($_GET['room']) ? $_GET['room'] : 'N/A';
$player_name = isset($_GET['player']) ? $_GET['player'] : 'N/A';
$host_name = 'N/A';
$players = [];
$game_started = false;

// 部屋情報のファイルパス
$room_info_file = "rooms/$room_name-info.json";
$players_file = "rooms/$room_name.json";

// 部屋情報の読み込み
if (file_exists($room_info_file)) {
    $room_info = json_decode(file_get_contents($room_info_file), true);
    $host_name = isset($room_info['host_name']) ? $room_info['host_name'] : 'N/A';
    $game_started = isset($room_info['game_started']) ? $room_info['game_started'] : false;
}

// プレイヤーリストの読み込み
if (file_exists($players_file)) {
    $players = json_decode(file_get_contents($players_file), true);
}

// プレイヤーの役割を確認
$role = 'guest';
if ($player_name === $host_name) {
    $role = 'host';
} elseif (is_array($players) && in_array($player_name, $players)) {
    $role = 'participant';
}

// ゲームが開始された場合、全プレイヤーをdraw.phpにリダイレクト
if ($game_started) {
    header("Location: draw.php?room=$room_name&player=$player_name");
    exit();
}
?>
<link type="text/css" rel="stylesheet" href="style.css">
<script>
    let validationHiraganaResult = false;

    // ひらがな一覧
    const hiraganaList = 'あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよ>らりるれろわ';
    // ランダムにひらがな一文字を生成して表示
    function generateOneHiragana() {
        const randomHiragana = hiraganaList[Math.floor(Math.random() * hiraganaList.length)];
        document.getElementById('topic').value = randomHiragana;
        console.log(randomHiragana);
        validationHiraganaResult = true;
    };

    function validationHiragana() {
        const drawingTitle = document.getElementById('topic').value;
        console.log("タイトル");
        console.log(drawingTitle);
        console.log(validationHiraganaResult);
        if (/^[\u3040-\u3090]$/.test(drawingTitle)) {
            validationHiraganaResult = true;
        } else {
            validationHiraganaResult = false;
        }
        console.log(validationHiraganaResult);
        return validationHiraganaResult;
    };

    document.getElementById('topic').addEventListener('input', () => {
        const drawingTitle = document.getElementById('topic').value;
        const validationResult = document.getElementById('validationResult');
        validationHiraganaResult = validationHiragana()
        if (!validationHiraganaResult) {
            validationResult.innerText = '「最初の1文字」はひらがなで入力してね';
        }
    });

    function submit_check() {
        validationHiraganaResult = validationHiragana()
        if (!validationHiraganaResult) {
            const validationResult = document.getElementById('validationResult');
            validationResult.innerText = '「最初の1文字」はひらがなで入力してね';
            return false;
        } else {
            return check()
        }
    }

    function check() {
        if (window.confirm('ゲームを開始します！')) {
            return true;
        }
        return false
    }
</script>

<?php include 'includes/header.php'; ?>
<body>
    <div class="title"><img src="paint_title2.png"></div>
    <br><br><br><br><br>
    <h1>マッチング画面</h1>
    <p>部屋名: <?php echo htmlspecialchars($room_name); ?></p>
    <p>ホスト名: <?php echo htmlspecialchars($host_name); ?></p>
    <p>参加人数: <?php echo count($players); ?></p>

    <h2>現在の参加者:</h2>
    <button style="width: 130px; border: 6px solid #ff3366; border-radius: 100px; padding: 15px; background: #ff6666; color: #fff; margin: 10px 0;" onclick="location.reload()">更新</button>
    <ul>
        <?php if (count($players) > 0) : ?>
            <?php foreach ($players as $player) : ?>
                <li><?php echo htmlspecialchars($player); ?></li>
            <?php endforeach; ?>
        <?php else : ?>
            <li>まだ参加者はいません</li>
        <?php endif; ?>
    </ul>

    <?php if ($role == 'host') : ?>
        <form action="start_game.php" method="POST" onSubmit="return submit_check()">
        <!-- <form action="start_game.php" method="POST"> -->
            <input type="hidden" name="room_name" value="<?php echo htmlspecialchars($room_name); ?>">
            <input type="hidden" name="host_name" value="<?php echo htmlspecialchars($host_name); ?>"> <!-- 追加 -->
            <label for="turns">書く回数: </label>
            <input type="number" name="turns" id="turns" min="1" required>
            <br>
            <label for="time_limit">制限時間（秒）: </label>
            <input type="number" name="time_limit" id="time_limit" min="10" required>
            <br>
            <label for="topic">最初の1文字: </label>
            <input type="text" name="topic" id="topic" required>
            <p id="validationResult"></p>
            <input type="button" id="generateHiragana" onclick="generateOneHiragana()" value="ランダム>でひらがな生成">
            <br><br>


            <div class="container">
                <input type="submit" class="btn2" value="ゲーム開始">
            </div>
        </form>
    <?php else : ?>
        <p>ホストがゲームを開始するのを待っています。</p>

        <script>
            // 定期的にチェックして、ゲームが開始されたら画面をリロードする
            setInterval(function() {
                window.location.reload();
            }, 3000); // 3秒ごとにチェック
        </script>
    <?php endif; ?>

    </div>
    <?php include 'includes/footer.php'; ?>

                                       