<?php

namespace Beste\Cache\Tests;

use Beste\Cache\InMemoryCache;
use Beste\Clock\FrozenClock;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class InMemoryCacheTest extends TestCase
{
    private FrozenClock $clock;
    private InMemoryCache $pool;

    protected function setUp(): void
    {
        $this->clock = FrozenClock::fromUTC();
        $this->pool = new InMemoryCache($this->clock);
    }

    public function testItWorksWithouProvidingAClock(): void
    {
        $pool = new InMemoryCache();
        $item = $pool->getItem('item');

        self::assertFalse($item->isHit());

        $item->set('value');
        $pool->save($item);

        $item = $pool->getItem('item');
        self::assertTrue($item->isHit());
    }

    public function testItReturnsANewItem(): void
    {
        $item = $this->pool->getItem('item');

        self::assertFalse($item->isHit());
        self::assertNull($item->get());
    }

    public function testItUsesTheProvidedClock(): void
    {
        $item = $this->pool->getItem('item');
        $item->set('value');
        $item->expiresAfter(new \DateInterval('PT2H'));
        $this->pool->save($item);

        $this->clock->setTo($this->clock->now()->add(new \DateInterval('PT1H')));
        self::assertTrue($this->pool->getItem('item')->isHit());

        $this->clock->setTo($this->clock->now()->add(new \DateInterval('PT2H')));
        self::assertFalse($this->pool->getItem('item')->isHit());
    }

    public function testItSavesAnItem(): void
    {
        $item = $this->pool->getItem('item');

        $item->set('value');
        $this->pool->save($item);

        self::assertTrue($this->pool->getItem('item')->isHit());
        self::assertSame('value', $this->pool->getItem('item')->get());
    }

    public function testItHasAnItem(): void
    {
        self::assertFalse($this->pool->hasItem('key'));

        $item = $this->pool->getItem('key');
        $item->set('value');
        $this->pool->save($item);

        self::assertTrue($this->pool->hasItem('key'));
    }

    public function testItCommitsDeferredItems(): void
    {
        $item = $this->pool->getItem('item');

        $item->set('value');

        $this->pool->saveDeferred($item);

        self::assertFalse($this->pool->getItem('item')->isHit());

        $this->pool->commit();

        self::assertTrue($this->pool->getItem('item')->isHit());
    }

    public function testItCanBeCleared(): void
    {
        $this->pool->save($this->pool->getItem('key')->set('value'));

        self::assertTrue($this->pool->getItem('key')->isHit());

        $this->pool->clear();

        self::assertFalse($this->pool->getItem('key')->isHit());
    }

    public function testItReturnsMultipleItems(): void
    {
        $this->pool->save($this->pool->getItem('first')->set('value'));
        $this->pool->save($this->pool->getItem('third')->set('value'));

        $items = $this->pool->getItems(['first', 'second', 'third']);

        self::assertCount(3, $items);
        self::assertIsArray($items);

        self::assertArrayHasKey('first', $items);
        self::assertTrue($items['first']->isHit());

        self::assertArrayHasKey('second', $items);
        self::assertFalse($items['second']->isHit());

        self::assertArrayHasKey('third', $items);
        self::assertTrue($items['third']->isHit());
    }

    public function testItReturnsNoItemsWhenNoKeysAreGiven(): void
    {
        $this->pool->save($this->pool->getItem('key')->set('value'));

        self::assertEmpty($this->pool->getItems());
    }

    public function testItDeletesAnItem(): void
    {
        $this->pool->save($this->pool->getItem('key')->set('value'));

        self::assertTrue($this->pool->hasItem('key'));

        $this->pool->deleteItem('key');

        self::assertFalse($this->pool->hasItem('key'));
    }

    public function testItDeletesMultipleItems(): void
    {
        $this->pool->save($this->pool->getItem('first')->set('value'));
        $this->pool->save($this->pool->getItem('second')->set('value'));
        $this->pool->save($this->pool->getItem('third')->set('value'));

        $this->pool->deleteItems(['first', 'third', 'fourth']);

        self::assertFalse($this->pool->hasItem('first'));
        self::assertTrue($this->pool->hasItem('second'));
        self::assertFalse($this->pool->hasItem('third'));
        self::assertFalse($this->pool->hasItem('fourth'));
    }
}
