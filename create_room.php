<?php include 'includes/header.php'; ?>
<link type="text/css" rel="stylesheet" href="style.css">
<body>
<div class="title"><img src="paint_title2.png"></div>
<br><br><br><br><br>
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
</body>

<?php include 'includes/footer.php'; ?>