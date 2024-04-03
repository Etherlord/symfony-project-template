<?php

declare(strict_types=1);

namespace App\Infrastructure;

/**
 * @psalm-pure
 * @throws \JsonException
 */
function jsonEncode(mixed $data, int $flags = 0): string
{
    return json_encode($data, $flags | JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
}

/**
 * @psalm-pure
 * @throws \JsonException
 */
function jsonDecode(string $json, int $flags = 0): mixed
{
    return json_decode($json, true, flags: $flags | JSON_THROW_ON_ERROR);
}
