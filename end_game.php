<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'];
    $room_info_file = "rooms/{$room_name}-info.json";
    $players_file = "rooms/{$room_name}.json";
    $drawings_file = "rooms/{$room_name}/{$room_name}-drawings.json";

    // ファイルを削除
    if (file_exists($room_info_file)) {
        unlink($room_info_file);
    }
    if (file_exists($players_file)) {
        unlink($players_file);
    }
    if (file_exists($drawings_file)) {
        unlink($drawings_file);
    }

    // フォルダが空であれば削除
    $room_dir = "rooms/{$room_name}";
    if (is_dir($room_dir) && count(scandir($room_dir)) == 2) {
        rmdir($room_dir);
    }

    echo '部屋が削除されました。';
    header("Location: index.php");
    exit();
}
?>
