<?php

namespace Beste\Cache;

final class InvalidArgument extends \InvalidArgumentException implements \Psr\Cache\InvalidArgumentException
{
    public static function invalidKey(): self
    {
        return new self('The given key is not valid');
    }
}
