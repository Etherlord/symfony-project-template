<?php

declare(strict_types=1);

namespace App\Feature\User\Repository;

use App\Infrastructure\PostgresThesis\PostgresConnection;
use App\Tests\TestApp;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Thesis\StatementExecutor\StatementExecutionException;
use function App\Infrastructure\Uuid\uuid;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

/**
 * @internal
 */
#[CoversClass(PostgresUserRepository::class)]
#[Group('integration')]
final class PostgresUserRepositoryTest extends TestCase
{
    private const USER_ID = 'c0241e61-ef17-4518-afc1-b536960f812a';
    private const LOGIN = 'test';
    private const HASHED_PASS = 'test_pass';

    /** @psalm-suppress PropertyNotSetInConstructor */
    private UserRepository $userRepository;

    /** @psalm-suppress PropertyNotSetInConstructor */
    private PostgresConnection $connection;

    /**
     * @throws StatementExecutionException
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->connection = TestApp::getDbConnection();
        $this->connection->execute('begin');
        $this->userRepository = new PostgresUserRepository($this->connection);
    }

    /**
     * @throws StatementExecutionException
     */
    #[\Override]
    protected function tearDown(): void
    {
        $this->connection->execute('rollback');
    }

    /**
     * @throws StatementExecutionException
     */
    public function testCreateUser(): void
    {
        $this->userRepository->createUser(new User(
            userId: uuid(self::USER_ID),
            login: self::LOGIN,
            hashedPassword: self::HASHED_PASS,
        ));

        $user = $this->userRepository->getUserByLogin(self::LOGIN);
        assertNotNull($user);
        assertSame(self::USER_ID, $user->userId->toString());
        assertSame(self::LOGIN, $user->login);
        assertSame(self::HASHED_PASS, $user->hashedPassword);
    }

    /**
     * @throws StatementExecutionException
     */
    public function testCreateAlreadyExistingUser(): void
    {
        $this->userRepository->createUser(new User(
            userId: uuid(self::USER_ID),
            login: self::LOGIN,
            hashedPassword: self::HASHED_PASS,
        ));

        $this->userRepository->createUser(new User(
            userId: uuid(self::USER_ID),
            login: self::LOGIN,
            hashedPassword: 'new hashed pass',
        ));

        $user = $this->userRepository->getUserByLogin(self::LOGIN);
        assertNotNull($user);
        assertSame(self::USER_ID, $user->userId->toString());
        assertSame(self::LOGIN, $user->login);
        assertSame(self::HASHED_PASS, $user->hashedPassword);
    }

    /**
     * @throws StatementExecutionException
     */
    public function testGetUserByLogin(): void
    {
        $this->userRepository->createUser(new User(
            userId: uuid(self::USER_ID),
            login: self::LOGIN,
            hashedPassword: self::HASHED_PASS,
        ));

        $user = $this->userRepository->getUserByLogin(self::LOGIN);

        assertNotNull($user);
        assertSame(self::USER_ID, $user->userId->toString());
        assertSame(self::LOGIN, $user->login);
        assertSame(self::HASHED_PASS, $user->hashedPassword);
    }

    /**
     * @throws StatementExecutionException
     */
    public function testGetNonExistentUser(): void
    {
        $user = $this->userRepository->getUserByLogin('qwerty');

        assertNull($user);
    }
}
