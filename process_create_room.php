<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_name = $_POST['room_name'];
    $host_name = $_POST['host_name'];
    $max_players = $_POST['max_players'];

    $room_info = [
        'room_name' => $room_name,
        'host_name' => $host_name,
        'max_players' => $max_players
    ];

    $players = [$host_name];

    // 部屋情報の保存
    file_put_contents("rooms/$room_name-info.json", json_encode($room_info));
    file_put_contents("rooms/$room_name.json", json_encode($players));
    file_put_contents("rooms/{$room_name}-{$host_name}.json", json_encode(['role' => 'host']));

    header('Location: match.php?room=' . urlencode($room_name) . '&player=' . urlencode($host_name));
    exit();
}
?>
