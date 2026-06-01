<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}
$room_name = $_POST['room_name'] ?? '';
$host_name = $_POST['host_name'] ?? '';
$turns = max(1, (int)($_POST['turns'] ?? 1));
$time_limit = max(10, (int)($_POST['time_limit'] ?? 30));
$topic = trim($_POST['topic'] ?? '');

$room_info_file = "rooms/$room_name-info.json";
if (!file_exists($room_info_file)) {
    echo '部屋情報が見つかりません。';
    exit();
}
$room_info = json_decode(file_get_contents($room_info_file), true);
if (!is_array($room_info)) $room_info = [];
$room_info['game_started'] = true;
$room_info['turn'] = 0;
$room_info['turns'] = $turns;
$room_info['topic'] = $topic;
$room_info['time_limit'] = $time_limit;
file_put_contents($room_info_file, json_encode($room_info, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

$room_dir = "rooms/$room_name";
if (!is_dir($room_dir)) {
    mkdir($room_dir, 0777, true);
}
file_put_contents("$room_dir/$room_name-drawings.json", json_encode([], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

header('Location: draw.php?room=' . rawurlencode($room_name) . '&player=' . rawurlencode($host_name));
exit();
?>
