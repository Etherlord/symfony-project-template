<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ErrorMetadata;

use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class CachingErrorMetadataFactory implements ErrorMetadataFactory
{
    public function __construct(
        private ErrorMetadataFactory $metadataFactory,
        private TagAwareCacheInterface $metadataCache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[\Override]
    public function get(string $class): ?Error
    {
        return $this->metadataCache->get(str_replace('\\', '-', $class), fn() => $this->metadataFactory->get($class));
    }
}
