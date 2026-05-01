<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_name = $_POST['room_name'];
    $player_name = $_POST['player_name'];

    // プレイヤーリストをファイルから読み込み
    $players = json_decode(file_get_contents("rooms/$room_name.json"), true);

    // プレイヤーをリストに追加
    $players[] = $player_name;
    file_put_contents("rooms/$room_name.json", json_encode($players));

    // 参加者情報を個別ファイルに保存
    file_put_contents("rooms/$room_name-$player_name.json", json_encode([
        'room_name' => $room_name,
        'player_name' => $player_name,
        'role' => 'participant'
    ]));

    header('Location: match.php?room=' . urlencode($room_name) . '&player=' . urlencode($player_name));
    exit();
}
?>
