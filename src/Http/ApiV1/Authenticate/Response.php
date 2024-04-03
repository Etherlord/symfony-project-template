<?php

declare(strict_types=1);

namespace App\Http\ApiV1\Authenticate;

final readonly class Response
{
    public function __construct(
        public string $token,
    ) {
    }
}
