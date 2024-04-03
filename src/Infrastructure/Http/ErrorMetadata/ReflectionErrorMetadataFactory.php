<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ErrorMetadata;

final class ReflectionErrorMetadataFactory implements ErrorMetadataFactory
{
    #[\Override]
    public function get(string $class): ?Error
    {
        $attributes = (new \ReflectionClass($class))->getAttributes(Error::class);

        if ($attributes) {
            return $attributes[0]->newInstance();
        }

        return null;
    }
}
