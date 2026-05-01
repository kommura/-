<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $room_name = $data['room'];

    // 部屋情報のファイルパス
    $room_info_file = "rooms/$room_name-info.json";

    // 部屋情報の読み込み
    if (file_exists($room_info_file)) {
        $room_info = json_decode(file_get_contents($room_info_file), true);
        $room_info['turn'] = ($room_info['turn'] + 1) % count(json_decode(file_get_contents("rooms/$room_name.json"), true));

        // 部屋情報をファイルに保存
        file_put_contents($room_info_file, json_encode($room_info));
    }
}
