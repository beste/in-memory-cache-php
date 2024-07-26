<?php

declare(strict_types=1);

use Beste\PhpCsFixer\Config;

$config = Config\Factory::fromRuleSet(new Config\RuleSet\Php81());

$config->getFinder()->in(__DIR__);

return $config;
