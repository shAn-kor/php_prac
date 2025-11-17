<?php

namespace Presentation;

use Application\BoardFacade;
use Infrastructure\Security\SecurityHelper;

class BoardController
{
    private BoardFacade $boardFacade;

    public function __construct()
    {
        $this->boardFacade = new BoardFacade();
    }

    public function handleRequest(): void
    {
        session_start();
        
        $action = $_GET['action'] ?? 'list';
        $id = $_GET['id'] ?? null;

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
                $user = $this->boardFacade->login($_POST['username'], $_POST['password']);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $this->redirect('/');
                } else {
                    $this->renderLogin("아이디 또는 비밀번호가 잘못되었습니다.");
                }
                break;

            case 'register':
                $user = $this->boardFacade->register($_POST['username'], $_POST['password']);
                if ($user) {
                    $this->redirect('/?action=login');
                } else {
                    $this->renderRegister("이미 존재하는 아이디입니다.");
                }
                break;

            case 'store':
                if ($this->isLoggedIn()) {
                    $post = $this->boardFacade->createPost(
                        SecurityHelper::sanitizeInput($_POST['title']),
                        SecurityHelper::preventXSS($_POST['content']),
                        $_SESSION['username'],
                        $_SESSION['user_id']
                    );
                    
                    // 파일 업로드 처리
                    if (!empty($_FILES['attachments']['name'][0])) {
                        $files = $this->reorganizeFilesArray($_FILES['attachments']);
                        $this->boardFacade->uploadFiles($post['id'], $files);
                    }
                }
                $this->redirect('/');
                break;

            case 'update':
                if ($this->isLoggedIn() && $id) {
                    $this->boardFacade->updatePost(
                        (int)$id,
                        $_POST['title'],
                        $_POST['content'],
                        $_SESSION['user_id']
                    );
                    $this->redirect("/?action=show&id=$id");
                }
                break;

            case 'delete':
                if ($this->isLoggedIn() && $id) {
                    $this->boardFacade->deletePost((int)$id, $_SESSION['user_id']);
                }
                $this->redirect('/');
                break;

            case 'comment_store':
                if ($this->isLoggedIn() && $id) {
                    $this->boardFacade->createComment(
                        (int)$id,
                        SecurityHelper::preventXSS($_POST['content']),
                        $_SESSION['username'],
                        $_SESSION['user_id']
                    );
                    $this->redirect("/?action=show&id=$id");
                }
                break;

            case 'comment_update':
                if ($this->isLoggedIn() && $id) {
                    $this->boardFacade->updateComment(
                        (int)$id,
                        $_POST['content'],
                        $_SESSION['user_id']
                    );
                    $postId = $_POST['post_id'];
                    $this->redirect("/?action=show&id=$postId");
                }
                break;

            case 'comment_delete':
                if ($this->isLoggedIn() && $id) {
                    $postId = $_POST['post_id'];
                    $this->boardFacade->deleteComment((int)$id, $_SESSION['user_id']);
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
                $post = $this->boardFacade->getPost((int)$id);
                $comments = $post ? $this->boardFacade->getCommentsByPostId((int)$id) : [];
                $attachments = $post ? $this->boardFacade->getAttachmentsByPostId((int)$id) : [];
                $this->renderPostDetail($post, $comments, $attachments);
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
                    $post = $this->boardFacade->getPost((int)$id);
                    if ($post && $this->boardFacade->canEditPost((int)$id, $_SESSION['user_id'])) {
                        $this->renderPostForm($post);
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
}