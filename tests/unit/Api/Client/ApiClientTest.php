<?php

declare(strict_types=1);

namespace Doudenko\Api\Tests\Client;

use Doudenko\Api\Client\ApiClient;
use Doudenko\Api\Client\HttpMethod;
use Doudenko\Api\Client\HttpRequestFactoryInterface;
use Doudenko\Api\Client\HttpRequestOptions;
use Doudenko\Api\Exception\DomainClientException;
use Doudenko\Api\Request\AbstractApiRequest;
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
use stdClass;
use Symfony\Component\Serializer\SerializerInterface;

class ApiClientTest extends TestCase
{
    #[Test]
    #[DataProvider('generateApiRequestParameters')]
    public function sendAsyncPositive(HttpMethod $method, string $uri, mixed $payload): void
    {
        $apiRequest = $this->createApiRequest($method, $uri, $payload);

        $httpRequestFactoryMock = mock(HttpRequestFactoryInterface::class);
        $httpRequestFactoryMock
            ->shouldReceive('createRequest')
            ->with($apiRequest)
            ->andReturn($request = new Request($method->value, $uri))
            ->atLeast()
            ->once()
        ;

        $promiseMock = mock(Promise::class);
        $promiseMock->shouldReceive('then')->withAnyArgs()->andReturn($promiseMock);
        $promiseMock->shouldReceive('otherwise')->withAnyArgs()->andReturn($promiseMock);
        $promiseMock->shouldReceive('wait')->withNoArgs()->andReturn($response = new ApiResponse());

        $optionsMock = mock(HttpRequestOptions::class);
        $optionsMock->shouldReceive('toArray')->withNoArgs()->andReturn($options = []);

        $httpClientMock = mock(ClientInterface::class);
        $httpClientMock
            ->shouldReceive('sendAsync')
            ->with($request, $options)
            ->andReturn($promiseMock)
            ->atLeast()
            ->once()
        ;

        $apiClient = new ApiClient(
            $httpRequestFactoryMock,
            $httpClientMock,
            mock(SerializerInterface::class),
            $optionsMock,
        );

        $actualResponse = $apiClient->sendAsync($apiRequest, ApiResponse::class)->wait();

        self::assertInstanceOf(ApiResponseInterface::class, $actualResponse);
        self::assertSame($response, $actualResponse);
    }

    #[Test]
    #[DataProvider('generateApiRequestParameters')]
    public function sendAsyncNegative(HttpMethod $method, string $uri, mixed $payload): void
    {
        $apiClient = new ApiClient(
            mock(HttpRequestFactoryInterface::class),
            mock(ClientInterface::class),
            mock(SerializerInterface::class),
        );

        $this->expectException(DomainClientException::class);

        $apiRequest = $this->createApiRequest($method, $uri, $payload);
        $apiClient->sendAsync($apiRequest, stdClass::class);
    }

    private function createApiRequest(HttpMethod $method, string $uri, mixed $payload): ApiRequestInterface
    {
        return new class ($method, $uri, $payload) extends AbstractApiRequest
        {
            public function __construct(
                public HttpMethod $method,
                public string $uri,
                private readonly mixed $payload,
            ) {
            }

            public function getPayload(): mixed
            {
                return $this->payload;
            }
        };
    }

    public static function generateApiRequestParameters(): Generator
    {
        yield ['method' => HttpMethod::Get, 'uri' => '/path', 'payload' => ['first' => 'one', 'second' => 'two']];
        yield ['method' => HttpMethod::Put, 'uri' => '/path', 'payload' => ['first' => 'one', 'second' => 'two']];
    }
}
