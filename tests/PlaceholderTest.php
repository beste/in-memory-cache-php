<?php

declare(strict_types=1);

namespace Beste\Psr\Cache\Tests;

use Beste\Psr\Cache\Placeholder;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Beste\Psr\Cache\Placeholder
 */
final class PlaceholderTest extends TestCase
{
    private Placeholder $placeholder;

    protected function setUp(): void
    {
        $this->placeholder = new Placeholder('Jérôme Gamez says: ');
    }

    /**
     * @test
     */
    public function it_echoes_a_value(): void
    {
        self::assertSame('Jérôme Gamez says: Hello', $this->placeholder->echo('Hello'));
    }
}
