<?php

namespace Infrastructure\Database;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=127.0.0.1;dbname=board",
                    'root',
                    'root'
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("데이터베이스 연결 실패: " . $e->getMessage());
            }
        }
        
        return self::$instance;
    }
}