<?php

namespace Infrastructure\Repository;

use Domain\Entity\Attachment;
use Domain\Repository\AttachmentRepositoryInterface;
use Infrastructure\Database\DatabaseConnection;
use PDO;

class AttachmentRepository implements AttachmentRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance();
    }

    public function findByPostId(int $postId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM attachments WHERE post_id = ? ORDER BY created_at ASC");
        $stmt->execute([$postId]);
        $attachments = [];
        
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $attachments[] = new Attachment(
                $data['id'],
                $data['post_id'],
                $data['original_name'],
                $data['stored_name'],
                $data['file_path'],
                $data['file_size'],
                $data['mime_type'],
                $data['created_at']
            );
        }
        
        return $attachments;
    }

    public function create(int $postId, string $originalName, string $storedName, string $filePath, int $fileSize, string $mimeType): Attachment
    {
        $stmt = $this->pdo->prepare("INSERT INTO attachments (post_id, original_name, stored_name, file_path, file_size, mime_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$postId, $originalName, $storedName, $filePath, $fileSize, $mimeType]);
        
        $id = $this->pdo->lastInsertId();
        return new Attachment($id, $postId, $originalName, $storedName, $filePath, $fileSize, $mimeType, date('Y-m-d H:i:s'));
    }

    public function deleteByPostId(int $postId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM attachments WHERE post_id = ?");
        return $stmt->execute([$postId]);
    }
}