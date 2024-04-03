<?php

declare(strict_types=1);

namespace App\Feature\User\Repository;

interface UserRepository
{
    public function createUser(User $user): void;

    public function getUserByLogin(string $login): ?User;
}
