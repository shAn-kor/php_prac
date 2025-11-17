<?php

use PHPUnit\Framework\TestCase;
use Domain\Service\PostService;
use Domain\Entity\Post;
use Domain\Repository\PostRepositoryInterface;

class PostServiceTest extends TestCase
{
    private PostService $postService;
    private PostRepositoryInterface $postRepository;

    protected function setUp(): void
    {
        $this->postRepository = $this->createMock(PostRepositoryInterface::class);
        $this->postService = new PostService($this->postRepository);
    }

    public function testGetAllPosts(): void
    {
        $posts = [
            new Post(1, 'Title 1', 'Content 1', 'Author 1', 1, '2024-01-01 00:00:00'),
            new Post(2, 'Title 2', 'Content 2', 'Author 2', 2, '2024-01-02 00:00:00')
        ];

        $this->postRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($posts);

        $result = $this->postService->getAllPosts();
        
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Post::class, $result[0]);
    }

    public function testGetPost(): void
    {
        $post = new Post(1, 'Test Title', 'Test Content', 'Test Author', 1, '2024-01-01 00:00:00');

        $this->postRepository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($post);

        $result = $this->postService->getPost(1);
        
        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals('Test Title', $result->getTitle());
    }

    public function testCreatePost(): void
    {
        $post = new Post(1, 'New Title', 'New Content', 'Author', 1, '2024-01-01 00:00:00');

        $this->postRepository
            ->expects($this->once())
            ->method('create')
            ->with('New Title', 'New Content', 'Author', 1)
            ->willReturn($post);

        $result = $this->postService->createPost('New Title', 'New Content', 'Author', 1);
        
        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals('New Title', $result->getTitle());
    }

    public function testUpdatePostByOwner(): void
    {
        $post = new Post(1, 'Old Title', 'Old Content', 'Author', 1, '2024-01-01 00:00:00');

        $this->postRepository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($post);

        $this->postRepository
            ->expects($this->once())
            ->method('update')
            ->with(1, 'New Title', 'New Content')
            ->willReturn(true);

        $result = $this->postService->updatePost(1, 'New Title', 'New Content', 1);
        
        $this->assertTrue($result);
    }

    public function testUpdatePostByNonOwner(): void
    {
        $post = new Post(1, 'Title', 'Content', 'Author', 1, '2024-01-01 00:00:00');

        $this->postRepository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($post);

        $result = $this->postService->updatePost(1, 'New Title', 'New Content', 2);
        
        $this->assertFalse($result);
    }

    public function testDeletePostByOwner(): void
    {
        $post = new Post(1, 'Title', 'Content', 'Author', 1, '2024-01-01 00:00:00');

        $this->postRepository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($post);

        $this->postRepository
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        $result = $this->postService->deletePost(1, 1);
        
        $this->assertTrue($result);
    }

    public function testDeletePostByNonOwner(): void
    {
        $post = new Post(1, 'Title', 'Content', 'Author', 1, '2024-01-01 00:00:00');

        $this->postRepository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($post);

        $result = $this->postService->deletePost(1, 2);
        
        $this->assertFalse($result);
    }
}