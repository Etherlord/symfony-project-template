<?php

declare(strict_types=1);

namespace App\Feature\User\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use function App\Infrastructure\Uuid\uuid;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * @internal
 */
#[CoversClass(InMemoryUserRepository::class)]
#[Group('unit')]
final class InMemoryUserRepositoryTest extends TestCase
{
    private const USER_ID = 'c0241e61-ef17-4518-afc1-b536960f812a';
    private const LOGIN = 'test';
    private const HASHED_PASS = 'test_pass';

    public function testCreateUser(): void
    {
        $userRepository = new InMemoryUserRepository();

        $userRepository->createUser(new User(
            userId: uuid(self::USER_ID),
            login: self::LOGIN,
            hashedPassword: self::HASHED_PASS,
        ));

        assertTrue(isset($userRepository->users[self::LOGIN]));
        assertSame(self::USER_ID, $userRepository->users[self::LOGIN]->userId->toString());
        assertSame(self::LOGIN, $userRepository->users[self::LOGIN]->login);
        assertSame(self::HASHED_PASS, $userRepository->users[self::LOGIN]->hashedPassword);
    }

    public function testCreateAlreadyExistingUser(): void
    {
        $userRepository = new InMemoryUserRepository();
        $userRepository->createUser(new User(
            userId: uuid(self::USER_ID),
            login: self::LOGIN,
            hashedPassword: self::HASHED_PASS,
        ));

        $userRepository->createUser(new User(
            userId: uuid(self::USER_ID),
            login: self::LOGIN,
            hashedPassword: 'new hashed pass',
        ));

        assertTrue(isset($userRepository->users[self::LOGIN]));
        assertSame(self::USER_ID, $userRepository->users[self::LOGIN]->userId->toString());
        assertSame(self::LOGIN, $userRepository->users[self::LOGIN]->login);
        assertSame(self::HASHED_PASS, $userRepository->users[self::LOGIN]->hashedPassword);
    }

    public function testGetUserByLogin(): void
    {
        $userRepository = new InMemoryUserRepository();
        $userRepository->createUser(new User(
            userId: uuid(self::USER_ID),
            login: self::LOGIN,
            hashedPassword: self::HASHED_PASS,
        ));

        $user = $userRepository->getUserByLogin(self::LOGIN);

        assertNotNull($user);
        assertSame(self::USER_ID, $user->userId->toString());
        assertSame(self::LOGIN, $user->login);
        assertSame(self::HASHED_PASS, $user->hashedPassword);
    }

    public function testGetNonExistentUser(): void
    {
        $userRepository = new InMemoryUserRepository();

        $user = $userRepository->getUserByLogin('qwerty');

        assertNull($user);
    }
}
