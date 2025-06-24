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
    if (placements.length === 0) return alert("Place at least one tile.");
    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'placements=' + encodeURIComponent(JSON.stringify(placements))
    })
    .then(res => res.json())
    .then(data => location.reload());
});
