<?php

namespace Beste\Cache;

use DateTimeImmutable;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Clock\ClockInterface;

final class InMemoryCache implements CacheItemPoolInterface
{
    private readonly ClockInterface $clock;

    /** @var array<string, CacheItemInterface> */
    private array $items;
    /** @var array<string, CacheItemInterface> */
    private array $deferredItems;

    public function __construct(
        ?ClockInterface $clock = null,
    ) {
        $this->clock = $clock ?? new class implements ClockInterface {
            public function now(): DateTimeImmutable
            {
                return new DateTimeImmutable();
            }

        };
        $this->items = [];
        $this->deferredItems = [];
    }

    public function getItem(string $key): CacheItemInterface
    {
        $key = CacheKey::fromString($key);

        $item = $this->items[$key->toString()] ?? null;

        if ($item === null) {
            return new CacheItem($key, $this->clock);
        }

        return clone $item;
    }

    /**
     * @return iterable<CacheItemInterface>
     */
    public function getItems(array $keys = []): iterable
    {
        if ($keys === []) {
            return [];
        }

        $items = [];

        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }

        return $items;
    }

    public function hasItem(string $key): bool
    {
        return $this->getItem($key)->isHit();
    }

    public function clear(): bool
    {
        $this->items = [];
        $this->deferredItems = [];

        return true;
    }

    public function deleteItem(string $key): bool
    {
        $key = CacheKey::fromString($key);

        unset($this->items[$key->toString()]);

        return true;
    }

    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }

        return true;
    }

    public function save(CacheItemInterface $item): bool
    {
        $this->items[$item->getKey()] = $item;

        return true;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferredItems[$item->getKey()] = $item;

        return true;
    }

    public function commit(): bool
    {
        foreach ($this->deferredItems as $item) {
            $this->save($item);
        }

        $this->deferredItems = [];

        return true;
    }
}
