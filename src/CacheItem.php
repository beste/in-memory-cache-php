<?php

namespace Beste\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Clock\ClockInterface;

/**
 * @internal
 */
final class CacheItem implements CacheItemInterface
{
    private mixed $value;
    private ?\DateTimeInterface $expiresAt;
    private bool $isHit;

    public function __construct(private readonly CacheKey $key, private readonly ClockInterface $clock)
    {
        $this->value = null;
        $this->expiresAt = null;
        $this->isHit = false;
    }

    public function getKey(): string
    {
        return $this->key->toString();
    }

    public function get(): mixed
    {
        if ($this->isHit()) {
            return $this->value;
        }

        return null;
    }

    public function isHit(): bool
    {
        if ($this->isHit === false) {
            return false;
        }

        if ($this->expiresAt === null) {
            return true;
        }

        return $this->clock->now()->getTimestamp() < $this->expiresAt->getTimestamp();
    }

    public function set(mixed $value): static
    {
        $this->isHit = true;
        $this->value = $value;

        return $this;
    }

    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        $this->expiresAt = $expiration;

        return $this;
    }

    public function expiresAfter(\DateInterval|int|null $time): static
    {
        if ($time === null) {
            $this->expiresAt = null;
            return $this;
        }

        if (is_int($time)) {
            $time = new \DateInterval("PT{$time}S");
        }

        $this->expiresAt = $this->clock->now()->add($time);

        return $this;
    }
}
