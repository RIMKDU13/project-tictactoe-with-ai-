<?php
class Player {
    private $id;
    private $username;
    private $email;
    private $registrationDate;
    
    public function __construct($id, $username, $email, $registrationDate) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->registrationDate = $registrationDate;
    }
    
    // Getters and setters...
}

class Game {
    private $id;
    private $playerX;
    private $playerO;
    private $winner;
    private $moves;
    private $startTime;
    private $endTime;
    
    public function __construct($id, Player $playerX, Player $playerO, $winner, $moves, $startTime, $endTime) {
        $this->id = $id;
        $this->playerX = $playerX;
        $this->playerO = $playerO;
        $this->winner = $winner;
        $this->moves = $moves;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }
    
    // Getters and setters...
}

class GameHistory implements IteratorAggregate {
    private $games = [];
    
    public function addGame(Game $game) {
        $this->games[] = $game;
    }
    
    public function getIterator() {
        return new ArrayIterator($this->games);
    }
    
    public function filterByPlayer($playerId) {
        return array_filter($this->games, function($game) use ($playerId) {
            return $game->getPlayerX()->getId() == $playerId || 
                   $game->getPlayerO()->getId() == $playerId;
        });
    }
}
?>

