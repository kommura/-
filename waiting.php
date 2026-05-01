<?php
$room_name = isset($_GET['room']) ? $_GET['room'] : 'N/A';
$player_name = isset($_GET['player']) ? $_GET['player'] : 'N/A';
$host_name = 'N/A';
$players = [];
$turn = 0;
$room_info_file = "rooms/$room_name-info.json";
$players_file = "rooms/$room_name.json";

// 部屋情報の読み込み
if (file_exists($room_info_file)) {
    $room_info = json_decode(file_get_contents($room_info_file), true);
    $host_name = isset($room_info['host_name']) ? $room_info['host_name'] : 'N/A';
    $turn = isset($room_info['turn']) ? $room_info['turn'] : 0;
}

// プレイヤーリストの読み込み
if (file_exists($players_file)) {
    $players = json_decode(file_get_contents($players_file), true);
} else {
    $players = []; // ファイルが存在しない場合は空の配列を設定
}

// 現在のプレイヤーを取得
$current_player = isset($players[$turn % count($players)]) ? $players[$turn % count($players)] : null;

if ($player_name === $current_player) {
    header("Location: draw.php?room=$room_name&player=$player_name");
    exit;
}
?>

<?php include 'includes/header.php'; ?>
<h1>待機画面</h1>
<p>現在のプレイヤー: <?php echo htmlspecialchars($current_player); ?> が描画中</p>
<p>順番を待っています...</p>

<?php include 'includes/footer.php'; ?>
