<?php

declare(strict_types=1);

namespace Doudenko\Api\Tests\Client;

use Doudenko\Api\Client\ApiClient;
use Doudenko\Api\Client\HttpMethod;
use Doudenko\Api\Client\HttpRequestFactoryInterface;
use Doudenko\Api\Request\ApiRequestInterface;
use Doudenko\Api\Response\ApiResponse;
use Doudenko\Api\Response\ApiResponseInterface;
use Generator;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ApiClientTest extends TestCase
{
    #[Test]
    #[DataProvider('generateApiRequestParameters')]
    public function sendAsyncPositive(HttpMethod $method, string $uri, mixed $payload): void
    {
        $apiRequest = $this->createApiRequest($method, $uri, $payload);
        $request = new Request($method->value, $uri);

        $httpRequestFactoryMock = mock(HttpRequestFactoryInterface::class);
        $httpRequestFactoryMock
            ->shouldReceive('createRequest')
            ->with($apiRequest)
            ->andReturn($request)
            ->atLeast()
            ->once()
        ;

        $expectedResponse = new ApiResponse();

        $promiseMock = mock(Promise::class);
        $promiseMock->shouldReceive('then')->withAnyArgs()->andReturn($promiseMock);
        $promiseMock->shouldReceive('wait')->withNoArgs()->andReturn($expectedResponse);

        $httpClientMock = mock(ClientInterface::class);
        $httpClientMock
            ->shouldReceive('sendAsync')
            ->with($request, [])
            ->andReturn($promiseMock)
            ->atLeast()
            ->once()
        ;

        $serializerMock = mock(SerializerInterface::class);

        $apiClient = new ApiClient(
            $httpRequestFactoryMock,
            $httpClientMock,
            $serializerMock,
        );

        $actualResponse = $apiClient->sendAsync($apiRequest, ApiResponse::class)->wait();

        self::assertInstanceOf(ApiResponseInterface::class, $actualResponse);
        self::assertSame($expectedResponse, $actualResponse);
    }

    private function createApiRequest(HttpMethod $method, string $uri, mixed $payload): ApiRequestInterface
    {
        return new readonly class ($method, $uri, $payload) implements ApiRequestInterface
        {
            public function __construct(
                public HttpMethod $method,
                public string $uri,
                public mixed $payload,
            ) {
            }

            public function getHttpMethod(): HttpMethod
            {
                return $this->method;
            }

            public function getUriPath(): string
            {
                return $this->uri;
            }

            public function getPayload(): mixed
            {
                return $this->payload;
            }
        };
    }

    public static function generateApiRequestParameters(): Generator
    {
        yield ['method' => HttpMethod::Get, 'uri' => '/', 'payload' => ['first' => 'one', 'second' => 'two']];
        yield ['method' => HttpMethod::Post, 'uri' => '/', 'payload' => ['first' => 'one', 'second' => 'two']];
    }
}
