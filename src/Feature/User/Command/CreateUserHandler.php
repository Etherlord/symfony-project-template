<?php

declare(strict_types=1);

namespace App\Feature\User\Command;

use App\Feature\User\CreateUser;
use App\Feature\User\Repository\User;
use App\Feature\User\Repository\UserRepository;
use App\Infrastructure\PasswordHasher\PasswordHasher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final readonly class CreateUserHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasher $passwordHasher,
    ) {
    }

    #[AsMessageHandler]
    public function __invoke(CreateUser $message): void
    {
        $this->userRepository->createUser(
            new User(
                userId: $message->userId,
                login: $message->login,
                hashedPassword: $this->passwordHasher->hash($message->password),
            ),
        );
    }
}
