<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertSame;

#[CoversClass(Uuid::class)]
#[Group('unit')]
final class UuidTest extends TestCase
{
    public function testIncorrectUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a valid UUID, got 123.');

        Uuid::fromString('123');
    }

    public function testSerialize(): void
    {
        $uuid = uuid('dccc4f57-5c32-4667-8482-69c321a17de8');

        $serialized = serialize($uuid);

        assertSame(
            'O:28:"App\Infrastructure\Uuid\Uuid":1:{i:0;s:36:"dccc4f57-5c32-4667-8482-69c321a17de8";}',
            $serialized,
        );
    }

    public function testUnserializedFromSerialized(): void
    {
        $serialized = 'O:28:"App\Infrastructure\Uuid\Uuid":1:{i:0;s:36:"dccc4f57-5c32-4667-8482-69c321a17de8";}';

        $unserialized = unserialize($serialized);

        assertEquals(uuid('dccc4f57-5c32-4667-8482-69c321a17de8'), $unserialized);
    }
}
