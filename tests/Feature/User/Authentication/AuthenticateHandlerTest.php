<?php

declare(strict_types=1);

namespace App\Feature\User\Authentication;

use App\Feature\User\Authenticate;
use App\Feature\User\Repository\InMemoryUserRepository;
use App\Feature\User\Repository\User;
use App\Feature\User\Repository\UserRepository;
use App\Infrastructure\PasswordHasher\PasswordHasher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use function App\Infrastructure\Uuid\uuid;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

/**
 * @internal
 */
#[CoversClass(AuthenticateHandler::class)]
#[Group('unit')]
final class AuthenticateHandlerTest extends TestCase
{
    private const USER_ID = 'c0241e61-ef17-4518-afc1-b536960f812a';
    private const LOGIN = 'test';
    private const PASS = 'password';
    private const HASHED_PASS = '$2y$10$mTqHYwc8P5uic1u0/bla0OiRpLDCV1HHzn.fruJKtvKZwtepACuiG';

    /** @psalm-suppress PropertyNotSetInConstructor */
    private UserRepository $userRepository;

    #[\Override]
    protected function setUp(): void
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->userRepository->createUser(
            new User(
                userId: uuid(self::USER_ID),
                login: self::LOGIN,
                hashedPassword: self::HASHED_PASS,
            ),
        );
    }

    public function testNonExistentUser(): void
    {
        $handler = new AuthenticateHandler(
            $this->userRepository,
            new PasswordHasher(),
        );

        $result = $handler(new Authenticate(
            login: 'user',
            password: 'pass',
        ));

        assertNull($result);
    }

    public function testBadPassword(): void
    {
        $handler = new AuthenticateHandler(
            $this->userRepository,
            new PasswordHasher(),
        );

        $result = $handler(new Authenticate(
            login: self::LOGIN,
            password: 'pass',
        ));

        assertNull($result);
    }

    public function testSuccessAuthenticate(): void
    {
        $handler = new AuthenticateHandler(
            $this->userRepository,
            new PasswordHasher(),
        );

        $result = $handler(new Authenticate(
            login: self::LOGIN,
            password: self::PASS,
        ));

        assertSame(self::USER_ID, $result?->data()['user_id'] ?? '');
    }
}
