<?php
session_start();

$letters = file_get_contents("./letters.json");
$letters = json_decode($letters, true);

$tileDistribution = [];
$letterPoints = [];
$remaining_tiles = 0;

// Loop through letters
foreach ($letters['letters'] as $key => $letter) {
    $tileDistribution[$key] = $letter['tiles'];
    $remaining_tiles += $letter['tiles'];
    $letterPoints[$key] = $letter['points'];
}

//used from refill
function drawTiles($num) {
    $letters = array_keys($GLOBALS['letterPoints']);
    $tiles = [];
    for ($i = 0; $i < $num; $i++) {
        $tiles[] = $letters[array_rand($letters)];
    }
    return $tiles;
}

//initialise
function drawTilesFromPool(&$pool, $num) {
    $drawn = [];
    while (count($drawn) < $num && !empty($pool)) {
        $index = array_rand($pool);
        $letter = $pool[$index];
        $drawn[] = $letter;
        array_splice($pool, $index, 1);
    }
    return $drawn;
}

function findPointsByLetter($letterArray, $letter) {
    foreach ($letterArray as $key => $value) {
        if ($key === $letter) {
            return $value;
        } elseif (is_array($value) || is_object($value)) {
            $found = findPointsByLetter((array)$value, $letter);
            if ($found !== null) {
                return $found;
            }
        }
    }
    return null;
}

// Initialize players and board
if (!isset($_SESSION['board'])) {
    $pool = [];
    foreach ($tileDistribution as $letter => $count) {
        $pool = array_merge($pool, array_fill(0, $count, $letter));
    }
    shuffle($pool);

    $_SESSION['pool'] = $pool;
    $_SESSION['board'] = array_fill(0, 15, array_fill(0, 15, ''));
    $_SESSION['players'] = [
        ['tiles' => drawTilesFromPool($_SESSION['pool'], 7), 'score' => 0],
        ['tiles' => drawTilesFromPool($_SESSION['pool'], 7), 'score' => 0]
    ];
    $_SESSION['turn'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placements = $_POST['placements'];
    $placements = json_decode($placements, true);
    $current = $_SESSION['turn'];
    $turnPoints = 0;

    foreach ($placements as $p) {
        $x = $p['x'];
        $y = $p['y'];
        $letter = $p['letter'];
        if ($_SESSION['board'][$y][$x] === '') {
            $_SESSION['board'][$y][$x] = $letter;
            $turnPoints += $letterPoints[$letter];
        }                
    }

    $_SESSION['players'][$current]['score'] += $turnPoints;
    $_SESSION['players'][$current]['tiles'] = drawTiles(7);$_SESSION['players'][$current]['tiles'] = drawTiles(7); // Refill hand
    $_SESSION['turn'] = 1 - $current;

    header('Content-Type: application/json');
    echo json_encode([
        'board' => $_SESSION['board'],
        'players' => $_SESSION['players'],
        'turn' => $_SESSION['turn']
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Scrabble Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Scrabble (2 Player)</h1>
    <div id="scoreboard">
        <div>Player 1: <span id="score1"><?= $_SESSION['players'][0]['score'] ?></span></div>
        <div>Player 2: <span id="score2"><?= $_SESSION['players'][1]['score'] ?></span></div>
        <div>Tiles remaining: <span id="remainingTiles"><?= $remaining_tiles ?></span></div>        
        <div>Current Turn: Player <span id="current"><?= $_SESSION['turn'] + 1 ?></span></div>
        <div id="error-messages"></div>
    </div>
    <div id="board">
        <?php
        for ($y = 0; $y < 15; $y++) {
            for ($x = 0; $x < 15; $x++) {
                $tile = $_SESSION['board'][$y][$x];
                echo "<div class='cell' data-x='$x' data-y='$y'>" . ($tile ?: '') . "</div>";
            }
        }
        ?>
    </div>
    <div id="rack">
        <?php
        foreach ($_SESSION['players'][$_SESSION['turn']]['tiles'] as $letter) {
            $points = findPointsByLetter($letterPoints, $letter);
            echo "<div class='tile' draggable='true' data-letter='$letter'>$letter <span class='tilePoints'>$points</span></div>";
        }
        ?>
    </div>
    <button id="submitMove">Submit Turn</button>

    <script>
        const letterPoints = <?= json_encode($letterPoints) ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>
