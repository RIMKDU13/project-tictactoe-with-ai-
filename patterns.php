<?php
// Singleton for database connection
class DatabaseConnection {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $this->connection = new PDO(
            "mysql:host=localhost;dbname=tictactoe;charset=utf8mb4",
            "root",
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }
    
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// Factory for game objects
class GameFactory {
    public static function createFromDatabase($gameData) {
        $playerX = PlayerRepository::getById($gameData['player_x_id']);
        $playerO = PlayerRepository::getById($gameData['player_o_id']);
        
        return new Game(
            $gameData['game_id'],
            $playerX,
            $playerO,
            $gameData['winner'],
            json_decode($gameData['moves'], true),
            $gameData['start_time'],
            $gameData['end_time']
        );
    }
}

// Strategy pattern for different AI difficulty levels
interface AIStrategy {
    public function makeMove($board);
}

class EasyAI implements AIStrategy {
    public function makeMove($board) {
        // Random moves
        $emptyCells = [];
        foreach ($board as $i => $row) {
            foreach ($row as $j => $cell) {
                if ($cell === '') {
                    $emptyCells[] = [$i, $j];
                }
            }
        }
        return $emptyCells[array_rand($emptyCells)];
    }
}

class HardAI implements AIStrategy {
    public function makeMove($board) {
        // Implement minimax algorithm here
        // This is a simplified version
        foreach ($board as $i => $row) {
            foreach ($row as $j => $cell) {
                if ($cell === '') {
                    // Check if this move would win
                    $tempBoard = $board;
                    $tempBoard[$i][$j] = 'O';
                    if (checkWin($tempBoard, 'O')) {
                        return [$i, $j];
                    }
                    
                    // Block opponent's winning move
                    $tempBoard[$i][$j] = 'X';
                    if (checkWin($tempBoard, 'X')) {
                        return [$i, $j];
                    }
                }
            }
        }
        
        // Default to center or corners
        if ($board[1][1] === '') return [1, 1];
        $corners = [[0,0], [0,2], [2,0], [2,2]];
        shuffle($corners);
        foreach ($corners as $corner) {
            if ($board[$corner[0]][$corner[1]] === '') {
                return $corner;
            }
        }
        
        // Fallback to random
        $easyAI = new EasyAI();
        return $easyAI->makeMove($board);
    }
}

// Observer pattern for game events
interface GameObserver {
    public function onGameEnd(Game $game);
}

class AchievementSystem implements GameObserver {
    public function onGameEnd(Game $game) {
        $this->checkFirstWinAchievement($game);
        $this->checkStreakAchievement($game);
    }
    
    private function checkFirstWinAchievement(Game $game) {
        // Implementation...
    }
    
    private function checkStreakAchievement(Game $game) {
        // Implementation...
    }
}
?>