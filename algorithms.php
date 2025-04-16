<?php
class GameAI {
    public static function minimax($board, $player, $depth = 0) {
        $availableMoves = self::getAvailableMoves($board);
        
        // Check terminal states
        if (checkWin($board, 'X')) return ['score' => -10 + $depth];
        if (checkWin($board, 'O')) return ['score' => 10 - $depth];
        if (count($availableMoves) === 0) return ['score' => 0];
        
        $moves = [];
        foreach ($availableMoves as $move) {
            $newBoard = $board;
            $newBoard[$move[0]][$move[1]] = $player;
            
            $result = self::minimax(
                $newBoard, 
                $player === 'O' ? 'X' : 'O', 
                $depth + 1
            );
            
            $moves[] = [
                'position' => $move,
                'score' => $result['score']
            ];
        }
        
        // Sort moves by score
        usort($moves, function($a, $b) use ($player) {
            return $player === 'O' ? $b['score'] - $a['score'] : $a['score'] - $b['score'];
        });
        
        return $moves[0];
    }
    
    private static function getAvailableMoves($board) {
        $moves = [];
        foreach ($board as $i => $row) {
            foreach ($row as $j => $cell) {
                if ($cell === '') {
                    $moves[] = [$i, $j];
                }
            }
        }
        return $moves;
    }
}

class GameAnalysis {
    public static function findWinningPatterns($games, $playerId) {
        $patterns = [];
        
        foreach ($games as $game) {
            if (($game->getPlayerX()->getId() == $playerId && $game->getWinner() == 'X') ||
                ($game->getPlayerO()->getId() == $playerId && $game->getWinner() == 'O')) {
                $moves = $game->getMoves();
                $pattern = [];
                
                foreach ($moves as $move) {
                    if ($move['player'] == ($game->getPlayerX()->getId() == $playerId ? 'X' : 'O')) {
                        $pattern[] = $move['position'];
                    }
                }
                
                $patterns[] = $pattern;
            }
        }
        
        return array_count_values(array_map('json_encode', $patterns));
    }
}
?>