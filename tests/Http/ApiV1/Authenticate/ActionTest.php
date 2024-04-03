<?php

declare(strict_types=1);

namespace App\Http\ApiV1\Authenticate;

use App\Fixtures\UserFixturesCommand;
use App\Tests\TestApp;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use function App\Infrastructure\jsonDecode;
use function App\Tests\Functional\assertResponseIsSuccessful;
use function App\Tests\Functional\assertResponseStatusCodeSame;
use function App\Tests\Functional\createJsonPostRequest;
use function PHPUnit\Framework\assertNotFalse;
use function PHPUnit\Framework\assertTrue;

/**
 * @internal
 */
#[CoversClass(Action::class)]
#[Group('functional')]
final class ActionTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        /** @var RateLimiterFactory */
        $limiter = TestApp::service('limiter.authenticate');
        $limiter->create(UserFixturesCommand::USER_LOGIN)->reset();
    }

    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    public function testSuccess(): void
    {
        $request = createJsonPostRequest(
            '/api/v1/authenticate',
            null,
            [
                'email' => UserFixturesCommand::USER_LOGIN,
                'password' => UserFixturesCommand::USER_PASSWORD,
            ],
        );

        $response = TestApp::handleRequest($request);

        assertResponseIsSuccessful($response);
        $responseContent = $response->getContent();
        assertNotFalse($responseContent);
        assertTrue(isset(jsonDecode($responseContent)['token']));
    }

    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    public function testInvalidCredentials(): void
    {
        $request = createJsonPostRequest(
            '/api/v1/authenticate',
            null,
            [
                'email' => UserFixturesCommand::USER_LOGIN,
                'password' => 'wrong password',
            ],
        );

        $response = TestApp::handleRequest($request);

        assertResponseStatusCodeSame($response, 401);
    }

    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    public function testTooManyAttempts(): void
    {
        $request = createJsonPostRequest(
            '/api/v1/authenticate',
            null,
            [
                'email' => UserFixturesCommand::USER_LOGIN,
                'password' => 'wrong password',
            ],
        );

        TestApp::handleRequest($request);
        TestApp::handleRequest($request);
        TestApp::handleRequest($request);
        TestApp::handleRequest($request);
        TestApp::handleRequest($request);
        $response = TestApp::handleRequest($request);

        assertResponseStatusCodeSame($response, 429);
    }
}
