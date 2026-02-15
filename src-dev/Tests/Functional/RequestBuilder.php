<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Request;
use function App\Infrastructure\jsonEncode;

final class RequestBuilder
{
    private array $parameters = [];

    private ?string $content = null;

    /**
     * @var array<string, string>
     */
    private array $headers = [
        'Accept-Charset' => 'utf-8',
    ];

    private function __construct(
        private readonly string $method,
        private readonly string $path,
    ) {
    }

    public static function get(string $path): self
    {
        return new self('GET', $path);
    }

    public static function post(string $path): self
    {
        return new self('POST', $path);
    }

    /**
     * @param array<string, string> $headers
     */
    public function addHeaders(array $headers): self
    {
        $this->headers = array_replace($this->headers, $headers);

        return $this;
    }

    public function addParameters(array $parameters): self
    {
        $this->parameters = array_replace($this->parameters, $parameters);

        return $this;
    }

    /**
     * @throws \JsonException
     */
    public function addJsonContent(array $content): self
    {
        $this->addHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        $this->content = jsonEncode($content);

        return $this;
    }

    public function addAuthorization(string $token): self
    {
        $this->addHeaders([
            'Authorization' => \sprintf('Bearer %s', $token),
        ]);

        return $this;
    }

    public function build(): Request
    {
        return Request::create(
            $this->path,
            $this->method,
            $this->parameters,
            [],
            [],
            array_combine(
                array_map(
                    static function (string $header): string {
                        $header = strtoupper(str_replace('-', '_', $header));

                        return match ($header) {
                            // https://www.php.net/manual/ru/reserved.variables.server.php#110763
                            'CONTENT_LENGTH', 'CONTENT_TYPE' => $header,
                            default => 'HTTP_' . $header,
                        };
                    },
                    array_keys($this->headers),
                ),
                $this->headers,
            ),
            $this->content,
        );
    }
}
