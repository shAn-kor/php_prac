<?php

namespace Infrastructure\Session;

class RedisSessionHandler implements \SessionHandlerInterface
{
    private $redis;
    private $ttl;

    public function __construct(string $host = 'redis', int $port = 6379, int $ttl = 3600)
    {
        $this->redis = new \Redis();
        $this->redis->connect($host, $port);
        $this->ttl = $ttl;
    }

    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return $this->redis->close();
    }

    public function read($sessionId): string
    {
        $data = $this->redis->get("session:$sessionId");
        return $data ?: '';
    }

    public function write($sessionId, $sessionData): bool
    {
        return $this->redis->setex("session:$sessionId", $this->ttl, $sessionData);
    }

    public function destroy($sessionId): bool
    {
        return $this->redis->del("session:$sessionId") > 0;
    }

    public function gc($maxlifetime): int
    {
        return 0;
    }
}