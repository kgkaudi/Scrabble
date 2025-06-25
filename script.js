let draggedTile = null;
let placements = [];

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
        showMessage("Place at least two tiles.", 5000); // 5 seconds
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

function showMessage(message, duration = 3000) {
  const messageBox = document.getElementById("error-messages");
  messageBox.textContent = message;
  messageBox.style.display = "block";

  // Optional: Add some styling
  messageBox.style.padding = "10px";
  messageBox.style.color = "#f54242";
  messageBox.style.margin = "10px 0";
  messageBox.style.borderRadius = "5px";

  // Hide the message after `duration` milliseconds
  setTimeout(() => {
    messageBox.style.display = "none";
  }, duration);
}