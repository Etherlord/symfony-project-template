<?php

declare(strict_types=1);

namespace App\Feature\User;

use App\Infrastructure\MessageBus\Message;
use App\Infrastructure\Uuid\Uuid;

/**
 * @psalm-immutable
 * @implements Message<void>
 */
final readonly class CreateUser implements Message
{
    public function __construct(
        public Uuid $userId,
        public string $login,
        public string $password,
    ) {
    }
}
