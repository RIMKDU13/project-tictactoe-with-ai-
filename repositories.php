<?php
class PlayerRepository {
    public static function getById($id) {
        $db = DatabaseConnection::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM players WHERE player_id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return new Player($data['player_id'], $data['username'], $data['email'], $data['registration_date']);
    }
    
    public static function authenticate($username, $password) {
        $db = DatabaseConnection::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM players WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($player = $stmt->fetch()) {
            if (password_verify($password, $player['password_hash'])) {
                return new Player(
                    $player['player_id'],
                    $player['username'],
                    $player['email'],
                    $player['registration_date']
                );
            }
        }
        return null;
    }
}

class GameRepository {
    public static function save(Game $game) {
        $db = DatabaseConnection::getInstance()->getConnection();
        $stmt = $db->prepare(
            "INSERT INTO games 
            (player_x_id, player_o_id, winner, moves, start_time, end_time, game_duration) 
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        $duration = strtotime($game->getEndTime()) - strtotime($game->getStartTime());
        
        $stmt->execute([
            $game->getPlayerX()->getId(),
            $game->getPlayerO()->getId(),
            $game->getWinner(),
            json_encode($game->getMoves()),
            $game->getStartTime(),
            $game->getEndTime(),
            $duration
        ]);
        
        return $db->lastInsertId();
    }
    
    public static function getRecentGames($limit = 10) {
        $db = DatabaseConnection::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM games ORDER BY end_time DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $games = [];
        while ($row = $stmt->fetch()) {
            $games[] = GameFactory::createFromDatabase($row);
        }
        return $games;
    }
}
?>