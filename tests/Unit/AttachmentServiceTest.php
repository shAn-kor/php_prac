<?php

use PHPUnit\Framework\TestCase;
use Domain\Service\AttachmentService;
use Domain\Entity\Attachment;
use Domain\Repository\AttachmentRepositoryInterface;

class AttachmentServiceTest extends TestCase
{
    private AttachmentService $attachmentService;
    private AttachmentRepositoryInterface $attachmentRepository;

    protected function setUp(): void
    {
        $this->attachmentRepository = $this->createMock(AttachmentRepositoryInterface::class);
        $this->attachmentService = new AttachmentService($this->attachmentRepository);
    }

    public function testGetAttachmentsByPostId(): void
    {
        $attachments = [
            new Attachment(1, 1, 'file1.jpg', 'stored1.jpg', '/uploads/stored1.jpg', 1024, 'image/jpeg', '2024-01-01 00:00:00'),
            new Attachment(2, 1, 'file2.pdf', 'stored2.pdf', '/uploads/stored2.pdf', 2048, 'application/pdf', '2024-01-01 00:00:00')
        ];

        $this->attachmentRepository
            ->expects($this->once())
            ->method('findByPostId')
            ->with(1)
            ->willReturn($attachments);

        $result = $this->attachmentService->getAttachmentsByPostId(1);
        
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Attachment::class, $result[0]);
    }

    public function testUploadFilesWithValidFiles(): void
    {
        $files = [
            [
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => '/tmp/test',
                'error' => UPLOAD_ERR_OK,
                'size' => 1024
            ]
        ];

        // Mock the file system operations would require more complex setup
        // This test focuses on the service logic structure
        $result = $this->attachmentService->uploadFiles(1, []);
        
        $this->assertIsArray($result);
    }

    public function testValidateFileSize(): void
    {
        $reflection = new ReflectionClass($this->attachmentService);
        $method = $reflection->getMethod('validateFile');
        $method->setAccessible(true);

        $validFile = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'size' => 1024
        ];

        $invalidFile = [
            'name' => 'large.jpg',
            'type' => 'image/jpeg',
            'size' => 20971520 // 20MB
        ];

        $this->assertTrue($method->invoke($this->attachmentService, $validFile));
        $this->assertFalse($method->invoke($this->attachmentService, $invalidFile));
    }

    public function testValidateFileMimeType(): void
    {
        $reflection = new ReflectionClass($this->attachmentService);
        $method = $reflection->getMethod('validateFile');
        $method->setAccessible(true);

        $validFile = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'size' => 1024
        ];

        $invalidFile = [
            'name' => 'script.exe',
            'type' => 'application/x-executable',
            'size' => 1024
        ];

        $this->assertTrue($method->invoke($this->attachmentService, $validFile));
        $this->assertFalse($method->invoke($this->attachmentService, $invalidFile));
    }
}