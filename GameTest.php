<?php
use PHPUnit\Framework\TestCase;

require_once '../functions.php';
require_once '../algorithms.php';

class GameTest extends TestCase {
    public function testCheckWinHorizontal() {
        $board = [
            ['X', 'X', 'X'],
            ['O', 'O', ''],
            ['', '', '']
        ];
        $this->assertTrue(checkWin($board, 'X'));
        $this->assertFalse(checkWin($board, 'O'));
    }
    
    public function testCheckWinVertical() {
        $board = [
            ['X', 'O', ''],
            ['X', 'O', ''],
            ['', 'O', 'X']
        ];
        $this->assertTrue(checkWin($board, 'O'));
        $this->assertFalse(checkWin($board, 'X'));
    }
    
    public function testCheckWinDiagonal() {
        $board = [
            ['X', 'O', ''],
            ['O', 'X', ''],
            ['', '', 'X']
        ];
        $this->assertTrue(checkWin($board, 'X'));
    }
    
    public function testCheckDraw() {
        $board = [
            ['X', 'O', 'X'],
            ['X', 'O', 'O'],
            ['O', 'X', 'X']
        ];
        $this->assertTrue(checkDraw($board));
        
        $board[2][2] = '';
        $this->assertFalse(checkDraw($board));
    }
    
    public function testMinimaxTerminalStates() {
        $winBoard = [
            ['X', 'X', 'X'],
            ['O', 'O', ''],
            ['', '', '']
        ];
        $result = GameAI::minimax($winBoard, 'O');
        $this->assertEquals(-7, $result['score']); // 10 - depth (3)
        
        $drawBoard = [
            ['X', 'O', 'X'],
            ['X', 'O', 'O'],
            ['O', 'X', 'X']
        ];
        $result = GameAI::minimax($drawBoard, 'O');
        $this->assertEquals(0, $result['score']);
    }
    
    /**
     * @dataProvider moveProvider
     */
    public function testAIMoveAgainstEasyWin($board, $expectedMove) {
        $ai = new HardAI();
        $move = $ai->makeMove($board);
        $this->assertEquals($expectedMove, $move);
    }
    
    public function moveProvider() {
        return [
            // Immediate win
            [
                [
                    ['O', 'O', ''],
                    ['X', 'X', ''],
                    ['', '', '']
                ],
                [0, 2]
            ],
            // Block opponent
            [
                [
                    ['X', 'X', ''],
                    ['O', '', ''],
                    ['', '', '']
                ],
                [0, 2]
            ],
            // Prefer center
            [
                [
                    ['', '', ''],
                    ['', '', ''],
                    ['', '', '']
                ],
                [1, 1]
            ]
        ];
    }
}
?>