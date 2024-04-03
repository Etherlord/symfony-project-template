<?php

declare(strict_types=1);

namespace App\Feature\User;

use App\Infrastructure\Jwt\UserJwt;
use App\Infrastructure\MessageBus\Message;

/**
 * @psalm-immutable
 * @implements Message<?UserJwt>
 */
final readonly class Authenticate implements Message
{
    public function __construct(
        public string $login,
        public string $password,
    ) {
    }
}
