<?php
require_once 'config.php';
require_once 'Game.php';

$errors      = [];
$title       = '';
$category    = '';
$min_players = 2;
$max_players = 4;
$duration    = 30;
$difficulty  = 'easy';
$available   = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']       ?? '');
    $category    = trim($_POST['category']    ?? '');
    $min_players = (int) ($_POST['min_players'] ?? 0);
    $max_players = (int) ($_POST['max_players'] ?? 0);
    $duration    = (int) ($_POST['duration']    ?? 0);
    $difficulty  = $_POST['difficulty'] ?? '';
    $available   = isset($_POST['available']) ? 1 : 0;

    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    if ($category === '') {
        $errors[] = 'Category is required.';
    }

    if ($min_players < 1) {
        $errors[] = 'Minimum players must be >= 1.';
    }

    if ($max_players < $min_players) {
        $errors[] = 'Maximum players must be >= minimum players.';
    }

    if ($duration <= 0) {
        $errors[] = 'Duration must be greater than 0.';
    }

    $allowed_difficulties = ['easy', 'medium', 'difficult'];
    if (!in_array($difficulty, $allowed_difficulties, true)) {
        $errors[] = 'The selected difficulty is invalid.';
    }

    if (empty($errors)) {
        $game = new Game(null, $title, $category, $min_players, $max_players, $duration, $difficulty, $available);

        $game->create($pdo);

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 650px;
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
        .error-box {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error-box ul { margin: 0; padding-left: 20px; }
        .form-group { margin-bottom: 16px; }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input:focus, select:focus {
            border-color: #1a4a7a;
            outline: none;
            box-shadow: 0 0 0 2px rgba(26,74,122,0.2);
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .checkbox-group input { width: auto; }
        .required { color: #dc3545; }
        .btn {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            border: none;
            margin: 2px;
        }
        .btn-success { background-color: #28a745; color: #fff; }
        .btn-primary { background-color: #1a4a7a; color: #fff; }
        .btn:hover   { opacity: 0.85; }
        .form-actions { margin-top: 24px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Add a Game</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <strong>Please fix the following errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="create.php">

        <div class="form-group">
            <label for="title">Title <span class="required">*</span></label>
            <input type="text" id="title" name="title"
                   value="<?= $title ?>" required>
        </div>

        <div class="form-group">
            <label for="category">Category <span class="required">*</span></label>
            <input type="text" id="category" name="category"
                   value="<?= $category ?>" required>
        </div>

        <div class="form-group">
            <label for="min_players">Minimum Players <span class="required">*</span></label>
            <input type="number" id="min_players" name="min_players"
                   value="<?= $min_players ?>" min="1" required>
        </div>

        <div class="form-group">
            <label for="max_players">Maximum Players <span class="required">*</span></label>
            <input type="number" id="max_players" name="max_players"
                   value="<?= $max_players ?>" min="1" required>
        </div>

        <div class="form-group">
            <label for="duration">Duration (minutes) <span class="required">*</span></label>
            <input type="number" id="duration" name="duration"
                   value="<?= $duration ?>" min="1" required>
        </div>

        <div class="form-group">
            <label for="difficulty">Difficulty <span class="required">*</span></label>
            <select id="difficulty" name="difficulty" required>
                <option value="easy"     <?= $difficulty === 'easy'     ? 'selected' : '' ?>>Easy</option>
                <option value="medium"   <?= $difficulty === 'medium'   ? 'selected' : '' ?>>Medium</option>
                <option value="difficult"<?= $difficulty === 'difficult' ? 'selected' : '' ?>>Difficult</option>
            </select>
        </div>

        <div class="form-group">
            <label>Available</label>
            <div class="checkbox-group">
                <input type="checkbox" id="available" name="available" value="1"
                       <?= $available ? 'checked' : '' ?>>
                <label for="available" style="font-weight:normal;">Yes, this game is available</label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Save</button>
            <a href="index.php" class="btn btn-primary">Cancel</a>
        </div>

    </form>
</div>
</body>
</html>
