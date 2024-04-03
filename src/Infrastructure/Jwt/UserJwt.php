<?php

declare(strict_types=1);

namespace App\Infrastructure\Jwt;

use App\Infrastructure\Uuid\Uuid;

final readonly class UserJwt implements Jwt
{
    public function __construct(
        private Uuid $userId,
    ) {
    }

    #[\Override]
    public function data(): array
    {
        return ['user_id' => $this->userId->toString()];
    }
}
