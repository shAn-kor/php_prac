<?php

namespace Infrastructure\Security;

class SecurityHelper
{
    public static function generateCSRFToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeOutput(string $output): string
    {
        return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    }

    public static function preventXSS(string $content): string
    {
        $allowedTags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote>';
        return strip_tags($content, $allowedTags);
    }
}