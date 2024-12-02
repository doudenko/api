<?php

declare(strict_types=1);

namespace Doudenko\Api\Tests\Client;

use Doudenko\Api\Client\ApiClient;
use Doudenko\Api\Client\HttpMethod;
use Doudenko\Api\Client\HttpRequestFactoryInterface;
use Doudenko\Api\Request\ApiRequestInterface;
use Doudenko\Api\Request\ApiRequestWithPayloadProperty;
use Doudenko\Api\Response\ApiResponse;
use Doudenko\Api\Response\ApiResponseInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ApiClientTest extends TestCase
{
    public function testSendAsync(): void
    {
        $apiRequest = $this->createRequest();
        $httpRequest = new Request($apiRequest->getUriPath(), $apiRequest->getHttpMethod()->value);

        $httpRequestFactoryMock = mock(HttpRequestFactoryInterface::class);
        $httpRequestFactoryMock
            ->shouldReceive('create')
            ->with($apiRequest)
            ->andReturn($httpRequest)
            ->atLeast()->once();

        $httpClientMock = mock(ClientInterface::class);
        $httpClientMock
            ->shouldReceive('sendAsync')
            ->with($httpRequest, [])
            ->andReturn(new Promise())
            ->atLeast()->once();

        $serializerMock = mock(SerializerInterface::class);

        $apiClient = new ApiClient(
            $httpRequestFactoryMock,
            $httpClientMock,
            $serializerMock,
        );

        $expectedResponse = new ApiResponse();

        $promise = $apiClient->sendAsync($apiRequest, ApiResponse::class);
        $promise->resolve($expectedResponse);

        $actualResponse = $promise->wait();

        self::assertInstanceOf(ApiResponseInterface::class, $actualResponse);
        self::assertSame($expectedResponse, $actualResponse);
    }

    public function createRequest(): ApiRequestInterface
    {
        return new class([]) extends ApiRequestWithPayloadProperty {
            public function getHttpMethod(): HttpMethod { return HttpMethod::Get; }
            public function getUriPath(): string { return '/'; }
        };
    }
}
