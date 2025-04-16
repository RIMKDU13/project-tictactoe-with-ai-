document.addEventListener('DOMContentLoaded', function() {
    // Initialize game board
    function updateBoard(board) {
        for (let i = 0; i < 3; i++) {
            for (let j = 0; j < 3; j++) {
                const cell = document.querySelector(`.cell[data-row="${i}"][data-col="${j}"]`);
                cell.textContent = board[i][j] || '';
            }
        }
    }

    // Update game status
    function updateStatus(currentPlayer) {
        document.getElementById('gameMessage').textContent = `${currentPlayer}'s turn`;
    }

    // New Game button
    document.getElementById('newGameBtn').addEventListener('click', async function() {
        try {
            const response = await fetch('game.php?action=new');
            const data = await response.json();
            if (data.status === 'success') {
                updateBoard([[ '', '', '' ], [ '', '', '' ], [ '', '', '' ]]);
                updateStatus('X');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });

    // Cell click handler
    document.querySelectorAll('.cell').forEach(cell => {
        cell.addEventListener('click', async function() {
            const row = this.dataset.row;
            const col = this.dataset.col;
            
            try {
                const response = await fetch(`game.php?action=move&row=${row}&col=${col}`);
                const data = await response.json();
                
                if (data.status === 'win') {
                    alert(`Player ${data.winner} wins!`);
                    updateBoard(data.board || [[ '', '', '' ], [ '', '', '' ], [ '', '', '' ]]);
                } else if (data.status === 'draw') {
                    alert('Game ended in a draw!');
                } else if (data.status === 'success') {
                    updateBoard(data.board);
                    updateStatus(data.currentPlayer);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });

    // Statistics button
    document.getElementById('statsBtn').addEventListener('click', function() {
        // Fetch and display statistics
        fetch('stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    console.error(data.message);
                    return;
                }

                // Update the stats display
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
                } else {
                    moveHistory.innerHTML = '<p class="text-muted">No moves yet</p>';
                }
            })
            .catch(error => console.error('Error:', error));
    });

    // AI Settings button
    document.getElementById('aiSettingsBtn').addEventListener('click', function() {
        // Open the AI settings modal
        const aiSettingsModal = new bootstrap.Modal(document.getElementById('aiSettingsModal'));
        aiSettingsModal.show();
    });

    // Save AI Settings
    document.getElementById('saveAISettings').addEventListener('click', async function() {
        const difficulty = document.querySelector('input[name="aiDifficulty"]:checked').value;
        try {
            const response = await fetch(`game.php?action=set_ai&difficulty=${difficulty}`);
            const data = await response.json();
            if (data.status === 'success') {
                // Close the modal and reload to apply changes
                const aiSettingsModal = bootstrap.Modal.getInstance(document.getElementById('aiSettingsModal'));
                aiSettingsModal.hide();
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});
