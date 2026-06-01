<?php
$room_name = isset($_GET['room']) ? $_GET['room'] : '';
$player_name = isset($_GET['player']) ? $_GET['player'] : '';
$room_info_file = "rooms/$room_name-info.json";
$players_file = "rooms/$room_name.json";

$host_name = 'N/A';
$players = [];
$game_started = false;

if (file_exists($room_info_file)) {
    $room_info = json_decode(file_get_contents($room_info_file), true);
    $host_name = $room_info['host_name'] ?? 'N/A';
    $game_started = $room_info['game_started'] ?? false;
} else {
    echo '部屋情報が見つかりません。';
    exit();
}

if (file_exists($players_file)) {
    $players = json_decode(file_get_contents($players_file), true);
    if (!is_array($players)) $players = [];
}

$role = ($player_name === $host_name) ? 'host' : 'participant';

if ($game_started) {
    header('Location: draw.php?room=' . rawurlencode($room_name) . '&player=' . rawurlencode($player_name));
    exit();
}
include 'includes/header.php';
?>
<div class="title"><img src="paint_title2.png" alt="絵しりとり"></div>
<h1>マッチング画面</h1>
<p>部屋名: <?php echo htmlspecialchars($room_name, ENT_QUOTES, 'UTF-8'); ?></p>
<p>ホスト名: <?php echo htmlspecialchars($host_name, ENT_QUOTES, 'UTF-8'); ?></p>
<p>参加人数: <?php echo count($players); ?></p>

<h2>現在の参加者:</h2>
<button onclick="location.reload()">更新</button>
<ul>
<?php foreach ($players as $player): ?>
    <li><?php echo htmlspecialchars($player, ENT_QUOTES, 'UTF-8'); ?></li>
<?php endforeach; ?>
</ul>

<?php if ($role === 'host') : ?>
<form action="start_game.php" method="POST" onsubmit="return submit_check()">
    <input type="hidden" name="room_name" value="<?php echo htmlspecialchars($room_name, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="host_name" value="<?php echo htmlspecialchars($host_name, ENT_QUOTES, 'UTF-8'); ?>">
    <label for="turns">書く回数:</label>
    <input type="number" name="turns" id="turns" min="1" value="1" required><br>
    <label for="time_limit">制限時間（秒）:</label>
    <input type="number" name="time_limit" id="time_limit" min="10" value="30" required><br>
    <label for="topic">最初の1文字:</label>
    <input type="text" name="topic" id="topic" maxlength="1" required>
    <p id="validationResult"></p>
    <input type="button" id="generateHiragana" onclick="generateOneHiragana()" value="ランダムでひらがな生成"><br><br>
    <div class="container"><input type="submit" class="btn2" value="ゲーム開始"></div>
</form>
<?php else : ?>
<p>ホストがゲームを開始するのを待っています。</p>
<script>
setInterval(function(){ location.reload(); }, 3000);
</script>
<?php endif; ?>

<script>
let validationHiraganaResult = false;
const hiraganaList = 'あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよらりるれろわ';
function generateOneHiragana() {
    const topic = document.getElementById('topic');
    if (!topic) return;
    topic.value = hiraganaList[Math.floor(Math.random() * hiraganaList.length)];
    validationHiraganaResult = true;
    document.getElementById('validationResult').innerText = '';
}
function validationHiragana() {
    const topic = document.getElementById('topic');
    if (!topic) return true;
    validationHiraganaResult = /^[\u3041-\u3096]$/.test(topic.value);
    return validationHiraganaResult;
}
function submit_check() {
    if (!validationHiragana()) {
        document.getElementById('validationResult').innerText = '「最初の1文字」はひらがな1文字で入力してね';
        return false;
    }
    return window.confirm('ゲームを開始します！');
}
const topicInput = document.getElementById('topic');
if (topicInput) {
    topicInput.addEventListener('input', function() {
        if (!validationHiragana()) {
            document.getElementById('validationResult').innerText = '「最初の1文字」はひらがな1文字で入力してね';
        } else {
            document.getElementById('validationResult').innerText = '';
        }
    });
}
</script>
<?php include 'includes/footer.php'; ?>
