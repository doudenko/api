<?php

declare(strict_types=1);

namespace Doudenko\Api\Tests\Client;

use Doudenko\Api\Client\ApiClient;
use Doudenko\Api\Client\HttpMethod;
use Doudenko\Api\Client\HttpRequestFactoryInterface;
use Doudenko\Api\Exception\DomainClientException;
use Doudenko\Api\Response\ApiResponse;
use Doudenko\Api\Response\ApiResponseInterface;
use Doudenko\Api\Tests\ApiTestCase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use stdClass;

final class ApiClientTest extends ApiTestCase
{
    /**
     * @param array<string, mixed> $parameters
     */
    #[Test]
    #[DataProvider('generateApiRequestParameters')]
    public function sendNegative(HttpMethod $method, string $uri, array $parameters, string $body): void
    {
        $apiClient = new ApiClient(
            mock(HttpRequestFactoryInterface::class),
            mock(ClientInterface::class),
            $this->mockConverter(),
            $this->createApiConfiguration(self::EXAMPLE_BASE_URI),
        );

        $this->expectException(DomainClientException::class);

        $apiRequest = $this->createApiRequest($method, $uri, $parameters);

        $apiClient->send($apiRequest, stdClass::class);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    #[Test]
    #[DataProvider('generateApiRequestParameters')]
    public function sendAsyncPositive(HttpMethod $method, string $uri, array $parameters, string $body): void
    {
        $apiConfiguration = $this->createApiConfiguration(self::EXAMPLE_BASE_URI);
        $apiRequest = $this->createApiRequest($method, $uri, $parameters);
        $httpRequest = new Request($method->value, $uri);

        $httpRequestFactoryMock = mock(HttpRequestFactoryInterface::class);
        $httpRequestFactoryMock
            ->shouldReceive('create')
            ->with($apiConfiguration, $apiRequest)
            ->andReturn($httpRequest)
            ->atLeast()
            ->once()
        ;

        $promiseMock = mock(Promise::class);
        $promiseMock->shouldReceive('then')->withAnyArgs()->andReturn($promiseMock);
        $promiseMock->shouldReceive('otherwise')->withAnyArgs()->andReturn($promiseMock);
        $promiseMock->shouldReceive('wait')->withNoArgs()->andReturn($expectedResponse = new ApiResponse());

        $httpClientMock = mock(ClientInterface::class);
        $httpClientMock
            ->shouldReceive('sendAsync')
            ->with($httpRequest, $apiConfiguration->httpOptions->toArray())
            ->andReturn($promiseMock)
            ->atLeast()
            ->once()
        ;

        $apiClient = new ApiClient(
            $httpRequestFactoryMock,
            $httpClientMock,
            $this->mockConverter(),
            $apiConfiguration,
        );

        $actualResponse = $apiClient->sendAsync($apiRequest, ApiResponse::class)->wait();

        self::assertInstanceOf(ApiResponseInterface::class, $actualResponse);
        self::assertSame($expectedResponse, $actualResponse);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    #[Test]
    #[DataProvider('generateApiRequestParameters')]
    public function sendAsyncNegative(HttpMethod $method, string $uri, array $parameters, string $body): void
    {
        $apiClient = new ApiClient(
            mock(HttpRequestFactoryInterface::class),
            mock(ClientInterface::class),
            $this->mockConverter(),
            $this->createApiConfiguration(self::EXAMPLE_BASE_URI),
        );

        $this->expectException(DomainClientException::class);

        $apiRequest = $this->createApiRequest($method, $uri, $parameters);

        $apiClient->sendAsync($apiRequest, stdClass::class);
    }
}
