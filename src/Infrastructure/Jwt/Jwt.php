<?php

declare(strict_types=1);

namespace App\Infrastructure\Jwt;

interface Jwt
{
    /**
     * @return non-empty-array<string, string>
     */
    public function data(): array;
}
