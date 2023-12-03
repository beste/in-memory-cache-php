# PSR-6 In-Memory Cache

A [PSR-6](https://www.php-fig.org/psr/psr-6/) In-Memory cache that can be used as a default implementation and in tests.

[![Current version](https://img.shields.io/packagist/v/beste/in-memory-cache-php.svg?logo=composer)](https://packagist.org/packages/beste/in-memory-cache-php)
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/beste/in-memory-cache-php)](https://packagist.org/packages/beste/in-memory-cache-php)
[![Monthly Downloads](https://img.shields.io/packagist/dm/beste/in-memory-cache-php.svg)](https://packagist.org/packages/beste/in-memory-cache-php/stats)
[![Total Downloads](https://img.shields.io/packagist/dt/beste/in-memory-cache-php.svg)](https://packagist.org/packages/beste/in-memory-cache-php/stats)
[![Tests](https://github.com/beste/in-memory-cache-php/actions/workflows/tests.yml/badge.svg)](https://github.com/beste/in-memory-cache-php/actions/workflows/tests.yml)

## Installation

In order to use this cache implementation, you also need to install a [PSR-20](https://www.php-fig.org/psr/psr-20/) [Clock Implementation](https://packagist.org/providers/psr/clock-implementation),
for example, the [`beste/clock`](https://packagist.org/packages/beste/clock).

```shell
composer require beste/in-memory-cache beste/clock
```

## Usage

```php
use Beste\Cache\InMemoryCache;
use Beste\Clock\SystemClock;

$clock = SystemClock::create();
$cache = new InMemoryCache($clock);

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

The test suite

## Running tests

```shell
composer test
```

## License

This project is published under the [MIT License](LICENSE).
