<?php
$room_name = isset($_GET['room']) ? $_GET['room'] : '';
$player_name = isset($_GET['player']) ? $_GET['player'] : '';
$room_info_file = "rooms/$room_name-info.json";

// 部屋情報の読み込み
if (file_exists($room_info_file)) {
    $room_info = json_decode(file_get_contents($room_info_file), true);
    $room_info['turn']++;
    file_put_contents($room_info_file, json_encode($room_info));
}

// 描画画面にリダイレクト
header("Location: draw.php?room=$room_name&player=$player_name");
exit();
?>
