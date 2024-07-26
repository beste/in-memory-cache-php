<?php

namespace Beste\Cache\Tests;

use Beste\Cache\CacheKey;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;

class CacheKeyTest extends TestCase
{
    #[DataProvider('validValues')]
    #[DoesNotPerformAssertions]
    public function testItAcceptsValidValues(string $value): void
    {
        CacheKey::fromString($value);
    }

    #[DataProvider('invalidValues')]
    public function testItRejectsInvalidValues(string $value): void
    {
        self::expectException(InvalidArgumentException::class);
        CacheKey::fromString($value);
    }

    /**
     * @return array<non-empty-string, list<non-empty-string>>
     */
    public static function validValues(): array
    {
        return [
            'single char' => ['x'],
            '64 chars' => [str_repeat('x', 64)],
            'all allowed chars' => ['aZ0_.-'],
        ];
    }

    /**
     * @return array<non-empty-string, list<string>>
     */
    public static function invalidValues(): array
    {
        return [
            'empty string' => [''],
            'invalid character' => ['\\'],
        ];
    }
}
