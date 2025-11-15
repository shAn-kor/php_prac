<?php

namespace Facade;

use Domain\Service\AuthService;
use Domain\Service\PostService;
use Domain\Service\CommentService;
use Infrastructure\Repository\UserRepository;
use Infrastructure\Repository\PostRepository;
use Infrastructure\Repository\CommentRepository;

class BoardFacade
{
    private AuthService $authService;
    private PostService $postService;
    private CommentService $commentService;

    public function __construct()
    {
        $userRepository = new UserRepository();
        $postRepository = new PostRepository();
        $commentRepository = new CommentRepository();
        
        $this->authService = new AuthService($userRepository);
        $this->postService = new PostService($postRepository);
        $this->commentService = new CommentService($commentRepository);
    }

    // Auth methods
    public function login(string $username, string $password): ?array
    {
        $user = $this->authService->login($username, $password);
        
        if ($user) {
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername()
            ];
        }
        
        return null;
    }

    public function register(string $username, string $password): ?array
    {
        $user = $this->authService->register($username, $password);
        
        if ($user) {
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername()
            ];
        }
        
        return null;
    }

    // Post methods
    public function getAllPosts(): array
    {
        $posts = $this->postService->getAllPosts();
        $result = [];
        
        foreach ($posts as $post) {
            $result[] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'author' => $post->getAuthor(),
                'user_id' => $post->getUserId(),
                'created_at' => $post->getCreatedAt()
            ];
        }
        
        return $result;
    }

    public function getPost(int $id): ?array
    {
        $post = $this->postService->getPost($id);
        
        if ($post) {
            return [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'author' => $post->getAuthor(),
                'user_id' => $post->getUserId(),
                'created_at' => $post->getCreatedAt()
            ];
        }
        
        return null;
    }

    public function createPost(string $title, string $content, string $author, int $userId): array
    {
        $post = $this->postService->createPost($title, $content, $author, $userId);
        
        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'author' => $post->getAuthor(),
            'user_id' => $post->getUserId(),
            'created_at' => $post->getCreatedAt()
        ];
    }

    public function updatePost(int $id, string $title, string $content, int $userId): bool
    {
        return $this->postService->updatePost($id, $title, $content, $userId);
    }

    public function deletePost(int $id, int $userId): bool
    {
        return $this->postService->deletePost($id, $userId);
    }

    public function canEditPost(int $postId, int $userId): bool
    {
        $post = $this->postService->getPost($postId);
        return $post && $post->canBeEditedBy($userId);
    }

    // Comment methods
    public function getCommentsByPostId(int $postId): array
    {
        $comments = $this->commentService->getCommentsByPostId($postId);
        $result = [];
        
        foreach ($comments as $comment) {
            $result[] = [
                'id' => $comment->getId(),
                'post_id' => $comment->getPostId(),
                'content' => $comment->getContent(),
                'author' => $comment->getAuthor(),
                'user_id' => $comment->getUserId(),
                'created_at' => $comment->getCreatedAt()
            ];
        }
        
        return $result;
    }

    public function createComment(int $postId, string $content, string $author, int $userId): array
    {
        $comment = $this->commentService->createComment($postId, $content, $author, $userId);
        
        return [
            'id' => $comment->getId(),
            'post_id' => $comment->getPostId(),
            'content' => $comment->getContent(),
            'author' => $comment->getAuthor(),
            'user_id' => $comment->getUserId(),
            'created_at' => $comment->getCreatedAt()
        ];
    }

    public function updateComment(int $id, string $content, int $userId): bool
    {
        return $this->commentService->updateComment($id, $content, $userId);
    }

    public function deleteComment(int $id, int $userId): bool
    {
        return $this->commentService->deleteComment($id, $userId);
    }
}