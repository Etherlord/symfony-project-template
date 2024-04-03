<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use Ramsey\Uuid\Uuid as RamseyUuid;

const NIL = '00000000-0000-0000-0000-000000000000';
const MAX = 'ffffffff-ffff-ffff-ffff-ffffffffffff';

/**
 * @template T of ?string
 * @psalm-param T $uuid
 * @psalm-return (T is null ? null : Uuid)
 */
function uuid(?string $uuid): ?Uuid
{
    if ($uuid === null) {
        return null;
    }

    return Uuid::fromString($uuid);
}

/**
 * @psalm-assert-if-true non-empty-string $uuid
 */
function isUuid(string $uuid): bool
{
    return RamseyUuid::isValid($uuid);
}
