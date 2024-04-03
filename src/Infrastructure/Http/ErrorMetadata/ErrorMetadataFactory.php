<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ErrorMetadata;

interface ErrorMetadataFactory
{
    /**
     * @param class-string $class
     */
    public function get(string $class): ?Error;
}
