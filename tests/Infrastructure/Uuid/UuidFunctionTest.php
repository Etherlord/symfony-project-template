<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertSame;

#[CoversFunction('\App\Infrastructure\Uuid\uuid')]
#[Group('unit')]
final class UuidFunctionTest extends TestCase
{
    public function testUuid(): void
    {
        $uuidString = '100de486-6d9e-4d74-bd79-96f94a5633a5';

        $uuid = uuid($uuidString);

        assertEquals(Uuid::fromString($uuidString), $uuid);
    }

    public function testNull(): void
    {
        $uuid = uuid(null);

        assertSame(null, $uuid);
    }
}
