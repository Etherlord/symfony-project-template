<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Infrastructure\DependencyInjection\Module;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    Module::create($di)->load('**/*Command.php');
};
