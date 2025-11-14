<?php

namespace Infrastructure\Repository;

use Domain\Entity\Post;
use Domain\Repository\PostRepositoryInterface;
use Infrastructure\Database\DatabaseConnection;
use PDO;

class PostRepository implements PostRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM posts ORDER BY id DESC");
        $posts = [];
        
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $posts[] = new Post(
                $data['id'],
                $data['title'],
                $data['content'],
                $data['author'],
                $data['user_id'],
                $data['created_at']
            );
        }
        
        return $posts;
    }

    public function findById(int $id): ?Post
    {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        return new Post(
            $data['id'],
            $data['title'],
            $data['content'],
            $data['author'],
            $data['user_id'],
            $data['created_at']
        );
    }

    public function create(string $title, string $content, string $author, int $userId): Post
    {
        $stmt = $this->pdo->prepare("INSERT INTO posts (title, content, author, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $author, $userId]);
        
        $id = $this->pdo->lastInsertId();
        return new Post($id, $title, $content, $author, $userId, date('Y-m-d H:i:s'));
    }

    public function update(int $id, string $title, string $content): bool
    {
        $stmt = $this->pdo->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
        return $stmt->execute([$title, $content, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id=?");
        return $stmt->execute([$id]);
    }
}