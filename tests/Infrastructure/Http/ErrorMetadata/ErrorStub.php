<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ErrorMetadata;

use App\Infrastructure\Http\Status;

#[Error(errorId: 'ed17d972-abf5-4162-a112-8dc8ee8bd504', title: 'Ошибка', status: Status::BAD_REQUEST)]
final class ErrorStub
{
}
