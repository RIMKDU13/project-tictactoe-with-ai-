<?php
session_start();

require_once 'patterns.php'; // For AI classes
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// Initialize new game
if ($action === 'new') {
    $_SESSION['game'] = [
        'board' => [['','',''],['','',''],['','','']],
        'currentPlayer' => 'X',
        'playerX' => 'Guest',
        'playerO' => 'AI',
        'aiDifficulty' => $_SESSION['game']['aiDifficulty'] ?? 'medium',
        'moves' => []
    ];
    echo json_encode(['status' => 'success']);
    exit;
}

// Set AI difficulty
if ($action === 'set_ai') {
    $_SESSION['game']['aiDifficulty'] = $_GET['difficulty'] ?? 'medium';
    echo json_encode(['status' => 'success']);
    exit;
}

// Handle player move
if ($action === 'move') {
    $row = (int)$_GET['row'];
    $col = (int)$_GET['col'];
    
    // Validate move
    if ($_SESSION['game']['board'][$row][$col] !== '') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid move']);
        exit;
    }
    
    // Record player move
    $_SESSION['game']['board'][$row][$col] = 'X';
    $_SESSION['game']['moves'][] = ['player' => 'X', 'position' => "$row,$col"];
    
    // Check for win/draw
    if (checkWin($_SESSION['game']['board'], 'X')) {
        echo json_encode([
            'status' => 'win',
            'winner' => 'X',
            'board' => $_SESSION['game']['board'],
            'moves' => $_SESSION['game']['moves']
        ]);
        exit;
    }
    
    if (checkDraw($_SESSION['game']['board'])) {
        echo json_encode([
            'status' => 'draw',
            'board' => $_SESSION['game']['board'],
            'moves' => $_SESSION['game']['moves']
        ]);
        exit;
    }
    
    // AI move based on difficulty
    $ai = match($_SESSION['game']['aiDifficulty']) {
        'easy' => new EasyAI(),
        'hard' => new HardAI(),
        default => new EasyAI()
    };
    
    [$aiRow, $aiCol] = $ai->makeMove($_SESSION['game']['board']);
    $_SESSION['game']['board'][$aiRow][$aiCol] = 'O';
    $_SESSION['game']['moves'][] = ['player' => 'O', 'position' => "$aiRow,$aiCol"];
    
    // Check for AI win
    if (checkWin($_SESSION['game']['board'], 'O')) {
        echo json_encode([
            'status' => 'win',
            'winner' => 'O',
            'board' => $_SESSION['game']['board'],
            'moves' => $_SESSION['game']['moves']
        ]);
        exit;
    }
    
    echo json_encode([
        'status' => 'success',
        'board' => $_SESSION['game']['board'],
        'currentPlayer' => 'X',
        'moves' => $_SESSION['game']['moves']
    ]);
    exit;
}

function checkWin($board, $player) {
    // Check rows and columns
    for ($i = 0; $i < 3; $i++) {
        if (($board[$i][0] == $player && $board[$i][1] == $player && $board[$i][2] == $player) ||
            ($board[0][$i] == $player && $board[1][$i] == $player && $board[2][$i] == $player)) {
            return true;
        }
    }
    // Check diagonals
    return (($board[0][0] == $player && $board[1][1] == $player && $board[2][2] == $player) ||
            ($board[0][2] == $player && $board[1][1] == $player && $board[2][0] == $player));
}

function checkDraw($board) {
    foreach ($board as $row) {
        foreach ($row as $cell) {
            if ($cell === '') return false;
        }
    }
    return true;
}
?>