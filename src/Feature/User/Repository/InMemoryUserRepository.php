<?php

declare(strict_types=1);

namespace App\Feature\User\Repository;

final class InMemoryUserRepository implements UserRepository
{
    /**
     * @var array<string, User>
     */
    public array $users = [];

    #[\Override]
    public function createUser(User $user): void
    {
        if (isset($this->users[$user->login])) {
            return;
        }

        $this->users[$user->login] = $user;
    }

    #[\Override]
    public function getUserByLogin(string $login): ?User
    {
        return $this->users[$login] ?? null;
    }
}
