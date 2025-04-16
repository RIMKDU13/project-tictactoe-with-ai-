<?php
class GameStatistics {
    public static function getPlayerStats($playerId) {
        $db = DatabaseConnection::getInstance()->getConnection();
        
        $stmt = $db->prepare(
            "SELECT 
                COUNT(*) as total_games,
                SUM(CASE WHEN winner = 'X' AND player_x_id = :player_id THEN 1 ELSE 0 END) as x_wins,
                SUM(CASE WHEN winner = 'O' AND player_o_id = :player_id THEN 1 ELSE 0 END) as o_wins,
                SUM(CASE WHEN winner = 'D' AND (player_x_id = :player_id OR player_o_id = :player_id) THEN 1 ELSE 0 END) as draws,
                MAX(game_date) as last_played
             FROM games
             WHERE player_x_id = :player_id OR player_o_id = :player_id"
        );
        $stmt->execute([':player_id' => $playerId]);
        return $stmt->fetch();
    }
    
    public static function getWinPercentageByFirstMove($playerId) {
        $db = DatabaseConnection::getInstance()->getConnection();
        
        $stmt = $db->prepare(
            "SELECT 
                SUBSTRING(JSON_EXTRACT(moves, '$[0].position'), 1, 1) as first_move,
                COUNT(*) as total_games,
                SUM(CASE WHEN winner = 'X' AND player_x_id = :player_id THEN 1
                         WHEN winner = 'O' AND player_o_id = :player_id THEN 1 ELSE 0 END) as wins,
                SUM(CASE WHEN winner = 'D' THEN 1 ELSE 0 END) as draws
             FROM games
             WHERE (player_x_id = :player_id OR player_o_id = :player_id)
             GROUP BY first_move"
        );
        $stmt->execute([':player_id' => $playerId]);
        return $stmt->fetchAll();
    }

    public static function saveGameResult($playerXId, $playerOId, $winner, $moves) {
        $db = DatabaseConnection::getInstance()->getConnection();
        
        $stmt = $db->prepare(
            "INSERT INTO games 
            (player_x_id, player_o_id, winner, moves, game_date) 
            VALUES (:player_x_id, :player_o_id, :winner, :moves, NOW())"
        );
        
        return $stmt->execute([
            ':player_x_id' => $playerXId,
            ':player_o_id' => $playerOId,
            ':winner' => $winner,
            ':moves' => json_encode($moves)
        ]);
    }
}
?>