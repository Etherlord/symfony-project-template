<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Infrastructure\DependencyInjection\Module;
use App\Infrastructure\Http\ErrorMetadata\CachingErrorMetadataFactory;
use App\Infrastructure\Http\ErrorMetadata\ErrorMetadataFactory;
use App\Infrastructure\Http\ErrorMetadata\ReflectionErrorMetadataFactory;
use App\Infrastructure\Http\Symfony\ConvertToJsonResponseControllerResultListener;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $di): void {
    $module = Module::create($di);

    $module->set(ErrorMetadataFactory::class, ReflectionErrorMetadataFactory::class);

    if ($module->isProd()) {
        $module
            ->set(CachingErrorMetadataFactory::class)
            ->decorate(ErrorMetadataFactory::class)
        ;
    }

    $module
        ->set(ConvertToJsonResponseControllerResultListener::class)
        ->tag('kernel.event_subscriber')
    ;
};
