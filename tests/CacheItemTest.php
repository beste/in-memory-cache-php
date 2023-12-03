<?php

namespace Beste\Cache\Tests;

use Beste\Cache\CacheItem;
use Beste\Cache\CacheKey;
use Beste\Clock\FrozenClock;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class CacheItemTest extends TestCase
{
    private string $key;
    private FrozenClock $clock;
    private CacheItem $cacheItem;

    protected function setUp(): void
    {
        $this->key = 'key';
        $this->clock = FrozenClock::fromUTC();
        $this->cacheItem = new CacheItem(CacheKey::fromString($this->key), $this->clock);
    }

    public function testItHasAKey(): void
    {
        self::assertSame($this->key, $this->cacheItem->getKey());
    }

    public function testItInitiallyHasNoValue(): void
    {
        self::assertNull($this->cacheItem->get());
    }

    public function testItInitiallyIsNotAHit(): void
    {
        self::assertFalse($this->cacheItem->isHit());
    }

    public function testItHasAValueWhenSetWithOne(): void
    {
        $this->cacheItem->set('value');

        self::assertSame('value', $this->cacheItem->get());
    }

    public function testItBecomesHitWhenSetWithAValue(): void
    {
        $this->cacheItem->set('value');

        self::assertTrue($this->cacheItem->isHit());
    }

    public function testItHasAValueAsLongAsItIsNotExpiredAtAGivenTime(): void
    {
        $this->cacheItem->set('value');
        $this->cacheItem->expiresAt($this->clock->now()->modify('+1 minute'));

        self::assertSame('value', $this->cacheItem->get());
    }

    public function testItHasNoValueWhenItIsExpiredAtAGivenTime(): void
    {
        $this->cacheItem->set('value');
        $this->cacheItem->expiresAt($this->clock->now()->modify('-1 minute'));

        self::assertNull($this->cacheItem->get());
    }

    public function testItIsAHitAsLongAsItIsNotExpiredAtAGivenTime(): void
    {
        $this->cacheItem->set('value');
        $this->cacheItem->expiresAt($this->clock->now()->modify('+1 minute'));

        self::assertTrue($this->cacheItem->isHit());
    }

    public function testItIsAMissWhenItIsExpiredAtAGivenTime(): void
    {
        $this->cacheItem->set('value');
        $this->cacheItem->expiresAt($this->clock->now()->modify('-1 minute'));

        self::assertFalse($this->cacheItem->isHit());
    }

    public function testTheExpirationCanBeGivenInSeconds(): void
    {
        $this->cacheItem->set('value');
        assert($this->cacheItem->isHit() === true);

        $this->cacheItem->expiresAfter(60);
        $this->clock->setTo($this->clock->now()->modify('+61 seconds'));

        self::assertFalse($this->cacheItem->isHit());
    }

    public function testTheExpirationCanBeGivenAsADateInterval(): void
    {
        $this->cacheItem->set('value');
        assert($this->cacheItem->isHit() === true);

        $this->cacheItem->expiresAfter(new \DateInterval('PT60S'));
        $this->clock->setTo($this->clock->now()->modify('+61 seconds'));

        self::assertFalse($this->cacheItem->isHit());
    }

    public function testTheExpirationCanBeUnset(): void
    {
        $this->cacheItem->set('value');
        $this->cacheItem->expiresAfter(60);
        $this->clock->setTo($this->clock->now()->modify('+61 seconds'));
        assert($this->cacheItem->isHit() === false);

        $this->cacheItem->expiresAfter(null);

        self::assertTrue($this->cacheItem->isHit());


    }
}
