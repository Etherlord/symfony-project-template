<?php

declare(strict_types=1);

namespace App\Http\ApiV1\Authenticate;

use App\Feature\User\Authenticate;
use App\Infrastructure\Http\Status;
use App\Infrastructure\Http\Symfony\ErrorResponse;
use App\Infrastructure\Jwt\LexikJWTIntegration\JwtEncoder;
use App\Infrastructure\MessageBus\Symfony\MessageBus;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

final class Action
{
    /**
     * @throws JWTEncodeFailureException
     */
    #[OA\RequestBody(content: new Model(type: Request::class))]
    #[OA\Response(
        response: Status::OK->value,
        description: 'Success response',
        content: new Model(type: Response::class),
    )]
    #[OA\Response(
        response: Status::UNAUTHORIZED->value,
        description: 'Unauthorized response',
        content: new Model(type: ErrorResponse::class),
    )]
    #[OA\Response(
        response: Status::TOO_MANY_REQUESTS->value,
        description: 'Too many requests response',
        content: new Model(type: ErrorResponse::class),
    )]
    #[Route(path: '/authenticate', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] Request $request, MessageBus $messageBus, JwtEncoder $jwtEncoder, RateLimiterFactory $authenticateLimiter): Response|InvalidLoginOrPassword|TooManyFailedAttempts
    {
        $email = mb_strtolower($request->email);

        $limit = $authenticateLimiter->create($email)->consume();

        if (!$limit->isAccepted()) {
            return new TooManyFailedAttempts();
        }

        $jwt = $messageBus->execute(new Authenticate(
            login: $email,
            password: $request->password,
        ));

        if ($jwt === null) {
            return new InvalidLoginOrPassword();
        }

        return new Response($jwtEncoder->encode($jwt));
    }
}
