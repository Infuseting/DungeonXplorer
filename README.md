# DungeonXplorer

School project for a web application to play a dungeon crawler game.

## Setup

1.  **Install Dependencies**:
    ```bash
    composer install
    ```

2.  **Environment Configuration**:
    - Copy `.env.example` to `.env`:
      ```bash
      cp .env.example .env
      ```
    - Edit `.env` with your database credentials.

3.  **Run the Project**:
    - Start the PHP built-in server:
      ```bash
      php -S localhost:8000 -t public
      ```
    - Open [http://localhost:8000](http://localhost:8000) in your browser.

## Features

- **Routing**: [Bramus Router](https://github.com/bramus/router)
- **Styling**: [Tailwind CSS](https://tailwindcss.com/) (via CDN for development)
- **Database**: PDO connection with `vlucas/phpdotenv` configuration.

## Database Connection

The database connection is handled in `config/database.php`. You can include this file to get a `$pdo` instance:

```php
require_once __DIR__ . '/../config/database.php';
// Use $pdo here
```
