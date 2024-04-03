<?php

declare(strict_types=1);

namespace App\Infrastructure\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator;

/**
 * @param ?class-string $class
 */
function inlineService(?string $class = null, array $args = []): InlineServiceConfigurator
{
    return (new InlineServiceConfigurator(new Definition($class)))
        ->args($args)
        ->autowire()
    ;
}
