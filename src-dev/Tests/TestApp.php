<?php

declare(strict_types=1);

namespace App\Tests;

use App\Infrastructure\Kernel;
use App\Infrastructure\PostgresThesis\PostgresConnection;
use App\Infrastructure\PostgresThesis\ValinorIntegration\ValinorHydrator;
use App\Infrastructure\PostgresThesis\ValueResolver\UuidResolver;
use App\Infrastructure\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Thesis\StatementContext\ValueResolverRegistry\InMemoryValueResolverRegistry;

final class TestApp
{
    private static ?Kernel $kernel = null;

    private static ?ContainerInterface $container = null;

    public static function getDbConnection(): PostgresConnection
    {
        return new PostgresConnection(
            host: $_ENV['DB_HOST'] ?? '',
            port: (int) ($_ENV['DB_PORT'] ?? 5_432),
            user: $_ENV['DB_USER'] ?? '',
            password: $_ENV['DB_PASSWORD'] ?? '',
            dbName: $_ENV['DB_NAME_TEST'] ?? '',
            valueResolverRegistry: new InMemoryValueResolverRegistry([
                Uuid::class => new UuidResolver(),
            ]),
            hydrator: new ValinorHydrator(),
        );
    }

    /**
     * @throws \Throwable
     */
    public static function handleRequest(Request $request): Response
    {
        $kernel = self::kernel();
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        return $response;
    }

    /**
     * @template TService of object
     * @template TServiceId of class-string<TService>|string
     * @param TServiceId $serviceId
     * @return (TServiceId is class-string<TService> ? TService : object)
     */
    public static function service(string $serviceId): object
    {
        return self::container()->get($serviceId);
    }

    private static function container(): ContainerInterface
    {
        return self::$container ??= self::kernel()->getContainer()->get('test.service_container');
    }

    private static function kernel(): Kernel
    {
        if (self::$kernel !== null) {
            return self::$kernel;
        }

        self::$kernel = new Kernel('test', true);
        self::$kernel->boot();

        return self::$kernel;
    }
}
