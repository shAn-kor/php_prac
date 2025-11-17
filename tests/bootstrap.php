<?php

require_once __DIR__ . '/../autoload.php';

// 테스트용 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}