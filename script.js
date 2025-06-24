function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

dropped = 0;
document.getElementById("submitBtn").disabled = true;
function drop(ev) {
    ev.preventDefault();
    var tileId = ev.dataTransfer.getData("text");
    var tile = document.getElementById(tileId);
    if (ev.target.classList.contains('cell') && ev.target.children.length === 0) {
        ev.target.appendChild(tile);
    }
    console.log(tile)
    dropped++;
    console.log(dropped)
    if (dropped < 2){
        document.getElementById("submitBtn").disabled = true;
    }
    else {
        document.getElementById("submitBtn").disabled = false;
    }
}
