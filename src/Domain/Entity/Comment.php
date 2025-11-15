<?php

namespace Domain\Entity;

class Comment
{
    private ?int $id;
    private int $postId;
    private string $content;
    private string $author;
    private int $userId;
    private ?string $createdAt;

    public function __construct(?int $id, int $postId, string $content, string $author, int $userId, ?string $createdAt = null)
    {
        $this->id = $id;
        $this->postId = $postId;
        $this->content = $content;
        $this->author = $author;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getPostId(): int { return $this->postId; }
    public function getContent(): string { return $this->content; }
    public function getAuthor(): string { return $this->author; }
    public function getUserId(): int { return $this->userId; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    
    public function canBeEditedBy(int $userId): bool
    {
        return $this->userId === $userId;
    }
}