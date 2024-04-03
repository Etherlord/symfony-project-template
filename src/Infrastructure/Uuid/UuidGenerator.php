<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use Ramsey\Uuid\UuidFactory;

final readonly class UuidGenerator
{
    private UuidFactory $uuidFactory;

    public function __construct(
    ) {
        $this->uuidFactory = new UuidFactory();
    }

    public function uuid(): Uuid
    {
        return Uuid::fromRamsey($this->uuidFactory->uuid7());
    }
}
