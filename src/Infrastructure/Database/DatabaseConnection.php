<?php

namespace Infrastructure\Database;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        // 각 요청마다 새로운 연결 생성하여 결과셋 혼재 방지
        try {
            $pdo = new PDO(
                "mysql:host=127.0.0.1;dbname=board",
                'root',
                'root',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            die("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }
}