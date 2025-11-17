<?php

namespace Domain\Repository;

use Domain\Entity\Comment;

interface CommentRepositoryInterface
{
    public function findByPostId(int $postId): array;
    public function findById(int $id): ?Comment;
    public function create(int $postId, string $content, string $author, int $userId): Comment;
    public function update(int $id, string $content): bool;
    public function delete(int $id): bool;
    public function countByPostId(int $postId): int;
}