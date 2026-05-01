document.addEventListener("DOMContentLoaded", function() {
    let canvas = document.getElementById("drawing-canvas");
    let ctx = canvas.getContext("2d");
    let painting = false;
    
    document.getElementById("color-picker").addEventListener("change", function() {
        ctx.strokeStyle = this.value;
    });
    
    document.getElementById("brush-size").addEventListener("change", function() {
        ctx.lineWidth = this.value;
    });
    
    canvas.addEventListener("mousedown", function() {
        painting = true;
    });
    
    canvas.addEventListener("mouseup", function() {
        painting = false;
        ctx.beginPath();
    });
    
    canvas.addEventListener("mousemove", function(event) {
        if (!painting) return;
        ctx.lineCap = "round";
        ctx.lineTo(event.clientX - canvas.offsetLeft, event.clientY - canvas.offsetTop);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(event.clientX - canvas.offsetLeft, event.clientY - canvas.offsetTop);
    });

    document.getElementById("clear-canvas").addEventListener("click", function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });

    document.getElementById("next-player").addEventListener("click", function() {
        // 次のプレイヤーへのロジックを追加
    });
});
