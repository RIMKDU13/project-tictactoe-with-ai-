<?php 
session_start();

// Initialize game state if not set
if (!isset($_SESSION['game'])) {
    $_SESSION['game'] = [
        'board' => [
            ['', '', ''],
            ['', '', ''],
            ['', '', '']
        ],
        'currentPlayer' => 'X',
        'playerX' => 'Guest',
        'playerO' => 'AI',
        'aiDifficulty' => 'medium',
        'aiEnabled' => true,
        'moves' => []
    ];
}

$currentPlayer = $_SESSION['game']['currentPlayer'];
$board = $_SESSION['game']['board'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>karim Tic Tac Toe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .game-container {
            max-width: 500px;
            margin: 0 auto;
        }
        .cell {
            aspect-ratio: 1;
            font-size: 3rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .cell:hover {
            background-color: rgba(0,0,0,0.05);
        }
        .player-card {
            transition: all 0.3s;
        }
        .player-card.active {
            border: 2px solid #0d6efd;
            transform: scale(1.05);
        }
        .move-history {
            max-height: 300px;
            overflow-y: auto;
        }
        .cell.x-mark {
            color: #0d6efd;
            font-weight: bold;
        }
        .cell.o-mark {
            color: #dc3545;
            font-weight: bold;
        }
        @media (max-width: 576px) {
            .cell {
                font-size: 2rem;
            }
        }
        #aiSettingsModal .form-check {
            padding-left: 2rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4 game-container">
        <div class="text-center mb-4">
            <h1 class="display-4">Tic Tac Toe</h1>
            <div class="btn-group mb-3">
                <button class="btn btn-outline-primary" id="newGameBtn">New Game</button>
                <button class="btn btn-outline-secondary" id="statsBtn">Statistics</button>
                <button class="btn btn-outline-info" id="aiSettingsBtn">AI Settings</button>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="card player-card <?= $currentPlayer === 'X' ? 'active' : '' ?>" id="playerXCard">
                    <div class="card-body text-center">
                        <h5 class="card-title">Player X</h5>
                        <p class="card-text" id="playerXName"><?= htmlspecialchars($_SESSION['game']['playerX']) ?></p>
                        <div class="d-flex justify-content-around">
                            <span class="badge bg-primary">Wins: <span id="playerXWins">0</span></span>
                            <span class="badge bg-secondary">Losses: <span id="playerXLosses">0</span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card player-card <?= $currentPlayer === 'O' ? 'active' : '' ?>" id="playerOCard">
                    <div class="card-body text-center">
                        <h5 class="card-title">Player O</h5>
                        <p class="card-text" id="playerOName"><?= htmlspecialchars($_SESSION['game']['playerO']) ?></p>
                        <div class="d-flex justify-content-around">
                            <span class="badge bg-primary">Wins: <span id="playerOWins">0</span></span>
                            <span class="badge bg-secondary">Losses: <span id="playerOLosses">0</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info text-center" id="gameMessage">
            <?= htmlspecialchars($currentPlayer) ?>'s turn
        </div>

        <div class="game-board bg-white rounded shadow mb-4">
            <div class="row g-0">
                <?php for ($i = 0; $i < 3; $i++): ?>
                <div class="col-4 border">
                    <?php for ($j = 0; $j < 3; $j++): ?>
                    <div class="cell d-flex justify-content-center align-items-center border 
                        <?= $board[$i][$j] === 'X' ? 'x-mark' : '' ?>
                        <?= $board[$i][$j] === 'O' ? 'o-mark' : '' ?>"
                         data-row="<?= $i ?>" data-col="<?= $j ?>">
                        <?= htmlspecialchars($board[$i][$j]) ?>
                    </div>
                    <?php endfor; ?>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Game History</h5>
            </div>
            <div class="card-body move-history" id="moveHistory">
                <p class="text-muted">No moves yet</p>
            </div>
        </div>
    </div>

    <!-- AI Settings Modal -->
    <div class="modal fade" id="aiSettingsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">AI Difficulty Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="aiDifficulty" id="easyAI" value="easy" <?= ($_SESSION['game']['aiDifficulty'] ?? 'medium') === 'easy' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="easyAI">Easy (Random Moves)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="aiDifficulty" id="mediumAI" value="medium" <?= ($_SESSION['game']['aiDifficulty'] ?? 'medium') === 'medium' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="mediumAI">Medium (Strategic)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="aiDifficulty" id="hardAI" value="hard" <?= ($_SESSION['game']['aiDifficulty'] ?? 'medium') === 'hard' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="hardAI">Hard (Minimax Algorithm)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveAISettings">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        fetch('stats.php?json=1')

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap modal
    const aiSettingsModal = new bootstrap.Modal(document.getElementById('aiSettingsModal'));

    // New Game button
    document.getElementById('newGameBtn').addEventListener('click', function() {
        fetch('game.php?action=new')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
    });

    // Statistics button - working version
    document.getElementById('statsBtn').addEventListener('click', function() {
        fetch('stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    console.error(data.message);
                    return;
                }
                // Update stats display
                document.getElementById('playerXWins').textContent = data.x_wins || 0;
                document.getElementById('playerXLosses').textContent = data.o_wins || 0;
                document.getElementById('playerOWins').textContent = data.o_wins || 0;
                document.getElementById('playerOLosses').textContent = data.x_wins || 0;
                
                // Update move history
                const moveHistory = document.getElementById('moveHistory');
                if (data.moves && data.moves.length > 0) {
                    let movesHTML = '<h6>Game Moves:</h6><ol>';
                    data.moves.forEach(move => {
                        movesHTML += `<li>Player ${move.player} at ${move.position}</li>`;
                    });
                    movesHTML += '</ol>';
                    moveHistory.innerHTML = movesHTML;
                }
            })
            .catch(error => console.error('Error:', error));
    });

    // AI Settings button
    document.getElementById('aiSettingsBtn').addEventListener('click', function() {
        // Set current selection
        const currentDifficulty = '<?= $_SESSION['game']['aiDifficulty'] ?? 'medium' ?>';
        document.querySelector(`input[name="aiDifficulty"][value="${currentDifficulty}"]`).checked = true;
        aiSettingsModal.show();
    });

    // Save AI Settings
    document.getElementById('saveAISettings').addEventListener('click', function() {
        const difficulty = document.querySelector('input[name="aiDifficulty"]:checked').value;
        fetch(`game.php?action=set_ai&difficulty=${difficulty}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    aiSettingsModal.hide();
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
    });

    // Cell click handler with win/draw detection
    document.querySelectorAll('.cell').forEach(cell => {
        cell.addEventListener('click', function() {
            const row = this.dataset.row;
            const col = this.dataset.col;
            
            fetch(`game.php?action=move&row=${row}&col=${col}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'win') {
                        alert(`Player ${data.winner} wins!`);
                        updateMoveHistory(data.moves);
                        location.reload();
                    } else if (data.status === 'draw') {
                        alert('Game ended in a draw!');
                        updateMoveHistory(data.moves);
                        location.reload();
                    } else if (data.status === 'success') {
                        updateMoveHistory(data.moves);
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    // Helper function to update move history
    function updateMoveHistory(moves) {
        const moveHistory = document.getElementById('moveHistory');
        if (moves && moves.length > 0) {
            let movesHTML = '<h6>Game Moves:</h6><ol>';
            moves.forEach(move => {
                movesHTML += `<li>Player ${move.player} at ${move.position}</li>`;
            });
            movesHTML += '</ol>';
            moveHistory.innerHTML = movesHTML;
        }
    }
});
</script>
</body>
</html>