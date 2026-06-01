<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'POST only';
    exit();
}
$room = $_POST['room'] ?? '';
$player = $_POST['player'] ?? '';
$drawing = $_POST['drawing'] ?? '';
$title = $_POST['title'] ?? '';

if ($room === '' || $player === '' || $drawing === '' || $title === '') {
    http_response_code(400);
    echo '必要なデータが足りません。';
    exit();
}
$room_dir = "rooms/$room";
if (!is_dir($room_dir)) {
    mkdir($room_dir, 0777, true);
}
$drawings_file = "$room_dir/$room-drawings.json";
$drawings = [];
if (file_exists($drawings_file)) {
    $drawings = json_decode(file_get_contents($drawings_file), true);
    if (!is_array($drawings)) $drawings = [];
}
$drawings[] = [
    'player' => $player,
    'description' => $title,
    'drawing' => $drawing,
    'created_at' => date('Y-m-d H:i:s')
];
file_put_contents($drawings_file, json_encode($drawings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo 'OK';
?>
