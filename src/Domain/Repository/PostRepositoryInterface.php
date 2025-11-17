<?php

namespace Domain\Repository;

use Domain\Entity\Post;

interface PostRepositoryInterface
{
    public function findAll(): array;
    public function findPaginated(int $page, int $limit): array;
    public function findById(int $id): ?Post;
    public function create(string $title, string $content, string $author, int $userId): Post;
    public function update(int $id, string $title, string $content): bool;
    public function delete(int $id): bool;
}