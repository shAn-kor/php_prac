<?php

namespace Domain\Service;

use Domain\Entity\Comment;
use Domain\Repository\CommentRepositoryInterface;

class CommentService
{
    private CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getCommentsByPostId(int $postId): array
    {
        return $this->commentRepository->findByPostId($postId);
    }

    public function createComment(int $postId, string $content, string $author, int $userId): Comment
    {
        return $this->commentRepository->create($postId, $content, $author, $userId);
    }

    public function updateComment(int $id, string $content, int $userId): bool
    {
        $comment = $this->commentRepository->findById($id);
        
        if (!$comment || !$comment->canBeEditedBy($userId)) {
            return false;
        }
        
        return $this->commentRepository->update($id, $content);
    }

    public function deleteComment(int $id, int $userId): bool
    {
        $comment = $this->commentRepository->findById($id);
        
        if (!$comment || !$comment->canBeEditedBy($userId)) {
            return false;
        }
        
        return $this->commentRepository->delete($id);
    }
}