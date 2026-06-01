<?php include 'includes/header.php'; ?>
<div class="title"><img src="paint_title2.png" alt="絵しりとり"></div>
<h1>部屋作成</h1>
<form action="process_create_room.php" method="POST">
    <label for="room_name">部屋名:</label>
    <input type="text" id="room_name" name="room_name" required>
    <label for="host_name">ホスト名:</label>
    <input type="text" id="host_name" name="host_name" required><br><br>
    <div class="container">
        <button type="submit" class="btn2">作成</button>
    </div>
</form>
<p><a href="index.php">トップへ戻る</a></p>
<?php include 'includes/footer.php'; ?>
