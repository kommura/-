<?php
include 'db.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rooms (
            id INT AUTO_INCREMENT PRIMARY KEY,
            room_name VARCHAR(255) NOT NULL,
            current_player INT DEFAULT 0
        );

        CREATE TABLE IF NOT EXISTS players (
            id INT AUTO_INCREMENT PRIMARY KEY,
            room_id INT,
            player_name VARCHAR(255) NOT NULL,
            FOREIGN KEY (room_id) REFERENCES rooms(id)
        );
    ");
    echo "Tables created successfully.";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>
