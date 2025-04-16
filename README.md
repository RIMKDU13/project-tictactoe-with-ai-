# Enhanced Tic Tac Toe

A comprehensive implementation of Tic Tac Toe meeting academic requirements with:

- Database persistence (MySQL)
- User authentication
- Game statistics and history
- AI opponents with different difficulty levels
- Modern responsive UI

## Features

1. **Database Design**
   - 4 interconnected tables with proper relationships
   - UTF-8mb4 encoding for full Unicode support
   - JSON storage for game moves

2. **Design Patterns**
   - Singleton (Database connection)
   - Factory (Game object creation)
   - Strategy (AI difficulty levels)
   - Observer (Achievement system)
   - Repository (Data access)

3. **Algorithms**
   - Minimax for perfect AI
   - Game analysis for statistics
   - Move validation and win detection

## Installation

1. Import `tictactoe.sql` to your MySQL server
2. Configure database credentials in `db.php`
3. Install dependencies:
   ```bash
   composer require phpunit/phpunit