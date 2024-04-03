<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use App\Infrastructure\DependencyInjection\Module;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    Module::create($di)->set(UuidGenerator::class);
};
