<?php
require_once 'config.php';
require_once 'Game.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
} else {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
}

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$game = Game::getById($pdo, $id);

if ($game === null) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game->delete($pdo);

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete a Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.12);
        }
        h1 {
            color: #dc3545;
            border-bottom: 3px solid #dc3545;
            padding-bottom: 10px;
            margin-top: 0;
        }
        .confirm-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 18px 20px;
            border-radius: 4px;
            margin: 20px 0;
            color: #856404;
            font-size: 16px;
        }
        .confirm-box strong {
            display: block;
            font-size: 18px;
            margin-bottom: 6px;
        }
        .btn {
            display: inline-block;
            padding: 9px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            border: none;
            margin: 2px;
        }
        .btn-danger  { background-color: #dc3545; color: #fff; }
        .btn-primary { background-color: #1a4a7a; color: #fff; }
        .btn:hover   { opacity: 0.85; }
    </style>
</head>
<body>
<div class="container">
    <h1>Delete a Game</h1>

    <div class="confirm-box">
        <strong>Warning!</strong>
        Are you sure you want to delete the game
        <em><?= $game->getTitle() ?></em>?
        This action cannot be undone.
    </div>

    <form method="post" action="delete.php">
        <input type="hidden" name="id" value="<?= $id ?>">
        <button type="submit" class="btn btn-danger">Yes, permanently delete</button>
        <a href="index.php" class="btn btn-primary">Cancel</a>
    </form>
</div>
</body>
</html>
