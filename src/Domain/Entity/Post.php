<?php

namespace Domain\Entity;

class Post
{
    private ?int $id;
    private string $title;
    private string $content;
    private string $author;
    private int $userId;
    private ?string $createdAt;

    public function __construct(?int $id, string $title, string $content, string $author, int $userId, ?string $createdAt = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->author = $author;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getContent(): string { return $this->content; }
    public function getAuthor(): string { return $this->author; }
    public function getUserId(): int { return $this->userId; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    
    public function canBeEditedBy(int $userId): bool
    {
        return $this->userId === $userId;
    }
}