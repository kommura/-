<?php
$room_name = isset($_GET['room']) ? $_GET['room'] : '';
$player_name = isset($_GET['player']) ? $_GET['player'] : '';
$room_info_file = "rooms/$room_name-info.json";
$players_file = "rooms/$room_name.json";

if (!file_exists($room_info_file) || !file_exists($players_file)) {
    echo '部屋情報が見つかりません。';
    exit();
}
$room_info = json_decode(file_get_contents($room_info_file), true);
$players = json_decode(file_get_contents($players_file), true);
if (!is_array($players) || count($players) === 0) {
    echo '参加者がいません。';
    exit();
}
$turn = (int)($room_info['turn'] ?? 0);
$turns = (int)($room_info['turns'] ?? 1);
$topic = $room_info['topic'] ?? '???';
$time_limit = (int)($room_info['time_limit'] ?? 30);
$current_player = $players[$turn % count($players)];
$is_current_player = ($player_name === $current_player);

if ($turn >= $turns * count($players)) {
    header('Location: view_drawings.php?room=' . rawurlencode($room_name));
    exit();
}
include 'includes/header.php';
?>
<div id="gameContainer">
    <h1>現在のプレイヤー: <?php echo htmlspecialchars($current_player, ENT_QUOTES, 'UTF-8'); ?> が描画中</h1>
    <p>頭文字: <?php echo htmlspecialchars($topic, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php if ($is_current_player) : ?>
        <p>あなたの番です！絵を描いてください。</p>
        <canvas id="drawingCanvas" width="800" height="600"></canvas>
        <div id="controls">
            <button type="button" onclick="clearCanvas()">クリア</button>
            <button type="button" onclick="setWhite()">消しゴム</button>
            <button type="button" onclick="setPenColor()">ペン</button>
            <label for="colorPicker">色選択:</label>
            <input type="color" id="colorPicker" value="#000000">
            <label for="brushSize">ブラシサイズ:</label>
            <input type="range" id="brushSize" min="1" max="50" value="5">
            <label for="drawingTitle">描いた絵:</label>
            <input type="text" id="drawingTitle" autocomplete="off">
            <h3 id="validationResult"></h3>
        </div>
        <button type="button" onclick="saveAndNext()">次のプレイヤーへ</button>
        <p id="timer"></p>
    <?php else : ?>
        <p>順番を待っています...</p>
        <script>setInterval(function(){ window.location.reload(); }, 3000);</script>
    <?php endif; ?>

    <h2>これまでの描画</h2>
    <?php
    $drawings_file = "rooms/{$room_name}/{$room_name}-drawings.json";
    $drawings = [];
    if (file_exists($drawings_file)) {
        $drawings = json_decode(file_get_contents($drawings_file), true);
        if (!is_array($drawings)) $drawings = [];
    }
    if (!empty($drawings)) {
        foreach ($drawings as $drawing) {
            echo '<div class="gallery">';
            echo '<p>プレイヤー: ' . htmlspecialchars($drawing['player'], ENT_QUOTES, 'UTF-8') . '</p>';
            echo '<p>描いた絵: 答えはゲーム終了後に表示されます</p>';
            echo '<img src="' . htmlspecialchars($drawing['drawing'], ENT_QUOTES, 'UTF-8') . '" alt="描いた絵" style="max-width: 200px; max-height: 200px;">';
            echo '</div>';
        }
    } else {
        echo '<p>まだ絵がありません。</p>';
    }
    ?>
</div>

<?php if ($is_current_player) : ?>
<script>
const canvas = document.getElementById('drawingCanvas');
const ctx = canvas.getContext('2d');
const colorPicker = document.getElementById('colorPicker');
const brushSize = document.getElementById('brushSize');
const timerElement = document.getElementById('timer');
let drawing = false;
let drawingPermission = true;
let timeLeft = <?php echo $time_limit; ?>;

function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    const p = e.touches ? e.touches[0] : e;
    return { x: p.clientX - rect.left, y: p.clientY - rect.top };
}
function startDraw(e) {
    if (!drawingPermission) return;
    e.preventDefault();
    drawing = true;
    const pos = getPos(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
}
function moveDraw(e) {
    if (!drawing || !drawingPermission) return;
    e.preventDefault();
    const pos = getPos(e);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
}
function endDraw() {
    drawing = false;
    ctx.closePath();
}
canvas.addEventListener('mousedown', startDraw);
canvas.addEventListener('mousemove', moveDraw);
canvas.addEventListener('mouseup', endDraw);
canvas.addEventListener('mouseout', endDraw);
canvas.addEventListener('touchstart', startDraw, {passive:false});
canvas.addEventListener('touchmove', moveDraw, {passive:false});
canvas.addEventListener('touchend', endDraw);

function clearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
}
function setWhite() { ctx.strokeStyle = '#fff'; }
function setPenColor() { ctx.strokeStyle = colorPicker.value; }
colorPicker.addEventListener('change', setPenColor);
brushSize.addEventListener('input', function(e){ ctx.lineWidth = e.target.value; });
ctx.strokeStyle = colorPicker.value;
ctx.lineWidth = brushSize.value;
ctx.lineCap = 'round';
ctx.lineJoin = 'round';

function validateDrawingTitle() {
    const drawingTitle = document.getElementById('drawingTitle').value;
    return /^[\u3041-\u3096ー]+$/.test(drawingTitle);
}
document.getElementById('drawingTitle').addEventListener('input', function(){
    const validationResult = document.getElementById('validationResult');
    const drawingTitle = document.getElementById('drawingTitle').value;
    validationResult.innerText = validateDrawingTitle() ? 'あなたは「' + drawingTitle + '」を描きました！' : 'タイトルはひらがなで入力してね';
});
function saveAndNext() {
    if (!validateDrawingTitle()) {
        document.getElementById('validationResult').innerText = '何を描いたのかひらがなで入力してね！';
        return;
    }
    drawingPermission = false;
    const dataURL = canvas.toDataURL('image/png');
    const drawingTitle = document.getElementById('drawingTitle').value;
    const params = 'room=' + encodeURIComponent('<?php echo $room_name; ?>') +
                   '&player=' + encodeURIComponent('<?php echo $player_name; ?>') +
                   '&drawing=' + encodeURIComponent(dataURL) +
                   '&title=' + encodeURIComponent(drawingTitle);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_drawing.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                window.location.href = 'next_player.php?room=<?php echo rawurlencode($room_name); ?>&player=<?php echo rawurlencode($player_name); ?>';
            } else {
                alert('保存に失敗しました。');
                drawingPermission = true;
            }
        }
    };
    xhr.send(params);
}
function startTimer() {
    const countdown = setInterval(function(){
        if (timeLeft <= 0) {
            clearInterval(countdown);
            saveAndNext();
        } else {
            timerElement.textContent = '残り時間: ' + timeLeft + '秒';
            timeLeft--;
        }
    }, 1000);
}
clearCanvas();
startTimer();
</script>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>
