<?php

class Game
{
    private ?int    $id;
    private string  $title;
    private string  $category;
    private int     $min_players;
    private int     $max_players;
    private int     $duration;
    private string  $difficulty;
    private int     $available;
    private ?string $created_at;

    public function __construct(
        ?int    $id,
        string  $title,
        string  $category,
        int     $min_players,
        int     $max_players,
        int     $duration,
        string  $difficulty,
        int     $available,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->category = $category;
        $this->min_players = $min_players;
        $this->max_players = $max_players;
        $this->duration = $duration;
        $this->difficulty = $difficulty;
        $this->available = $available;
        $this->created_at = $created_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getMinPlayers(): int
    {
        return $this->min_players;
    }

    public function getMaxPlayers(): int
    {
        return $this->max_players;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getDifficulty(): string
    {
        return $this->difficulty;
    }

    public function getAvailable(): int
    {
        return $this->available;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    private static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['title'],
            (string) $row['category'],
            (int) $row['min_players'],
            (int) $row['max_players'],
            (int) $row['duration'],
            (string) $row['difficulty'],
            (int) $row['available'],
            isset($row['created_at']) ? (string) $row['created_at'] : null
        );
    }

    public static function getAll(PDO $pdo): array
    {
        $stmt = $pdo->query('SELECT * FROM games ORDER BY id ASC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $games = [];
        foreach ($rows as $row) {
            $games[] = self::fromRow($row);
        }

        return $games;
    }

    public static function getById(PDO $pdo, int $id): ?self
    {
        $stmt = $pdo->prepare('SELECT * FROM games WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return self::fromRow($row);
    }

    public function create(PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            'INSERT INTO games (title, category, min_players, max_players, duration, difficulty, available)
             VALUES (:title, :category, :min_players, :max_players, :duration, :difficulty, :available)'
        );

        return $stmt->execute([
            'title' => $this->title,
            'category' => $this->category,
            'min_players' => $this->min_players,
            'max_players' => $this->max_players,
            'duration' => $this->duration,
            'difficulty' => $this->difficulty,
            'available' => $this->available,
        ]);
    }

    public function update(PDO $pdo): bool
    {
        if ($this->id === null) {
            return false;
        }

        $stmt = $pdo->prepare(
            'UPDATE games
             SET title       = :title,
                 category    = :category,
                 min_players = :min_players,
                 max_players = :max_players,
                 duration    = :duration,
                 difficulty  = :difficulty,
                 available   = :available
             WHERE id = :id'
        );

        return $stmt->execute([
            'title' => $this->title,
            'category' => $this->category,
            'min_players' => $this->min_players,
            'max_players' => $this->max_players,
            'duration' => $this->duration,
            'difficulty' => $this->difficulty,
            'available' => $this->available,
            'id' => $this->id,
        ]);
    }

    public function delete(PDO $pdo): bool
    {
        if ($this->id === null) {
            return false;
        }

        $stmt = $pdo->prepare('DELETE FROM games WHERE id = :id');
        return $stmt->execute(['id' => $this->id]);
    }
}
