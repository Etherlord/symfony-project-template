<?php

declare(strict_types=1);

namespace App\Feature\User\Repository;

use App\Infrastructure\Doctrine\SchemaConfigurator\SchemaConfigurator;
use App\Infrastructure\Doctrine\SchemaConfigurator\SchemaSubscriber;
use App\Infrastructure\PostgresThesis\PostgresConnection;
use Doctrine\DBAL\Types\Exception\TypesException;
use Thesis\StatementContext\Tsx;
use Thesis\StatementExecutor\StatementExecutionException;

final readonly class PostgresUserRepository implements UserRepository, SchemaSubscriber
{
    public function __construct(
        private PostgresConnection $connection,
    ) {
    }

    /**
     * @throws TypesException
     */
    #[\Override]
    public function configureSchema(SchemaConfigurator $schema): void
    {
        $schema
            ->table('user')
            ->uuidColumn('user_id')
            ->stringColumn('login')
            ->stringColumn('hashed_password')
            ->primaryKey('user_id')
            ->uniqueIndex('login')
        ;
    }

    /**
     * @throws StatementExecutionException
     */
    #[\Override]
    public function createUser(User $user): void
    {
        $this->connection->execute(
            static fn(Tsx $tsx) => <<<SQL
                insert into "user" (user_id, login, hashed_password) 
                values ({$tsx($user->userId)}, {$tsx($user->login)}, {$tsx($user->hashedPassword)})
                on conflict on constraint user_pkey do nothing
                SQL,
        );
    }

    /**
     * @throws StatementExecutionException
     */
    #[\Override]
    public function getUserByLogin(string $login): ?User
    {
        return $this
            ->connection
            ->execute(
                static fn(Tsx $tsx) => <<<SQL
                    select user_id as "userId", login, hashed_password as "hashedPassword"
                    from "user"
                    where login = {$tsx($login)}
                    SQL,
            )
            ->hydrate(User::class)
            ->fetch()
        ;
    }
}
