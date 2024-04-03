<?php

declare(strict_types=1);

namespace App\Feature\User\Repository;

use App\Infrastructure\Uuid\Uuid;

final readonly class User
{
    public function __construct(
        public Uuid $userId,
        public string $login,
        public string $hashedPassword,
    ) {
    }
}
