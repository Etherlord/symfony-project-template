<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Infrastructure\DependencyInjection\Module;
use App\Infrastructure\Doctrine\Console\SchemaValidateCommand;
use App\Infrastructure\Doctrine\SchemaConfigurator\ConfigurableSchemaProvider;
use App\Infrastructure\Doctrine\SchemaConfigurator\SchemaSubscriber;
use App\Infrastructure\Doctrine\SchemaConfigurator\SchemaSubscriberChain;
use Doctrine\Migrations\Provider\SchemaProvider;
use Doctrine\ORM\Tools\ToolEvents;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function App\Infrastructure\DependencyInjection\inlineService;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $di, ContainerBuilder $builder): void {
    $di->extension('doctrine_migrations', [
        'services' => [
            SchemaProvider::class => ConfigurableSchemaProvider::class,
        ],
    ]);

    $builder->registerForAutoconfiguration(SchemaSubscriber::class)->addTag(SchemaSubscriber::class);

    $module = Module::create($di);
    $module
        ->set(SchemaProvider::class)->factory([service('doctrine.migrations.dependency_factory'), 'getSchemaProvider'])
        ->set(ConfigurableSchemaProvider::class)->args([
            '$subscriber' => inlineService(SchemaSubscriberChain::class, [
                tagged_iterator(SchemaSubscriber::class),
            ]),
        ])
        ->set(SchemaValidateCommand::class)->args([
            '$configuration' => service('doctrine.migrations.configuration'),
        ])
    ;

    if ($module->isDev()) {
        $module->set(FixPostgresDefaultSchemaListener::class)->tag('doctrine.event_listener', ['event' => ToolEvents::postGenerateSchema]);
    }
};
