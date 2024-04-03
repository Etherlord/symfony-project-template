<?php

declare(strict_types=1);

namespace App\Http\ApiV1\Authenticate;

use App\Infrastructure\Http\ErrorMetadata\Error;
use App\Infrastructure\Http\Status;

#[Error(errorId: '63c32231-06c3-45f5-af6b-4291c450fde0', title: 'Превышен лимит неудачных попыток', status: Status::TOO_MANY_REQUESTS)]
final readonly class TooManyFailedAttempts
{
}
