let draggedTile = null;
let placements = [];

if (localStorage.getItem('remainingTiles') !== null) {
    // The key exists in localStorage
    let remainingTiles = localStorage.getItem('remainingTiles');
    document.getElementById("remainingTiles").textContent = remainingTiles;
}

let remainingTiles = document.getElementById("remainingTiles").textContent;
if (remainingTiles < 7) {
    console.log("out");
    const score1 = document.getElementById("score1").textContent;
    const score2 = document.getElementById("score2").textContent;
    if (score1 > score2){
        winMessage = "Winner is Player1";
    } else if (score2 > score1){
        winMessage = "Winner is Player2";
    } else {
        winMessage = "Draw";
    }
    showMessage(winMessage, "#229602",15000); // 15 seconds
    document.getElementById("submitMove").disabled = true;
}

document.querySelectorAll('.tile').forEach(tile => {
    tile.addEventListener('dragstart', e => {
        draggedTile = tile;
    });
});

document.querySelectorAll('.cell').forEach(cell => {
    cell.addEventListener('dragover', e => e.preventDefault());
    cell.addEventListener('drop', e => {
        e.preventDefault();
        if (!draggedTile) return;

        if (cell.textContent === '') {            
            const letter = draggedTile.dataset.letter;
            cell.textContent = letter;
            
            if (localStorage.getItem('remainingTiles') !== null) {
                // The key exists in localStorage
                let remainingTilesString = localStorage.getItem('remainingTiles');
                let remainingTiles = parseInt(remainingTilesString);
                remainingTiles--;
                remainingTiles = remainingTiles.toString();
                localStorage.setItem('remainingTiles', remainingTiles);
            } else {
                // The key does not exist
                let remainingTiles = document.getElementById("remainingTiles").textContent;
                localStorage.setItem('initialTiles', remainingTiles);
                localStorage.setItem('remainingTiles', remainingTiles);
                remainingTiles = parseInt(remainingTiles);
                remainingTiles--;
                remainingTiles = remainingTiles.toString();
                localStorage.setItem('remainingTiles', remainingTiles);                
            }

            placements.push({
                x: parseInt(cell.dataset.x),
                y: parseInt(cell.dataset.y),
                letter: letter
            });
            draggedTile.remove();
        }
    });
});

document.getElementById('submitMove').addEventListener('click', () => {
    if (placements.length < 2){
        showMessage("Place at least two tiles.", "#f54242",5000); // 5 seconds
        return;
    } 
        
    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'placements=' + encodeURIComponent(JSON.stringify(placements))
    })
    .then(res => res.json())
    .then(data => location.reload());
});

function showMessage(message, color, duration = 3000) {
  const messageBox = document.getElementById("error-messages");
  messageBox.textContent = message;
  messageBox.style.display = "block";

  // Optional: Add some styling
  messageBox.style.padding = "10px";
  messageBox.style.color = color;
  messageBox.style.margin = "10px 0";
  messageBox.style.borderRadius = "5px";

  // Hide the message after `duration` milliseconds
  setTimeout(() => {
    messageBox.style.display = "none";
  }, duration);
}