<?php

namespace Domain\Service;

use Domain\Entity\Attachment;
use Domain\Repository\AttachmentRepositoryInterface;

class AttachmentService
{
    private AttachmentRepositoryInterface $attachmentRepository;
    private array $allowedMimeTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain', 'text/csv'
    ];
    private int $maxFileSize = 10485760; // 10MB

    public function __construct(AttachmentRepositoryInterface $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    public function getAttachmentsByPostId(int $postId): array
    {
        return $this->attachmentRepository->findByPostId($postId);
    }

    public function uploadFiles(int $postId, array $files): array
    {
        $uploadedFiles = [];
        
        foreach ($files as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $result = $this->processFile($postId, $file);
                if ($result) {
                    $uploadedFiles[] = $result;
                }
            }
        }
        
        return $uploadedFiles;
    }

    private function processFile(int $postId, array $file): ?Attachment
    {
        // 파일 검증
        if (!$this->validateFile($file)) {
            return null;
        }

        $originalName = $file['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $storedName = uniqid() . '_' . time() . '.' . $extension;
        $uploadDir = __DIR__ . '/../../../public/uploads/';
        $filePath = $uploadDir . $storedName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $this->attachmentRepository->create(
                $postId,
                $originalName,
                $storedName,
                '/uploads/' . $storedName,
                $file['size'],
                $file['type']
            );
        }

        return null;
    }

    private function validateFile(array $file): bool
    {
        // 파일 크기 검증
        if ($file['size'] > $this->maxFileSize) {
            return false;
        }

        // MIME 타입 검증
        if (!in_array($file['type'], $this->allowedMimeTypes)) {
            return false;
        }

        // 파일 확장자 검증
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv'];
        
        return in_array($extension, $allowedExtensions);
    }
}