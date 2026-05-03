<?php
require_once 'config.php';
require_once 'Game.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$game = Game::getById($pdo, $id);

if ($game === null) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.12);
        }
        h1 {
            color: #1a4a7a;
            border-bottom: 3px solid #1a4a7a;
            padding-bottom: 10px;
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px 14px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #e8f0f9;
            color: #1a4a7a;
            width: 200px;
        }
        tr:nth-child(even) td { background-color: #f7f9fb; }
        .btn {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            cursor: pointer;
            border: none;
            margin: 2px;
        }
        .btn-primary { background-color: #1a4a7a; color: #fff; }
        .btn-warning { background-color: #e0a800; color: #fff; }
        .btn-danger  { background-color: #dc3545; color: #fff; }
        .btn:hover   { opacity: 0.85; }
        .actions     { margin-top: 25px; }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-yes { background-color: #28a745; color: #fff; }
        .badge-no  { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>
<div class="container">
    <h1>Details: <?= $game->getTitle() ?></h1>

    <table>
        <tr>
            <th>ID</th>
            <td><?= $game->getId() ?></td>
        </tr>
        <tr>
            <th>Title</th>
            <td><?= $game->getTitle() ?></td>
        </tr>
        <tr>
            <th>Category</th>
            <td><?= $game->getCategory() ?></td>
        </tr>
        <tr>
            <th>Minimum Players</th>
            <td><?= $game->getMinPlayers() ?></td>
        </tr>
        <tr>
            <th>Maximum Players</th>
            <td><?= $game->getMaxPlayers() ?></td>
        </tr>
        <tr>
            <th>Duration</th>
            <td><?= $game->getDuration() ?> minutes</td>
        </tr>
        <tr>
            <th>Difficulty</th>
            <td><?= $game->getDifficulty() ?></td>
        </tr>
        <tr>
            <th>Available</th>
            <td>
                <?php if ($game->getAvailable()): ?>
                    <span class="badge badge-yes">Yes</span>
                <?php else: ?>
                    <span class="badge badge-no">No</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Created at</th>
            <td><?= $game->getCreatedAt() ?? '—' ?></td>
        </tr>
    </table>

    <div class="actions">
        <a href="edit.php?id=<?= $game->getId() ?>" class="btn btn-warning">Edit</a>
        <a href="delete.php?id=<?= $game->getId() ?>" class="btn btn-danger">Delete</a>
        <a href="index.php" class="btn btn-primary">Back to list</a>
    </div>
</div>
</body>
</html>
