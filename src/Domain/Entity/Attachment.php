<?php

namespace Domain\Entity;

class Attachment
{
    private ?int $id;
    private int $postId;
    private string $originalName;
    private string $storedName;
    private string $filePath;
    private int $fileSize;
    private string $mimeType;
    private ?string $createdAt;

    public function __construct(?int $id, int $postId, string $originalName, string $storedName, string $filePath, int $fileSize, string $mimeType, ?string $createdAt = null)
    {
        $this->id = $id;
        $this->postId = $postId;
        $this->originalName = $originalName;
        $this->storedName = $storedName;
        $this->filePath = $filePath;
        $this->fileSize = $fileSize;
        $this->mimeType = $mimeType;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getPostId(): int { return $this->postId; }
    public function getOriginalName(): string { return $this->originalName; }
    public function getStoredName(): string { return $this->storedName; }
    public function getFilePath(): string { return $this->filePath; }
    public function getFileSize(): int { return $this->fileSize; }
    public function getMimeType(): string { return $this->mimeType; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    
    public function getFormattedSize(): string
    {
        $bytes = $this->fileSize;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}