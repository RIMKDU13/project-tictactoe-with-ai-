<?php
header('Content-Type: application/json');

$gamesDir = 'games/';
$gameFiles = glob($gamesDir . 'game_*.json');
$totalGames = count($gameFiles);

$wins = ['X' => 0, 'O' => 0, 'draws' => 0];
$moves = [];

foreach ($gameFiles as $file) {
    $json = file_get_contents($file);
    $data = json_decode($json, true);

    if (isset($data['winner'])) {
        if ($data['winner'] === 'X') {
            $wins['X']++;
        } elseif ($data['winner'] === 'O') {
            $wins['O']++;
        } elseif ($data['winner'] === 'draw') {
            $wins['draws']++;
        }
    }

    if (isset($data['moves'])) {
        $moves = array_merge($moves, $data['moves']);
    }
}

echo json_encode([
    'status' => 'success',
    'total_games' => $totalGames,
    'x_wins' => $wins['X'],
    'o_wins' => $wins['O'],
    'draws' => $wins['draws'],
    'moves' => $moves
]);
