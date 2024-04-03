<?php

declare(strict_types=1);

namespace App\Infrastructure\PostgresThesis;

use App\Infrastructure\DependencyInjection\Module;
use App\Infrastructure\PostgresThesis\DependencyInjection\AddValueResolverCompilerPass;
use App\Infrastructure\PostgresThesis\ValinorIntegration\ValinorHydrator;
use App\Infrastructure\PostgresThesis\ValueResolver\UuidResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Thesis\Result\Hydrator;
use Thesis\StatementContext\ValueResolver;
use Thesis\StatementContext\ValueResolverRegistry;
use Thesis\StatementContext\ValueResolverRegistry\ContainerValueResolverRegistry;

return static function (ContainerConfigurator $di, ContainerBuilder $containerBuilder): void {
    $containerBuilder->addCompilerPass(new AddValueResolverCompilerPass());
    $containerBuilder->registerForAutoconfiguration(ValueResolver::class)->addTag(ValueResolver::class);

    Module::create($di)
        ->set(PostgresConnection::class)->args([
            '$host' => '%env(DB_HOST)%',
            '$port' => '%env(DB_PORT)%',
            '$user' => '%env(DB_USER)%',
            '$password' => '%env(DB_PASSWORD)%',
            '$dbName' => '%env(DB_NAME)%',
        ])
        ->set(ValinorHydrator::class)
        ->alias(Hydrator::class, ValinorHydrator::class)
        ->set(ContainerValueResolverRegistry::class)
        ->alias(ValueResolverRegistry::class, ContainerValueResolverRegistry::class)
        ->set(UuidResolver::class)
    ;
};
