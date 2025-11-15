<?php

namespace Infrastructure\Repository;

use Domain\Entity\Comment;
use Domain\Repository\CommentRepositoryInterface;
use Infrastructure\Database\DatabaseConnection;
use PDO;

class CommentRepository implements CommentRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance();
    }

    public function findByPostId(int $postId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at ASC");
        $stmt->execute([$postId]);
        $comments = [];
        
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = new Comment(
                $data['id'],
                $data['post_id'],
                $data['content'],
                $data['author'],
                $data['user_id'],
                $data['created_at']
            );
        }
        
        return $comments;
    }

    public function findById(int $id): ?Comment
    {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        return new Comment(
            $data['id'],
            $data['post_id'],
            $data['content'],
            $data['author'],
            $data['user_id'],
            $data['created_at']
        );
    }

    public function create(int $postId, string $content, string $author, int $userId): Comment
    {
        $stmt = $this->pdo->prepare("INSERT INTO comments (post_id, content, author, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$postId, $content, $author, $userId]);
        
        $id = $this->pdo->lastInsertId();
        return new Comment($id, $postId, $content, $author, $userId, date('Y-m-d H:i:s'));
    }

    public function update(int $id, string $content): bool
    {
        $stmt = $this->pdo->prepare("UPDATE comments SET content=? WHERE id=?");
        return $stmt->execute([$content, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id=?");
        return $stmt->execute([$id]);
    }
}