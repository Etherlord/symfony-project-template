<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertTrue;

#[CoversFunction('\App\Infrastructure\Uuid\isUuid')]
#[Group('unit')]
final class IsUuidTest extends TestCase
{
    /**
     * @return \Generator<array-key, array{string}>
     */
    public static function validUuids(): \Generator
    {
        yield [NIL];
        yield [MAX];
        yield ['21627164-acb7-11e6-80f5-76304dec7eb7'];
        yield ['d9c04bc2-173f-2cb7-ad4e-e4ca3b2c273f'];
        yield ['7b368038-a5ca-3aa3-b0db-1177d1761c9e'];
        yield ['9f4db639-0e87-4367-9beb-d64e3f42ae18'];
        yield ['1f2b1a18-81a0-5685-bca7-f23022ed7c7b'];
        yield ['1ebb5050-b028-616a-9180-0a00ac070060'];
        yield ['0184c240-40ce-773b-aa7e-d6e99798535d'];
        yield ['bf2f2cd8-c5f6-8b1a-8c1e-d8a20e1b614c'];
    }

    #[DataProvider('validUuids')]
    public function testValid(string $uuid): void
    {
        $valid = isUuid($uuid);

        assertTrue($valid);
    }
}
