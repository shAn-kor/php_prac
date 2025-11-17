<?php

namespace Domain\Repository;

use Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findByUsername(string $username): ?User;
    public function create(string $username, string $password): User;
}