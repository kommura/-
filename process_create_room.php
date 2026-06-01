<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create_room.php');
    exit();
}

$room_name = trim($_POST['room_name'] ?? '');
$host_name = trim($_POST['host_name'] ?? '');

if ($room_name === '' || $host_name === '') {
    echo '部屋名とホスト名を入力してください。';
    exit();
}

// ファイル名として危険な文字を除去
$room_name = preg_replace('/[^a-zA-Z0-9ぁ-んァ-ヶ一-龠_-]/u', '_', $room_name);
$host_name = preg_replace('/[\r\n]/', '', $host_name);

if (!is_dir('rooms')) {
    mkdir('rooms', 0777, true);
}
if (!is_dir("rooms/$room_name")) {
    mkdir("rooms/$room_name", 0777, true);
}

$room_info = [
    'room_name' => $room_name,
    'host_name' => $host_name,
    'game_started' => false,
    'turn' => 0,
    'turns' => 1,
    'topic' => '',
    'time_limit' => 30
];

file_put_contents("rooms/$room_name-info.json", json_encode($room_info, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
file_put_contents("rooms/$room_name.json", json_encode([$host_name], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
file_put_contents("rooms/$room_name/$room_name-drawings.json", json_encode([], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

header('Location: match.php?room=' . rawurlencode($room_name) . '&player=' . rawurlencode($host_name));
exit();
?>
