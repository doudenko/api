<?php

declare(strict_types=1);

namespace Doudenko\Api\Tests\Client;

use Doudenko\Api\Client\HttpMethod;
use Doudenko\Api\Client\HttpRequestFactory;
use Doudenko\Api\Tests\ApiTestCase;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class HttpRequestFactoryTest extends ApiTestCase
{
    /**
     * @param array<string, mixed> $parameters
     *
     * @throws ExceptionInterface
     */
    #[Test]
    #[DataProvider('generateApiRequestParameters')]
    public function createPositive(HttpMethod $method, string $uri, array $parameters, string $body): void
    {
        /**
         * @var MockInterface & UriFactoryInterface & RequestFactoryInterface & StreamFactoryInterface $httpRequestFactoryMock
         */
        $httpRequestFactoryMock = mock(
            UriFactoryInterface::class,
            RequestFactoryInterface::class,
            StreamFactoryInterface::class,
        );

        $uriModified = self::EXAMPLE_BASE_URI . $uri;

        if (!$method->hasBody() && $parameters !== []) {
            $uriModified .= '?' . http_build_query($parameters);
        }

        $httpRequestFactoryMock
            ->shouldReceive('createUri')
            ->with($uriModified)
            ->andReturn($expectedUri = new Uri($uriModified))
            ->atLeast()
            ->once()
        ;

        $httpRequestFactoryMock
            ->shouldReceive('createRequest')
            ->with($method->value, $expectedUri)
            ->andReturn(new Request($method->value, $expectedUri))
            ->atLeast()
            ->once()
        ;

        $expectedStream = mock(StreamInterface::class);
        $expectedStream
            ->shouldReceive('getContents')
            ->withNoArgs()
            ->andReturn($body)
            ->atLeast()
            ->once()
        ;

        $httpRequestFactoryMock
            ->shouldReceive('createStream')
            ->with($body)
            ->andReturn($expectedStream)
            ->atLeast()
            ->once()
        ;

        $converterMock = $this->mockConverter();

        if ($method->hasBody()) {
            $converterMock
                ->shouldReceive('serialize')
                ->with($parameters, JsonEncoder::FORMAT)
                ->andReturn($body)
                ->atLeast()
                ->once()
            ;
        } else {
            $converterMock
                ->shouldReceive('normalize')
                ->with($parameters)
                ->andReturn($parameters)
                ->atLeast()
                ->once()
            ;
        }

        $httpRequestFactory = new HttpRequestFactory(
            $httpRequestFactoryMock,
            $converterMock,
        );

        $apiConfiguration = $this->createApiConfiguration(self::EXAMPLE_BASE_URI);
        $apiRequest = $this->createApiRequest($method, $uri, $parameters);

        $actualHttpRequest = $httpRequestFactory->create(
            $apiConfiguration,
            $apiRequest,
        );

        self::assertEquals($method->value, $actualHttpRequest->getMethod());
        self::assertEquals($expectedUri, $actualHttpRequest->getUri());
        self::assertEquals($body, $actualHttpRequest->getBody()->getContents());
    }
}
