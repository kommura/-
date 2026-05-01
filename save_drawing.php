<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room'];
    $player_name = $_POST['player'];
    $drawing = $_POST['drawing'];
    $title = $_POST['title'];

    $room_dir = "rooms/{$room_name}";
    if (!file_exists($room_dir)) {
        mkdir($room_dir, 0777, true);
    }

    $drawings_file = "rooms/$room_name/{$room_name}-drawings.json";

    // 保存されている描画データを読み込む
    $drawings = [];
    if (file_exists($drawings_file)) {
        $drawings = json_decode(file_get_contents($drawings_file), true);
    }

    // 新しい描画データを追加
    $drawings[] = [
        'player' => $player_name,
        'drawing' => $drawing,
        'title' => $title 
    ];

    // 描画データをファイルに保存
    file_put_contents($drawings_file, json_encode($drawings));

    echo '保存完了';
}
?>
