<?php
$room_name = isset($_GET['room']) ? $_GET['room'] : 'N/A';
$room_file = "rooms/$room_name.json";

$response = ['game_started' => false];

if (file_exists($room_file)) {
    $room_data = json_decode(file_get_contents($room_file), true);
    if (isset($room_data['game_started']) && $room_data['game_started'] === true) {
        $response['game_started'] = true;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
