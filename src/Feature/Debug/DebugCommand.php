<?php

declare(strict_types=1);

namespace App\Feature\Debug;

use App\Infrastructure\SymfonyIntegration\Console\ConsoleCommand;
use App\Infrastructure\SymfonyIntegration\Console\Output;
use Symfony\Component\Console\Input\InputInterface;

final class DebugCommand extends ConsoleCommand
{
    #[\Override]
    protected static function name(): string
    {
        return 'debug';
    }

    #[\Override]
    protected function doExecute(InputInterface $input, Output $output): int
    {
        return self::SUCCESS;
    }
}
