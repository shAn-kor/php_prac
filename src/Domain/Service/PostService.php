<?php

namespace Domain\Service;

use Domain\Entity\Post;
use Domain\Repository\PostRepositoryInterface;

class PostService
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getAllPosts(): array
    {
        return $this->postRepository->findAll();
    }

    public function getPostsPaginated(int $page, int $limit): array
    {
        return $this->postRepository->findPaginated($page, $limit);
    }

    public function getPost(int $id): ?Post
    {
        return $this->postRepository->findById($id);
    }

    public function createPost(string $title, string $content, string $author, int $userId): Post
    {
        return $this->postRepository->create($title, $content, $author, $userId);
    }

    public function updatePost(int $id, string $title, string $content, int $userId): bool
    {
        $post = $this->postRepository->findById($id);
        
        if (!$post || !$post->canBeEditedBy($userId)) {
            return false;
        }
        
        return $this->postRepository->update($id, $title, $content);
    }

    public function deletePost(int $id, int $userId): bool
    {
        $post = $this->postRepository->findById($id);
        
        if (!$post || !$post->canBeEditedBy($userId)) {
            return false;
        }
        
        return $this->postRepository->delete($id);
    }
}