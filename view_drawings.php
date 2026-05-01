<?php
$room_name = isset($_GET['room']) ? $_GET['room'] : '';
$drawings_file = "rooms/{$room_name}/{$room_name}-drawings.json";

// 描画データの読み込み
$drawings = [];
if (file_exists($drawings_file)) {
    $drawings = json_decode(file_get_contents($drawings_file), true);
}

include 'includes/header.php';
?>

<link type="text/css" rel="stylesheet" href="style.css">
<body>
<div class="title"><img src="paint_title2.png"></div>
<br><br><br><br><br>
<h1>描いた絵</h1>
<?php if (!empty($drawings)): ?>
    <div class="drawings_container" style="display: flex;  flex-wrap: wrap; flex-direction: row;">
    <?php foreach ($drawings as $drawing): ?>
        <div>
        <p>プレイヤー: <?php echo htmlspecialchars($drawing['player']); ?></p>
        <p>描いた絵: <?php echo htmlspecialchars($drawing['title']); ?></p> <!-- ここを変更 -->
        <img style="border: 1px solid black; width:400px; height:300px;" src="<?php echo htmlspecialchars($drawing['drawing']); ?>" alt="描いた絵">
        </div>
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>まだ絵がありません。</p>
<?php endif; ?>
<form action="end_game.php" method="POST">
    <input type="hidden" name="room_name" value="<?php echo htmlspecialchars($room_name); ?>">
        <button  style="width: 200px; border: 6px solid #ff3366; border-radius: 10px; padding: 15px; background: #ff6666; color: #fff; margin: 10px 0;"  type="submit">ゲーム終了</button>
</form>
<?php include 'includes/footer.php'; ?>