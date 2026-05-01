<?php
$room = $_GET['room'];
$roomFile = 'rooms/' . $room . '-info.json';

if (file_exists($roomFile)) {
    $roomData = json_decode(file_get_contents($roomFile), true);
    echo json_encode(['game_started' => $roomData['game_started']]);
} else {
    echo json_encode(['game_started' => false]);
}
?>
