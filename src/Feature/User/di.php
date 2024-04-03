<?php

declare(strict_types=1);

namespace App\Feature\User;

use App\Feature\User\Repository\PostgresUserRepository;
use App\Feature\User\Repository\UserRepository;
use App\Infrastructure\DependencyInjection\Module;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    Module::create($di)
        ->messageHandlers()
        ->set(PostgresUserRepository::class)
        ->alias(UserRepository::class, PostgresUserRepository::class)
    ;
};
