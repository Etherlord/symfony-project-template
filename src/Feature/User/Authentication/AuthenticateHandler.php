<?php

declare(strict_types=1);

namespace App\Feature\User\Authentication;

use App\Feature\User\Authenticate;
use App\Feature\User\Repository\UserRepository;
use App\Infrastructure\Jwt\UserJwt;
use App\Infrastructure\PasswordHasher\PasswordHasher;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

final readonly class AuthenticateHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasher $passwordHasher,
    ) {
    }

    #[AsMessageHandler]
    public function __invoke(Authenticate $message): ?UserJwt
    {
        $user = $this->userRepository->getUserByLogin($message->login);

        if ($user === null) {
            return null;
        }

        if (!$this->passwordHasher->verify($message->password, $user->hashedPassword)) {
            return null;
        }

        return new UserJwt($user->userId);
    }
}
