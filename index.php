<?php
require_once 'config.php';
require_once 'Game.php';

$games = Game::getAll($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Board Game Library</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1000px;
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
            background-color: #1a4a7a;
            color: #fff;
        }
        tr:nth-child(even) { background-color: #f7f9fb; }
        tr:hover           { background-color: #e8f0f9; }
        .btn {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            cursor: pointer;
            border: none;
            margin: 1px;
        }
        .btn-primary { background-color: #1a4a7a; color: #fff; }
        .btn-success { background-color: #28a745; color: #fff; }
        .btn-warning { background-color: #e0a800; color: #fff; }
        .btn-danger  { background-color: #dc3545; color: #fff; }
        .btn:hover   { opacity: 0.85; }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-yes { background-color: #28a745; color: #fff; }
        .badge-no  { background-color: #dc3545; color: #fff; }
        .empty { color: #888; font-style: italic; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Board Game Library</h1>

    <a href="create.php" class="btn btn-success">+ Add a Game</a>

    <?php if (empty($games)): ?>
        <p class="empty">No games in the library yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Players</th>
                    <th>Duration</th>
                    <th>Difficulty</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $game): ?>
                <tr>
                    <td><?= $game->getId() ?></td>

                    <td><?= $game->getTitle() ?></td>
                    <td><?= $game->getCategory() ?></td>

                    <td><?= $game->getMinPlayers() ?>-<?= $game->getMaxPlayers() ?></td>

                    <td><?= $game->getDuration() ?> min</td>
                    <td><?= $game->getDifficulty() ?></td>

                    <td>
                        <?php if ($game->getAvailable()): ?>
                            <span class="badge badge-yes">Yes</span>
                        <?php else: ?>
                            <span class="badge badge-no">No</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="show.php?id=<?= $game->getId() ?>" class="btn btn-primary">View</a>
                        <a href="edit.php?id=<?= $game->getId() ?>" class="btn btn-warning">Edit</a>
                        <a href="delete.php?id=<?= $game->getId() ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
