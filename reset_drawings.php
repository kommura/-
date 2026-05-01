<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'];

    // 描画データを初期化
    file_put_contents("rooms/$room_name-drawings.json", json_encode([]));
    echo "データが初期化されました。";
    header("Location: index.php");
    exit();
}
?>
