<?php

declare(strict_types=1);

namespace App\Infrastructure\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\Configurator\AliasConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\DefaultsConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\PrototypeConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator;

final readonly class Module
{
    private function __construct(
        private ContainerConfigurator $container,
        private DefaultsConfigurator $services,
        private string $dir,
        private string $namespace,
    ) {
    }

    public static function create(ContainerConfigurator $container): self
    {
        /** @var array{array{file: string}, array{function: string}} */
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        return new self(
            $container,
            $container
                ->services()
                ->defaults()
                ->autowire()
                ->autoconfigure(),
            \dirname($trace[0]['file']),
            substr($trace[1]['function'], 0, -10),
        );
    }

    public function set(?string $serviceId, ?string $class = null): ServiceConfigurator
    {
        return $this->services->set($serviceId, $class);
    }

    public function alias(string $serviceId, string $referencedId): AliasConfigurator
    {
        return $this->services->alias($serviceId, $referencedId);
    }

    public function load(string $subDir, string $subNamespace = ''): PrototypeConfigurator
    {
        $subDir = ltrim($subDir, '/');
        $subNamespace = trim($subNamespace, '\\');

        return $this->services->load(
            $this->namespace . '\\' . $subNamespace . ($subNamespace ? '\\' : ''),
            $this->dir . '/' . $subDir,
        );
    }

    public function messageHandlers(): self
    {
        $this->load('**/*Handler.php');

        return $this;
    }

    public function isProd(): bool
    {
        return $this->container->env() === 'prod';
    }

    public function isDev(): bool
    {
        return $this->container->env() === 'dev';
    }
}
