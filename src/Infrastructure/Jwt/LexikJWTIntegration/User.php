<?php

declare(strict_types=1);

namespace App\Infrastructure\Jwt\LexikJWTIntegration;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

final class User implements JWTUserInterface
{
    #[\Override]
    public static function createFromPayload($username, array $payload): self
    {
        return new self();
    }

    #[\Override]
    public function getRoles(): array
    {
        return [];
    }

    #[\Override]
    public function eraseCredentials(): void
    {
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        return 'api';
    }
}
