<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ErrorMetadata;

use App\Infrastructure\Http\Status;
use App\Infrastructure\Uuid\Uuid;
use function App\Infrastructure\Uuid\uuid;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class Error
{
    public Uuid $errorId;

    /**
     * @param literal-string $errorId
     */
    public function __construct(
        string $errorId,
        public string $title = '',
        public Status $status = Status::INTERNAL_SERVER_ERROR,
    ) {
        $this->errorId = uuid($errorId);
    }
}
