<?php

use PhpCsFixer\Config;

$finder = (new PhpCsFixer\Finder())->in("/var/www/bitrix/");

return (new Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'full_opening_tag' => true,
    ])->setFinder($finder);