<?php

use PHPUnit\Framework\TestCase;

class UserLoginTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function testLoginPageAccess(): void
    {
        $_GET['action'] = 'login';
        
        ob_start();
        include __DIR__ . '/../../public/index.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('로그인', $output);
        $this->assertStringContainsString('아이디', $output);
        $this->assertStringContainsString('비밀번호', $output);
    }

    public function testRegisterPageAccess(): void
    {
        $_GET['action'] = 'register';
        
        ob_start();
        include __DIR__ . '/../../public/index.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('회원가입', $output);
        $this->assertStringContainsString('아이디', $output);
        $this->assertStringContainsString('비밀번호', $output);
    }

    public function testCSRFTokenGeneration(): void
    {
        $_GET['action'] = 'login';
        
        ob_start();
        include __DIR__ . '/../../public/index.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('csrf_token', $output);
        $this->assertArrayHasKey('csrf_token', $_SESSION);
    }

    public function testSessionSecurity(): void
    {
        // 세션 보안 설정 테스트
        $this->assertTrue(session_status() === PHP_SESSION_ACTIVE);
        
        // CSRF 토큰이 세션에 저장되는지 확인
        \Infrastructure\Security\SecurityHelper::generateCSRFToken();
        $this->assertArrayHasKey('csrf_token', $_SESSION);
        $this->assertEquals(64, strlen($_SESSION['csrf_token']));
    }

    protected function tearDown(): void
    {
        // 테스트 후 세션 정리
        $_SESSION = [];
        $_GET = [];
        $_POST = [];
    }
}