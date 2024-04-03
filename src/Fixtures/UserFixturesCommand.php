<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Feature\User\CreateUser;
use App\Infrastructure\SymfonyIntegration\Console\Output;
use Symfony\Component\Console\Input\InputInterface;
use function App\Infrastructure\Uuid\uuid;

final class UserFixturesCommand extends FixturesCommand
{
    public const USER_ID = '53dd12d7-d847-41a4-8c73-fba31e2df3f0';
    public const USER_LOGIN = 'test@symfony-template.wip';
    public const USER_PASSWORD = 'test';

    #[\Override]
    public static function fixturesName(): string
    {
        return 'user';
    }

    #[\Override]
    protected function commands(InputInterface $input, Output $output): \Generator
    {
        yield new CreateUser(uuid(self::USER_ID), self::USER_LOGIN, self::USER_PASSWORD);
    }
}
