<?php

declare(strict_types=1);

namespace App\Infrastructure\Jwt;

use App\Infrastructure\DependencyInjection\Module;
use App\Infrastructure\Jwt\LexikJWTIntegration\JwtEncoder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    Module::create($di)->set(JwtEncoder::class);
};
