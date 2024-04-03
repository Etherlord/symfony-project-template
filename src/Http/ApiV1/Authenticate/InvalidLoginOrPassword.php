<?php

declare(strict_types=1);

namespace App\Http\ApiV1\Authenticate;

use App\Infrastructure\Http\ErrorMetadata\Error;
use App\Infrastructure\Http\Status;

#[Error(errorId: 'd1dff7d3-b2b2-48c8-95b0-a91a811fffb3', title: 'Неверное имя пользователя или пароль', status: Status::UNAUTHORIZED)]
final readonly class InvalidLoginOrPassword
{
}
