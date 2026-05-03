CREATE DATABASE IF NOT EXISTS exam_php_crud
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE exam_php_crud;

CREATE TABLE IF NOT EXISTS games (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(100) NOT NULL,
    category    VARCHAR(50)  NOT NULL,
    min_players INT          NOT NULL,
    max_players INT          NOT NULL,
    duration    INT          NOT NULL,
    difficulty  ENUM('easy', 'medium', 'difficult') NOT NULL,
    available   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO games (title, category, min_players, max_players, duration, difficulty, available)
VALUES
    ('Kingdom Builder',      'strategy', 2, 4, 45, 'medium',    1),
    ('Zombie Party',         'atmosphere',  3, 8, 30, 'easy',   1),
    ('Code Secret',          'deduction', 2, 8, 20, 'easy',   0),
    ('Terraforming Mars Mini','strategy',1, 5, 90, 'difficult',1);
