<?php

namespace Beste\Cache;

/**
 * @internal
 */
final class CacheKey
{
    private function __construct(private readonly string $value) {}

    public static function fromString(string $value): self
    {
        if (preg_match('/^[a-zA-Z0-9_.-]+$/u', $value) !== 1) {
            throw InvalidArgument::invalidKey();
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
