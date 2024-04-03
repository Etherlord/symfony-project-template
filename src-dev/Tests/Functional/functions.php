<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Test\Constraint;

/**
 * @param ?array<string, string> $headers
 * @throws \JsonException
 */
function createJsonPostRequest(string $path, ?string $token = null, ?array $data = null, ?array $headers = null): Request
{
    $requestBuilder = RequestBuilder::post($path);

    if ($data !== null) {
        $requestBuilder->addJsonContent($data);
    }

    if ($headers !== null) {
        $requestBuilder->addHeaders($headers);
    }

    if ($token !== null) {
        $requestBuilder->addAuthorization($token);
    }

    return $requestBuilder->build();
}

function assertResponseIsSuccessful(Response $response, string $message = ''): void
{
    Assert::assertThat($response, new Constraint\ResponseIsSuccessful(), $message);
}

function assertResponseStatusCodeSame(Response $response, int $expectedCode, string $message = ''): void
{
    Assert::assertThat($response, new Constraint\ResponseStatusCodeSame($expectedCode), $message);
}
