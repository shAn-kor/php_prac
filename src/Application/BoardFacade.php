<?php

namespace Application;

use Domain\Service\PostService;
use Domain\Service\CommentService;
use Infrastructure\Repository\PostRepository;
use Infrastructure\Repository\CommentRepository;

class BoardFacade
{
    private PostService $postService;
    private CommentService $commentService;

    public function __construct()
    {
        $postRepository = new PostRepository();
        $commentRepository = new CommentRepository();
        $this->postService = new PostService($postRepository);
        $this->commentService = new CommentService($commentRepository);
    }

    public function getPostsPaginated(int $page, int $limit): array
    {
        $posts = $this->postService->getPostsPaginated($page, $limit);
        $result = [];
        
        foreach ($posts as $post) {
            $commentCount = $this->commentService->getCommentCountByPostId($post->getId());
            $result[] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'author' => $post->getAuthor(),
                'user_id' => $post->getUserId(),
                'created_at' => $post->getCreatedAt(),
                'comment_count' => $commentCount
            ];
        }
        
        return $result;
    }
}