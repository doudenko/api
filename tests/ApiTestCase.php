<?php

declare(strict_types=1);

namespace Doudenko\Api\Tests;

use Doudenko\Api\Client\ApiConfiguration;
use Doudenko\Api\Client\HttpMethod;
use Doudenko\Api\Request\AbstractApiRequest;
use Doudenko\Api\Request\ApiRequestInterface;
use Generator;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class ApiTestCase extends TestCase
{
    protected const string EXAMPLE_BASE_URI = 'https://example.com';

    protected function mockConverter(): MockInterface & SerializerInterface & NormalizerInterface
    {
        /**
         * @var MockInterface & SerializerInterface & NormalizerInterface $converterMock
         */
        $converterMock = mock(
            SerializerInterface::class,
            NormalizerInterface::class,
        );

        return $converterMock;
    }

    protected function createApiConfiguration(string $baseUri): ApiConfiguration
    {
        return new ApiConfiguration(
            $baseUri,
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function createApiRequest(HttpMethod $method, string $uri, array $parameters): ApiRequestInterface
    {
        return new class ($method, $uri, $parameters) extends AbstractApiRequest
        {
            public function __construct(
                public readonly HttpMethod $httpMethod,
                public readonly string $uri,
                private readonly array $payload,
            ) {
            }

            public function getPayload(): array
            {
                return $this->payload;
            }
        };
    }

    /**
     * @return Generator<int, array<string, mixed>>
     */
    public static function generateApiRequestParameters(): Generator
    {
        // Dataset for bodiless method without a payload
        yield ['method' => HttpMethod::Get, 'uri' => '/path', 'parameters' => [], 'body' => ''];

        // Dataset for bodiless method with a payload
        yield ['method' => HttpMethod::Get, 'uri' => '/path', 'parameters' => ['parameter' => 'value'], 'body' => ''];

        // Dataset for a method that has a body without a payload
        yield ['method' => HttpMethod::Put, 'uri' => '/path', 'parameters' => [], 'body' => '{}'];

        // Dataset for a method that has a body with a payload
        yield ['method' => HttpMethod::Put, 'uri' => '/path', 'parameters' => ['parameter' => 'value'], 'body' => '{"parameter": "value"}'];
    }
}
