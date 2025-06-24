<?php
session_start();

// Define tile pool
$letters = file_get_contents("./letters.json");
$letters = json_decode($letters, true);

$tilePool = [];
$points = [];
$numOfTiles = 0;

// Loop through letters
foreach ($letters['letters'] as $key => $letter) {
    
    $tilePool[$key] = $letter['tiles'];
    $numOfTiles += $letter['tiles'];
    $points[$key] = $letter['points'];
}

// Init game state if first time
if (!isset($_SESSION['board'])) {
    $_SESSION['board'] = array_fill(0, 15, array_fill(0, 15, ''));
    $_SESSION['pool'] = [];
    foreach ($tilePool as $tile => $count) {
        for ($i = 0; $i < $count; $i++) {
            $_SESSION['pool'][] = $tile;
        }
    }
    shuffle($_SESSION['pool']);
    $_SESSION['players'] = [
        ['tiles' => [], 'score' => 0],
        ['tiles' => [], 'score' => 0],
    ];
    $_SESSION['turn'] = 0;

    // Draw 7 tiles for each player
    foreach ($_SESSION['players'] as &$player) {
        $player['tiles'] = array_splice($_SESSION['pool'], 0, 7);
    }
}

$turn = $_SESSION['turn'];
$player = &$_SESSION['players'][$turn];
$board = $_SESSION['board'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // For simplicity, only update score manually
    $score = (int)$_POST['score'] ?? 0;
    $player['score'] += $score;

    // Draw up to 7 tiles
    $missing = 7 - count($player['tiles']);
    $newTiles = array_splice($_SESSION['pool'], 0, $missing);
    $player['tiles'] = array_merge($player['tiles'], $newTiles);
    
    // Switch turn
    $_SESSION['turn'] = ($turn + 1) % 2;
    header("Location: game.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Scrabble - 2 Players</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Scrabble - Player <?= $turn + 1 ?>'s Turn</h1>

<div class="scoreboard">
    <p>Player 1: <?= $_SESSION['players'][0]['score'] ?> points</p>
    <p>Player 2: <?= $_SESSION['players'][1]['score'] ?> points</p>
</div>

<div class="board">
    <?php for ($i = 0; $i < 15; $i++): ?>
        <div class="row">
            <?php for ($j = 0; $j < 15; $j++): ?>
                <div class="cell" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <?= htmlspecialchars($board[$i][$j]) ?>
                </div>
            <?php endfor; ?>
        </div>
    <?php endfor; ?>
</div>

<h2>Your Tiles</h2>
<div class="rack">
    <?php foreach ($player['tiles'] as $i => $tile): ?>
        <div class="tile" draggable="true" ondragstart="drag(event)" id="tile<?= $i ?>">
            <?= $tile ?><sub><?= $points[$tile] ?></sub>
        </div>
    <?php endforeach; ?>
</div>

<form method="POST">
    <label>Enter Score This Turn:
        <input type="number" name="score" required>
    </label>
    <button type="submit" id="submitBtn">End Turn</button>
</form>

<a href="index.php">Restart Game</a>

<script src="script.js"></script>
</body>
</html>
