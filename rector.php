<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php83\Rector\ClassConst\AddTypeToConstRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->parallel();
    $rectorConfig->cacheDirectory(__DIR__ . '/var/rector');
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/src-dev',
        __DIR__ . '/tests',
    ]);
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_83,
        PHPUnitSetList::PHPUNIT_100,
    ]);
    $rectorConfig->skip([
        AddTypeToConstRector::class,
        StringClassNameToClassConstantRector::class => [
            __DIR__ . '/tests/Infrastructure/Uuid/UuidFunctionTest.php',
        ],
    ]);
};
