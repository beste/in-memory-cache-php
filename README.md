# PSR-6 In-Memory Cache

A [PSR-6](https://www.php-fig.org/psr/psr-6/) In-Memory cache that can be used as a default implementation and in tests.

[![Current version](https://img.shields.io/packagist/v/beste/in-memory-cache.svg?logo=composer)](https://packagist.org/packages/beste/in-memory-cache)
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/beste/in-memory-cache)](https://packagist.org/packages/beste/in-memory-cache)
[![Monthly Downloads](https://img.shields.io/packagist/dm/beste/in-memory-cache.svg)](https://packagist.org/packages/beste/in-memory-cache/stats)
[![Total Downloads](https://img.shields.io/packagist/dt/beste/in-memory-cache.svg)](https://packagist.org/packages/beste/in-memory-cache/stats)
[![Tests](https://github.com/beste/in-memory-cache-php/actions/workflows/tests.yml/badge.svg)](https://github.com/beste/in-memory-cache-php/actions/workflows/tests.yml)

## Installation

```shell
composer require beste/in-memory-cache
```

## Usage

```php
use Beste\Cache\InMemoryCache;

$cache = new InMemoryCache();

$item = $cache->getItem('key');

assert($item->isHit() === false);
assert($item->get() === null);

$item->set('value');
$cache->save($item);

// Later...

$item = $cache->getItem('key');

assert($item->isHit() === true);
assert($item->get() === 'value');
```

You can also provide your own [PSR-20](https://www.php-fig.org/psr/psr-20/) clock implementation, for example a frozen
clock for testing, for example from the [`beste/clock` library](https://github.com/beste/clock).

```php
use Beste\Clock\FrozenClock;
use Beste\Cache\InMemoryCache;

$clock = FrozenClock::fromUTC()
$cache = new InMemoryCache();

$item = $cache->getItem('key');
$item->set('value')->expiresAfter(new DateInterval('PT5M'));
$cache->save($item);

$clock->setTo($clock->now()->add(new DateInterval('PT2M')));
assert($cache->getItem('key')->isHit() === true);

$clock->setTo($clock->now()->add(new DateInterval('PT5M')));
assert($cache->getItem('key')->isHit() === false);
```

## Running tests

```shell
composer test
```

## License

This project is published under the [MIT License](LICENSE).
