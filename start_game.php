<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'];
    $turns = $_POST['turns'];
    $topic = $_POST['topic'];
    $host_name = $_POST['host_name'];
    $time_limit = $_POST['time_limit'];

    $room_info = [
        'host_name' => $host_name,
        'turns' => $turns,
        'topic' => $topic,
        'time_limit' => $time_limit,
        'turn' => 0,
        'game_started' => true
    ];

    // 部屋情報を保存
    file_put_contents("rooms/$room_name-info.json", json_encode($room_info));

    // 描画データを初期化
    file_put_contents("rooms/{$room_name}/{$room_name}-drawings.json", json_encode([]));

    header("Location: match.php?room=$room_name&player=$host_name");
    exit();
}
?>
