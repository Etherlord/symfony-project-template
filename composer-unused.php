<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;

return static fn(Configuration $config): Configuration => $config
    ->addNamedFilter(NamedFilter::fromString('ext-ctype'))
    ->addNamedFilter(NamedFilter::fromString('ext-iconv'))
    ->addNamedFilter(NamedFilter::fromString('ext-intl'))
    ->addNamedFilter(NamedFilter::fromString('ext-simplexml'))
    ->addNamedFilter(NamedFilter::fromString('ext-sockets'))
    ->addNamedFilter(NamedFilter::fromString('doctrine/doctrine-migrations-bundle'))
    ->addNamedFilter(NamedFilter::fromString('phpstan/phpdoc-parser'))
    ->addNamedFilter(NamedFilter::fromString('runtime/roadrunner-symfony-nyholm'))
    ->addNamedFilter(NamedFilter::fromString('symfony/dotenv'))
    ->addNamedFilter(NamedFilter::fromString('symfony/flex'))
    ->addNamedFilter(NamedFilter::fromString('symfony/lock'))
    ->addNamedFilter(NamedFilter::fromString('symfony/runtime'))
;
