<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ErrorMetadata;

use App\Infrastructure\Http\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;

/**
 * @internal
 */
#[CoversClass(ReflectionErrorMetadataFactory::class)]
#[Group('unit')]
final class ReflectionErrorMetadataFactoryTest extends TestCase
{
    public function testReturnsErrorForMappedClass(): void
    {
        $factory = new ReflectionErrorMetadataFactory();

        $error = $factory->get(ErrorStub::class);

        assertEquals(new Error('ed17d972-abf5-4162-a112-8dc8ee8bd504', 'Ошибка', Status::BAD_REQUEST), $error);
    }

    public function testReturnsNullForNonMappedClass(): void
    {
        $factory = new ReflectionErrorMetadataFactory();

        $error = $factory->get(self::class);

        assertNull($error);
    }
}
