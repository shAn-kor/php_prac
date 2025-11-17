<?php

namespace Domain\Repository;

use Domain\Entity\Attachment;

interface AttachmentRepositoryInterface
{
    public function findByPostId(int $postId): array;
    public function create(int $postId, string $originalName, string $storedName, string $filePath, int $fileSize, string $mimeType): Attachment;
    public function deleteByPostId(int $postId): bool;
}