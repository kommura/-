<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'];
    $player_name = $_POST['player_name'];

    $room_info_file = "rooms/$room_name-info.json";

    // 部屋が存在するか確認
    if (!file_exists($room_info_file)) {
        echo '部屋がありません';
        exit();
    }

    // 部屋情報の読み込み
    $room_info = json_decode(file_get_contents($room_info_file), true);
    $players_file = "rooms/$room_name.json";

    // プレイヤーリストの読み込み
    $players = [];
    if (file_exists($players_file)) {
        $players = json_decode(file_get_contents($players_file), true);
    }

    // プレイヤーを追加
    if (!in_array($player_name, $players)) {
        $players[] = $player_name;
        file_put_contents($players_file, json_encode($players));
    }

    // 部屋へリダイレクト
    header("Location: match.php?room=$room_name&player=$player_name");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>部屋に参加する</title>
<link type="text/css" rel="stylesheet" href="style.css">
</head>
<body>
<div class="title"><img src="paint_title2.png"></div>
<br><br><br><br><br>
<h1>部屋に参加する</h1>
<form action="join_room.php" method="POST">
    <label for="room_name">部屋名:</label>
    <input type="text" name="room_name" id="room_name" required>
    <label for="player_name">プレイヤー名:</label>
    <input type="text" name="player_name" id="player_name" required>
    <br><br>
    <div class="container">
        <button type="submit" class="btn2">参加する</button>
    </div>
</form>
</body>
</html>
