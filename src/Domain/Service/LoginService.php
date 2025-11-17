<?php

namespace Domain\Service;

use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;

class LoginService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $username, string $password): ?User
    {
        $user = $this->userRepository->findByUsername($username);
        
        if ($user && $user->verifyPassword($password)) {
            return $user;
        }
        
        return null;
    }

    public function register(string $username, string $password): ?User
    {
        if ($this->userRepository->findByUsername($username)) {
            return null;
        }
        
        return $this->userRepository->create($username, $password);
    }
}