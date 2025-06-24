<?php
session_start();

// Reset game session
$_SESSION = [];
header("Location: game.php");
exit;
