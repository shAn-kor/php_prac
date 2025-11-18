<?php

namespace Presentation;

use Application\BoardFacade;
use Domain\Service\LoginService;
use Domain\Service\PostService;
use Domain\Service\CommentService;
use Domain\Service\AttachmentService;
use Infrastructure\Repository\UserRepository;
use Infrastructure\Repository\PostRepository;
use Infrastructure\Repository\CommentRepository;
use Infrastructure\Repository\AttachmentRepository;
use Infrastructure\Security\SecurityHelper;

class BoardController
{
    private BoardFacade $boardFacade;
    private LoginService $loginService;
    private PostService $postService;
    private CommentService $commentService;
    private AttachmentService $attachmentService;

    public function __construct()
    {
        $this->boardFacade = new BoardFacade();
        $this->loginService = new LoginService(new UserRepository());
        $this->postService = new PostService(new PostRepository());
        $this->commentService = new CommentService(new CommentRepository());
        $this->attachmentService = new AttachmentService(new AttachmentRepository());
    }

    public function handleRequest(): void
    {
        session_start();
        
        $action = $_GET['action'] ?? 'list';
        $id = $_GET['id'] ?? null;

        // POST 크기 초과 확인
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $_SESSION['upload_error'] = '파일 크기가 너무 큽니다. 최대 8MB까지 업로드 가능합니다.';
            $this->redirect('/');
            return;
        }

        // POST 요청 처리
        if ($_POST) {
            // CSRF 토큰 검증 (로그인/회원가입 제외)
            if (!in_array($action, ['login', 'register']) && !SecurityHelper::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                die('CSRF token validation failed');
            }
            $this->handlePostRequest($action, $id);
            return;
        }

        // GET 요청 처리
        $this->handleGetRequest($action, $id);
    }

    private function handlePostRequest(string $action, ?string $id): void
    {
        switch ($action) {
            case 'login':
                $username = SecurityHelper::preventSQLInjection($_POST['username']);
                $password = SecurityHelper::preventSQLInjection($_POST['password']);
                $user = $this->loginService->login($username, $password);
                if ($user) {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['username'] = $user->getUsername();
                    $this->redirect('/');
                } else {
                    $this->renderLogin("아이디 또는 비밀번호가 잘못되었습니다.");
                }
                break;

            case 'register':
                $username = SecurityHelper::preventSQLInjection($_POST['username']);
                $password = SecurityHelper::preventSQLInjection($_POST['password']);
                $user = $this->loginService->register($username, $password);
                if ($user) {
                    $this->redirect('/?action=login');
                } else {
                    $this->renderRegister("이미 존재하는 아이디입니다.");
                }
                break;

            case 'store':
                if ($this->isLoggedIn()) {
                    $title = SecurityHelper::preventSQLInjection($_POST['title']);
                    $content = SecurityHelper::preventSQLInjection($_POST['content']);
                    $post = $this->postService->createPost(
                        SecurityHelper::sanitizeInput($title),
                        SecurityHelper::preventXSS($content),
                        $_SESSION['username'],
                        $_SESSION['user_id']
                    );
                    
                    // 파일 업로드 처리
                    if (!empty($_FILES['attachments']['name'][0])) {
                        $uploadError = $this->checkFileUploadErrors($_FILES['attachments']);
                        if ($uploadError) {
                            $_SESSION['upload_error'] = $uploadError;
                        } else {
                            $files = $this->reorganizeFilesArray($_FILES['attachments']);
                            $this->attachmentService->uploadFiles($post->getId(), $files);
                        }
                    }
                }
                $this->redirect('/');
                break;

            case 'update':
                if ($this->isLoggedIn() && $id) {
                    $title = SecurityHelper::preventSQLInjection($_POST['title']);
                    $content = SecurityHelper::preventSQLInjection($_POST['content']);
                    $this->postService->updatePost(
                        (int)$id,
                        $title,
                        $content,
                        $_SESSION['user_id']
                    );
                    
                    // 첨부파일 삭제 처리
                    if (!empty($_POST['delete_attachments'])) {
                        foreach ($_POST['delete_attachments'] as $attachmentId) {
                            $this->attachmentService->deleteAttachment((int)$attachmentId);
                        }
                    }
                    
                    // 새 첨부파일 추가
                    if (!empty($_FILES['attachments']['name'][0])) {
                        $uploadError = $this->checkFileUploadErrors($_FILES['attachments']);
                        if ($uploadError) {
                            $_SESSION['upload_error'] = $uploadError;
                        } else {
                            $files = $this->reorganizeFilesArray($_FILES['attachments']);
                            $this->attachmentService->uploadFiles((int)$id, $files);
                        }
                    }
                    
                    $this->redirect("/?action=show&id=$id");
                }
                break;

            case 'delete':
                if ($this->isLoggedIn() && $id) {
                    // 첫부파일 먼저 삭제
                    $this->attachmentService->deleteAttachmentsByPostId((int)$id);
                    // 게시글 삭제
                    $this->postService->deletePost((int)$id, $_SESSION['user_id']);
                }
                $this->redirect('/');
                break;

            case 'comment_store':
                if ($this->isLoggedIn() && $id) {
                    $content = SecurityHelper::preventSQLInjection($_POST['content']);
                    $this->commentService->createComment(
                        (int)$id,
                        SecurityHelper::preventXSS($content),
                        $_SESSION['username'],
                        $_SESSION['user_id']
                    );
                    $this->redirect("/?action=show&id=$id");
                }
                break;

            case 'comment_update':
                if ($this->isLoggedIn() && $id) {
                    $content = SecurityHelper::preventSQLInjection($_POST['content']);
                    $this->commentService->updateComment(
                        (int)$id,
                        $content,
                        $_SESSION['user_id']
                    );
                    $postId = $_POST['post_id'];
                    $this->redirect("/?action=show&id=$postId");
                }
                break;

            case 'comment_delete':
                if ($this->isLoggedIn() && $id) {
                    $postId = $_POST['post_id'];
                    $this->commentService->deleteComment((int)$id, $_SESSION['user_id']);
                    $this->redirect("/?action=show&id=$postId");
                }
                break;
        }
    }

    private function handleGetRequest(string $action, ?string $id): void
    {
        switch ($action) {
            case 'logout':
                session_destroy();
                $this->redirect('/');
                break;

            case 'login':
                if (!$this->isLoggedIn()) {
                    $this->renderLogin();
                } else {
                    $this->redirect('/');
                }
                break;

            case 'register':
                if (!$this->isLoggedIn()) {
                    $this->renderRegister();
                } else {
                    $this->redirect('/');
                }
                break;

            case 'show':
                $post = $this->postService->getPost((int)$id);
                $postArray = $post ? $this->convertPostToArray($post) : null;
                $comments = $post ? $this->convertCommentsToArray($this->commentService->getCommentsByPostId((int)$id)) : [];
                $attachments = $post ? $this->convertAttachmentsToArray($this->attachmentService->getAttachmentsByPostId((int)$id)) : [];
                $this->renderPostDetail($postArray, $comments, $attachments);
                break;

            case 'create':
                if ($this->isLoggedIn()) {
                    $this->renderPostForm();
                } else {
                    $this->redirect('/?action=login');
                }
                break;

            case 'edit':
                if ($this->isLoggedIn() && $id) {
                    $post = $this->postService->getPost((int)$id);
                    if ($post && $post->canBeEditedBy($_SESSION['user_id'])) {
                        $this->renderPostForm($this->convertPostToArray($post));
                    } else {
                        $this->redirect('/');
                    }
                } else {
                    $this->redirect('/?action=login');
                }
                break;

            case 'api_posts':
                $page = (int)($_GET['page'] ?? 1);
                $limit = 6;
                $posts = $this->boardFacade->getPostsPaginated($page, $limit);
                header('Content-Type: application/json');
                echo json_encode($posts);
                exit;
                break;

            case 'download':
                if ($id) {
                    $this->handleFileDownload((int)$id);
                }
                break;

            default:
                $posts = $this->boardFacade->getPostsPaginated(1, 6);
                $this->renderPostList($posts);
                break;
        }
    }

    private function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    private function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    private function renderLogin(string $error = ''): void
    {
        include __DIR__ . '/Views/login.php';
    }

    private function renderRegister(string $error = ''): void
    {
        include __DIR__ . '/Views/register.php';
    }

    private function renderPostList(array $posts): void
    {
        include __DIR__ . '/Views/post_list.php';
    }

    private function renderPostDetail(?array $post, array $comments = [], array $attachments = []): void
    {
        include __DIR__ . '/Views/post_detail.php';
    }

    private function reorganizeFilesArray(array $files): array
    {
        $reorganized = [];
        $fileCount = count($files['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $reorganized[] = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
            }
        }
        
        return $reorganized;
    }

    private function renderPostForm(?array $post = null): void
    {
        include __DIR__ . '/Views/post_form.php';
    }

    private function handleFileDownload(int $attachmentId): void
    {
        $attachment = $this->attachmentService->getAttachment($attachmentId);
        
        if (!$attachment) {
            http_response_code(404);
            echo 'File not found';
            return;
        }
        
        $filePath = __DIR__ . '/../../storage/uploads/' . $attachment->getFilePath();
        
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo 'File not found';
            return;
        }
        
        header('Content-Type: ' . $attachment->getMimeType());
        header('Content-Disposition: attachment; filename="' . $attachment->getOriginalName() . '"');
        header('Content-Length: ' . filesize($filePath));
        
        readfile($filePath);
        exit;
    }

    private function convertPostToArray($post): array
    {
        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'author' => $post->getAuthor(),
            'user_id' => $post->getUserId(),
            'created_at' => $post->getCreatedAt()
        ];
    }

    private function convertCommentsToArray(array $comments): array
    {
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

    private function convertAttachmentsToArray(array $attachments): array
    {
        $result = [];
        foreach ($attachments as $attachment) {
            $result[] = [
                'id' => $attachment->getId(),
                'post_id' => $attachment->getPostId(),
                'original_name' => $attachment->getOriginalName(),
                'stored_name' => $attachment->getStoredName(),
                'file_path' => $attachment->getFilePath(),
                'file_size' => $attachment->getFileSize(),
                'formatted_size' => $attachment->getFormattedSize(),
                'mime_type' => $attachment->getMimeType(),
                'created_at' => $attachment->getCreatedAt()
            ];
        }
        return $result;
    }

    private function checkFileUploadErrors(array $files): ?string
    {
        foreach ($files['error'] as $index => $error) {
            if ($error !== UPLOAD_ERR_OK) {
                $fileName = $files['name'][$index];
                switch ($error) {
                    case UPLOAD_ERR_INI_SIZE:
                        return "파일 '{$fileName}'이 너무 큽니다. 최대 2MB까지 업로드 가능합니다.";
                    case UPLOAD_ERR_FORM_SIZE:
                        return "파일 '{$fileName}'이 폼에서 지정한 최대 크기를 초과했습니다.";
                    case UPLOAD_ERR_PARTIAL:
                        return "파일 '{$fileName}'이 부분적으로만 업로드되었습니다.";
                    case UPLOAD_ERR_NO_TMP_DIR:
                        return "임시 폴더가 없습니다.";
                    case UPLOAD_ERR_CANT_WRITE:
                        return "디스크에 파일을 쓸 수 없습니다.";
                    default:
                        return "파일 '{$fileName}' 업로드 중 오류가 발생했습니다.";
                }
            }
        }
        return null;
    }
}