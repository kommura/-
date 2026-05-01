<!-- drawing_canvas.php -->
<div id="canvas-container">
    <canvas id="drawing-canvas" width="800" height="600"></canvas>
    <div>
        <button id="clear-canvas">クリア</button>
        <label for="color-picker">色選択: </label>
        <input type="color" id="color-picker" value="#000000">
        <label for="brush-size">ブラシサイズ: </label>
        <input type="range" id="brush-size" min="1" max="50" value="5">
    </div>
    <button id="next-player">次のプレイヤーへ</button>
</div>

<script src="script.js"></script>
