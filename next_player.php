<?php
$room_name = isset($_GET['room']) ? $_GET['room'] : '';
$player_name = isset($_GET['player']) ? $_GET['player'] : '';
$room_info_file = "rooms/$room_name-info.json";

if (file_exists($room_info_file)) {
    $room_info = json_decode(file_get_contents($room_info_file), true);
    if (!is_array($room_info)) $room_info = [];
    $room_info['turn'] = (int)($room_info['turn'] ?? 0) + 1;
    file_put_contents($room_info_file, json_encode($room_info, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
header('Location: draw.php?room=' . rawurlencode($room_name) . '&player=' . rawurlencode($player_name));
exit();
?>
