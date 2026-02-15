<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Infrastructure\MessageBus\Message;
use App\Infrastructure\MessageBus\Symfony\MessageBus;
use App\Infrastructure\SymfonyIntegration\Console\ConsoleCommand;
use App\Infrastructure\SymfonyIntegration\Console\Output;
use Symfony\Component\Console\Input\InputInterface;

abstract class FixturesCommand extends ConsoleCommand
{
    public function __construct(
        private readonly MessageBus $messageBus,
    ) {
        parent::__construct();
    }

    abstract public static function fixturesName(): string;

    #[\Override]
    final protected static function name(): string
    {
        return 'fixtures:' . static::fixturesName();
    }

    #[\Override]
    final protected function doExecute(InputInterface $input, Output $output): int
    {
        $output->title(\sprintf('Loading "%s" fixtures', static::fixturesName()));

        $progressBar = $output->progressBar('fixtures');
        $progressBar->start();

        foreach ($this->commands($input, $output) as $command) {
            $this->messageBus->execute($command);
            $progressBar->advance();
        }

        $progressBar->finish();
        $output->newLine(2);
        $output->success(\sprintf('Successfully loaded "%s" fixtures.', static::fixturesName()));

        return self::SUCCESS;
    }

    /**
     * @return \Generator<Message>
     */
    abstract protected function commands(InputInterface $input, Output $output): \Generator;
}
