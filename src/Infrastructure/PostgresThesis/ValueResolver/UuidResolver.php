<?php

declare(strict_types=1);

namespace App\Infrastructure\PostgresThesis\ValueResolver;

use App\Infrastructure\Uuid\Uuid;
use Thesis\StatementContext\ValueRecursiveResolver;
use Thesis\StatementContext\ValueResolver;

/**
 * @implements ValueResolver<Uuid>
 */
final class UuidResolver implements ValueResolver
{
    #[\Override]
    public static function valueTypes(): array
    {
        return [Uuid::class];
    }

    /**
     * @param Uuid $value
     */
    #[\Override]
    public function resolve(mixed $value, ValueRecursiveResolver $resolver): string
    {
        return $resolver->resolve($value->toString());
    }
}
