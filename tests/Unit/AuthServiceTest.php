<?php

use PHPUnit\Framework\TestCase;
use Domain\Service\AuthService;
use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;

class AuthServiceTest extends TestCase
{
    private AuthService $authService;
    private UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->authService = new AuthService($this->userRepository);
    }

    public function testLoginWithValidCredentials(): void
    {
        $user = new User(1, 'testuser', password_hash('password', PASSWORD_DEFAULT));
        
        $this->userRepository
            ->expects($this->once())
            ->method('findByUsername')
            ->with('testuser')
            ->willReturn($user);

        $result = $this->authService->login('testuser', 'password');
        
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('testuser', $result->getUsername());
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $user = new User(1, 'testuser', password_hash('password', PASSWORD_DEFAULT));
        
        $this->userRepository
            ->expects($this->once())
            ->method('findByUsername')
            ->with('testuser')
            ->willReturn($user);

        $result = $this->authService->login('testuser', 'wrongpassword');
        
        $this->assertNull($result);
    }

    public function testLoginWithNonExistentUser(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('findByUsername')
            ->with('nonexistent')
            ->willReturn(null);

        $result = $this->authService->login('nonexistent', 'password');
        
        $this->assertNull($result);
    }

    public function testRegisterWithNewUser(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('existsByUsername')
            ->with('newuser')
            ->willReturn(false);

        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->with('newuser', 'password')
            ->willReturn(new User(1, 'newuser', password_hash('password', PASSWORD_DEFAULT)));

        $result = $this->authService->register('newuser', 'password');
        
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('newuser', $result->getUsername());
    }

    public function testRegisterWithExistingUser(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('existsByUsername')
            ->with('existinguser')
            ->willReturn(true);

        $result = $this->authService->register('existinguser', 'password');
        
        $this->assertNull($result);
    }
}