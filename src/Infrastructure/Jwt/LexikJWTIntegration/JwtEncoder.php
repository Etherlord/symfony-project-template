<?php

declare(strict_types=1);

namespace App\Infrastructure\Jwt\LexikJWTIntegration;

use App\Infrastructure\Jwt\Jwt;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;

final readonly class JwtEncoder
{
    public function __construct(
        private JWTEncoderInterface $jwtEncoder,
    ) {
    }

    /**
     * @throws JWTEncodeFailureException
     */
    public function encode(Jwt $jwt): string
    {
        return $this->jwtEncoder->encode([
            'username' => 'api',
            ...$jwt->data(),
        ]);
    }
}
