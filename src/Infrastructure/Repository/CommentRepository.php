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
        $stmt->bindValue(1, $postId, PDO::PARAM_INT);
        $stmt->execute();
        $comments = [];
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        foreach ($results as $data) {
            $comments[] = new Comment(
                (int)$data['id'],
                (int)$data['post_id'],
                $data['content'],
                $data['author'],
                (int)$data['user_id'],
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
        $koreaTime = new \DateTime('now', new \DateTimeZone('Asia/Seoul'));
        $createdAt = $koreaTime->format('Y-m-d H:i:s');
        
        $stmt = $this->pdo->prepare("INSERT INTO comments (post_id, content, author, user_id, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$postId, $content, $author, $userId, $createdAt]);
        
        $id = $this->pdo->lastInsertId();
        return new Comment($id, $postId, $content, $author, $userId, $createdAt);
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

    public function countByPostId(int $postId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
        $stmt->execute([$postId]);
        return (int)$stmt->fetchColumn();
    }
}