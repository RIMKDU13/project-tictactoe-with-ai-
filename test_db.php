<?php
require_once __DIR__ . '/DatabaseConnection.php';
require_once __DIR__ . '/game.php'; // Include your game logic file

$db = DatabaseConnection::getInstance()->getConnection();

// Test making a move
$_SESSION['game'] = [
    'board' => [['', '', ''], ['', '', ''], ['', '', '']],
    'currentPlayer' => 'X'
];

// Simulate a move
$testBoard = $_SESSION['game']['board'];
$testBoard[1][1] = 'X'; // Player X moves to center

// Check win (should return false)
echo "Win check (should be false): ";
var_dump(checkWin($testBoard, 'X'));

// Test winning condition
$winningBoard = [['X', 'X', 'X'], ['', '', ''], ['', '', '']];
echo "Win check (should be true): ";
var_dump(checkWin($winningBoard, 'X'));
?>