<form id="drawingForm">
    <canvas id="drawingCanvas" width="800" height="600"></canvas>
    <input type="hidden" name="drawing_data" id="drawing_data">
    <!-- その他の描画コントロール -->
</form>

<script>
// 描画処理のJavaScript
const canvas = document.getElementById('drawingCanvas');
const ctx = canvas.getContext('2d');
let drawing = false;

canvas.addEventListener('mousedown', () => drawing = true);
canvas.addEventListener('mouseup', () => drawing = false);
canvas.addEventListener('mousemove', draw);

function draw(e) {
    if (!drawing) return;
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.stroke();
}

document.querySelector('button').addEventListener('click', () => {
    document.getElementById('drawing_data').value = canvas.toDataURL();
    endTurn();
});
</script>
