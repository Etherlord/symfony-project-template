<?php

declare(strict_types=1);

namespace App\Infrastructure\PasswordHasher;

final readonly class PasswordHasher
{
    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verify(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }
}
