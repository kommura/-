<?php
$room_name = isset($_GET['room']) ? $_GET['room'] : '';
$drawings_file = "rooms/$room_name/$room_name-drawings.json";
$drawings = [];
if (file_exists($drawings_file)) {
    $drawings = json_decode(file_get_contents($drawings_file), true);
    if (!is_array($drawings)) $drawings = [];
}
include 'includes/header.php';
?>
<div id="gameContainer">
    <h1>ゲーム終了</h1>
    <h2>完成した絵しりとり</h2>
    <?php if (empty($drawings)) : ?>
        <p>描画データがありません。</p>
    <?php else : ?>
        <?php foreach ($drawings as $i => $drawing) : ?>
            <div class="gallery">
                <h3><?php echo $i + 1; ?>番目</h3>
                <p>プレイヤー: <?php echo htmlspecialchars($drawing['player'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p>描いた絵: <?php echo htmlspecialchars($drawing['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                <img src="<?php echo htmlspecialchars($drawing['drawing'], ENT_QUOTES, 'UTF-8'); ?>" alt="描いた絵" style="max-width: 240px; max-height: 240px;">
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <p><a href="index.php">トップへ戻る</a></p>
</div>
<?php include 'includes/footer.php'; ?>
