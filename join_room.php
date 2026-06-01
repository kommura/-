<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = trim($_POST['room_name'] ?? '');
    $player_name = trim($_POST['player_name'] ?? '');
    $room_name = preg_replace('/[^a-zA-Z0-9ぁ-んァ-ヶ一-龠_-]/u', '_', $room_name);
    $player_name = preg_replace('/[\r\n]/', '', $player_name);

    $room_info_file = "rooms/$room_name-info.json";
    if (!file_exists($room_info_file)) {
        echo '<p class="error">部屋がありません。</p><p><a href="join_room.php">戻る</a></p>';
        exit();
    }

    $players_file = "rooms/$room_name.json";
    $players = [];
    if (file_exists($players_file)) {
        $players = json_decode(file_get_contents($players_file), true);
        if (!is_array($players)) $players = [];
    }
    if (!in_array($player_name, $players, true)) {
        $players[] = $player_name;
        file_put_contents($players_file, json_encode($players, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    header('Location: match.php?room=' . rawurlencode($room_name) . '&player=' . rawurlencode($player_name));
    exit();
}
include 'includes/header.php';
?>
<div class="title"><img src="paint_title2.png" alt="絵しりとり"></div>
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
<p><a href="index.php">トップへ戻る</a></p>
<?php include 'includes/footer.php'; ?>
