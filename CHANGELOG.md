# CHANGELOG

## Unreleased

## 1.4.0 - 2025-09-30

* Added support for PHP 8.5

## 1.3.1 - 2024-08-26

* Made clock argument of `InMemoryCache` constructor explicitly nullable
  ([#4](https://github.com/beste/in-memory-cache-php/pull/4))

## 1.3.0 - 2024-08-17

* Added support for PHP 8.4

## 1.2.0 - 2024-07-27

* The [PSR-6 definition on what makes a valid cache key](https://www.php-fig.org/psr/psr-6/#definitions), it is said that
  keys must support keys consisting of the characters `A-Z`, `a-z`, `0-9`, `_`, and `.` in any order in UTF-8
  encoding and a length of up to 64 characters. Implementing libraries MAY support additional characters and encodings
  or longer lengths, but must support at least that minimum.
  * Dashes (`-`) are now allowed in cache keys.
  * The arbitrary maximum key length of 64 characters has been removed.


## 1.1.0 - 2024-03-02

* The Cache can now be instantiated without providing a [PSR-20](https://www.php-fig.org/psr/psr-20/) clock implementation.
* The library doesn't depend on the [`beste/clock` library](https://github.com/beste/clock) anymore.

## 1.0.0 - 2023-12-09

Initial Release
