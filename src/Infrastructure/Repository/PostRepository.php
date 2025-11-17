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
                $data['user_id'] ?? 0,
                $data['created_at']
            );
        }
        
        return $posts;
    }

    public function findPaginated(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->pdo->prepare("SELECT * FROM posts ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $posts = [];
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        foreach ($results as $data) {
            $posts[] = new Post(
                (int)$data['id'],
                $data['title'],
                $data['content'],
                $data['author'],
                (int)($data['user_id'] ?? 0),
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
            $data['user_id'] ?? 0,
            $data['created_at']
        );
    }

    public function create(string $title, string $content, string $author, int $userId): Post
    {
        $koreaTime = new \DateTime('now', new \DateTimeZone('Asia/Seoul'));
        $createdAt = $koreaTime->format('Y-m-d H:i:s');
        
        $stmt = $this->pdo->prepare("INSERT INTO posts (title, content, author, user_id, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $content, $author, $userId, $createdAt]);
        
        $id = $this->pdo->lastInsertId();
        return new Post($id, $title, $content, $author, $userId, $createdAt);
    }

    public function update(int $id, string $title, string $content): bool
    {
        $stmt = $this->pdo->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
        return $stmt->execute([$title, $content, $id]);
    }

    public function delete(int $id): bool
    {
        try {
            $this->pdo->beginTransaction();
            
            // 댓글 삭제
            $stmt = $this->pdo->prepare("DELETE FROM comments WHERE post_id=?");
            $stmt->execute([$id]);
            
            // 첨부파일 삭제
            $stmt = $this->pdo->prepare("DELETE FROM attachments WHERE post_id=?");
            $stmt->execute([$id]);
            
            // 게시글 삭제
            $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id=?");
            $result = $stmt->execute([$id]);
            
            $this->pdo->commit();
            return $result;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
}