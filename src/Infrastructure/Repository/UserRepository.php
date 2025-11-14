<?php

namespace Infrastructure\Repository;

use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;
use Infrastructure\Database\DatabaseConnection;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance();
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        return new User($data['id'], $data['username'], $data['password']);
    }

    public function create(string $username, string $password): User
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);
        
        $id = $this->pdo->lastInsertId();
        return new User($id, $username, $hashedPassword);
    }

    public function existsByUsername(string $username): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }
}