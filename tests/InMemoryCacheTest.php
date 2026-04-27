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

    public function testItWorksWithoutProvidingAClock(): void
    {
        $pool = new InMemoryCache();
        $item = $pool->getItem('item');

        $this->assertFalse($item->isHit());

        $item->set('value')->expiresAfter(new \DateInterval('PT5M'));
        $pool->save($item);

        $item = $pool->getItem('item');
        $this->assertTrue($item->isHit());
    }

    public function testItReturnsANewItem(): void
    {
        $item = $this->pool->getItem('item');

        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
    }

    public function testItUsesTheProvidedClock(): void
    {
        $item = $this->pool->getItem('item');
        $item->set('value');
        $item->expiresAfter(new \DateInterval('PT2H'));
        $this->pool->save($item);

        $this->clock->setTo($this->clock->now()->add(new \DateInterval('PT1H')));
        $this->assertTrue($this->pool->getItem('item')->isHit());

        $this->clock->setTo($this->clock->now()->add(new \DateInterval('PT2H')));
        $this->assertFalse($this->pool->getItem('item')->isHit());
    }

    public function testItSavesAnItem(): void
    {
        $item = $this->pool->getItem('item');

        $item->set('value');
        $this->pool->save($item);

        $this->assertTrue($this->pool->getItem('item')->isHit());
        $this->assertSame('value', $this->pool->getItem('item')->get());
    }

    public function testItHasAnItem(): void
    {
        $this->assertFalse($this->pool->hasItem('key'));

        $item = $this->pool->getItem('key');
        $item->set('value');
        $this->pool->save($item);

        $this->assertTrue($this->pool->hasItem('key'));
    }

    public function testItCommitsDeferredItems(): void
    {
        $item = $this->pool->getItem('item');

        $item->set('value');

        $this->pool->saveDeferred($item);

        $this->assertFalse($this->pool->getItem('item')->isHit());

        $this->pool->commit();

        $this->assertTrue($this->pool->getItem('item')->isHit());
    }

    public function testItCanBeCleared(): void
    {
        $this->pool->save($this->pool->getItem('key')->set('value'));

        $this->assertTrue($this->pool->getItem('key')->isHit());

        $this->pool->clear();

        $this->assertFalse($this->pool->getItem('key')->isHit());
    }

    public function testItReturnsMultipleItems(): void
    {
        $this->pool->save($this->pool->getItem('first')->set('value'));
        $this->pool->save($this->pool->getItem('third')->set('value'));

        $items = $this->pool->getItems(['first', 'second', 'third']);

        $this->assertCount(3, $items);
        $this->assertIsArray($items);

        $this->assertArrayHasKey('first', $items);
        $this->assertTrue($items['first']->isHit());

        $this->assertArrayHasKey('second', $items);
        $this->assertFalse($items['second']->isHit());

        $this->assertArrayHasKey('third', $items);
        $this->assertTrue($items['third']->isHit());
    }

    public function testItReturnsNoItemsWhenNoKeysAreGiven(): void
    {
        $this->pool->save($this->pool->getItem('key')->set('value'));

        $this->assertEmpty($this->pool->getItems());
    }

    public function testItDeletesAnItem(): void
    {
        $this->pool->save($this->pool->getItem('key')->set('value'));

        $this->assertTrue($this->pool->hasItem('key'));

        $this->pool->deleteItem('key');

        $this->assertFalse($this->pool->hasItem('key'));
    }

    public function testItDeletesMultipleItems(): void
    {
        $this->pool->save($this->pool->getItem('first')->set('value'));
        $this->pool->save($this->pool->getItem('second')->set('value'));
        $this->pool->save($this->pool->getItem('third')->set('value'));

        $this->pool->deleteItems(['first', 'third', 'fourth']);

        $this->assertFalse($this->pool->hasItem('first'));
        $this->assertTrue($this->pool->hasItem('second'));
        $this->assertFalse($this->pool->hasItem('third'));
        $this->assertFalse($this->pool->hasItem('fourth'));
    }
}
