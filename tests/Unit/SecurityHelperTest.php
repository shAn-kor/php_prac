<?php

use PHPUnit\Framework\TestCase;
use Infrastructure\Security\SecurityHelper;

class SecurityHelperTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function testGenerateCSRFToken(): void
    {
        $token = SecurityHelper::generateCSRFToken();
        
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes = 64 hex chars
        $this->assertEquals($token, $_SESSION['csrf_token']);
    }

    public function testValidateCSRFTokenValid(): void
    {
        $token = SecurityHelper::generateCSRFToken();
        
        $result = SecurityHelper::validateCSRFToken($token);
        
        $this->assertTrue($result);
    }

    public function testValidateCSRFTokenInvalid(): void
    {
        SecurityHelper::generateCSRFToken();
        
        $result = SecurityHelper::validateCSRFToken('invalid_token');
        
        $this->assertFalse($result);
    }

    public function testSanitizeInput(): void
    {
        $input = '  <script>alert("xss")</script>  ';
        $expected = '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;';
        
        $result = SecurityHelper::sanitizeInput($input);
        
        $this->assertEquals($expected, $result);
    }

    public function testSanitizeOutput(): void
    {
        $output = '<script>alert("xss")</script>';
        $expected = '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;';
        
        $result = SecurityHelper::sanitizeOutput($output);
        
        $this->assertEquals($expected, $result);
    }

    public function testPreventXSS(): void
    {
        $content = '<p>Safe content</p><script>alert("xss")</script><strong>Bold text</strong>';
        $expected = '<p>Safe content</p>alert("xss")<strong>Bold text</strong>';
        
        $result = SecurityHelper::preventXSS($content);
        
        $this->assertEquals($expected, $result);
        $this->assertStringNotContainsString('<script>', $result);
    }

    public function testPreventXSSAllowedTags(): void
    {
        $content = '<p>Paragraph</p><h1>Header</h1><strong>Bold</strong><em>Italic</em>';
        
        $result = SecurityHelper::preventXSS($content);
        
        $this->assertEquals($content, $result);
    }
}