<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Symfony;

use App\Infrastructure\Uuid\Uuid;
use OpenApi\Attributes as OA;

final readonly class ErrorResponse
{
    public function __construct(
        #[OA\Property(type: 'string', example: '1f1037d4-9bd3-406b-a810-e3cf928cf3e8')]
        public Uuid $errorId,
        public string $title,
    ) {
    }
}
