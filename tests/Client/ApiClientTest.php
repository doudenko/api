<?php

declare(strict_types=1);

namespace Doudenko\Api\Tests\Client;

use Doudenko\Api\Client\ApiClient;
use Doudenko\Api\Client\HttpMethod;
use Doudenko\Api\Client\HttpRequestFactoryInterface;
use Doudenko\Api\Client\HttpRequestOptions;
use Doudenko\Api\Exception\DomainClientException;
use Doudenko\Api\Request\AbstractApiRequest;
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

final class ApiClientTest extends TestCase
{
    #[Test]
    #[DataProvider('generateApiRequestParameters')]
    public function sendAsyncPositive(HttpMethod $method, string $uri, mixed $payload): void
    {
        $request = $this->createRequest($method, $uri, $payload);

        $httpRequestFactoryMock = mock(HttpRequestFactoryInterface::class);
        $httpRequestFactoryMock
            ->shouldReceive('create')
            ->with($request)
            ->andReturn($httpRequest = new Request($method->value, $uri))
            ->atLeast()
            ->once()
        ;

        $promiseMock = mock(Promise::class);
        $promiseMock->shouldReceive('then')->withAnyArgs()->andReturn($promiseMock);
        $promiseMock->shouldReceive('otherwise')->withAnyArgs()->andReturn($promiseMock);
        $promiseMock->shouldReceive('wait')->withNoArgs()->andReturn($expectedResponse = new ApiResponse());

        $httpRequestOptionsMock = mock(HttpRequestOptions::class);
        $httpRequestOptionsMock->shouldReceive('toArray')->withNoArgs()->andReturn($options = []);

        $httpClientMock = mock(ClientInterface::class);
        $httpClientMock
            ->shouldReceive('sendAsync')
            ->with($httpRequest, $options)
            ->andReturn($promiseMock)
            ->atLeast()
            ->once()
        ;

        $apiClient = new ApiClient(
            $httpRequestFactoryMock,
            $httpClientMock,
            mock(SerializerInterface::class),
            $httpRequestOptionsMock,
        );

        $actualResponse = $apiClient->sendAsync($request, ApiResponse::class)->wait();

        self::assertInstanceOf(ApiResponseInterface::class, $actualResponse);
        self::assertSame($expectedResponse, $actualResponse);
    }

    #[Test]
    #[DataProvider('generateApiRequestParameters')]
    public function sendAsyncNegative(HttpMethod $method, string $uri, mixed $payload): void
    {
        $this->expectException(DomainClientException::class);

        $apiClient = new ApiClient(
            mock(HttpRequestFactoryInterface::class),
            mock(ClientInterface::class),
            mock(SerializerInterface::class),
        );

        $request = $this->createRequest($method, $uri, $payload);
        $apiClient->sendAsync($request, stdClass::class);
    }

    private function createRequest(HttpMethod $httpMethod, string $uri, mixed $payload): AbstractApiRequest
    {
        return new class ($httpMethod, $uri, $payload) extends AbstractApiRequest
        {
            public function __construct(
                public HttpMethod $httpMethod,
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
        yield ['method' => HttpMethod::Get, 'uri' => '/path', 'payload' => []];
        yield ['method' => HttpMethod::Get, 'uri' => '/path', 'payload' => ['first' => 'one', 'second' => 'two']];
        yield ['method' => HttpMethod::Put, 'uri' => '/path', 'payload' => []];
        yield ['method' => HttpMethod::Put, 'uri' => '/path', 'payload' => ['first' => 'one', 'second' => 'two']];
    }
}
