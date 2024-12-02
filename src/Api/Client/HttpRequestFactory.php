<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

use Doudenko\Api\Request\ApiRequestInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

readonly class HttpRequestFactory implements HttpRequestFactoryInterface
{
    public function __construct(
        protected RequestFactoryInterface $requestFactory,
        protected SerializerInterface $serializer,
        protected string $baseUri,
    ) {}

    final public function create(ApiRequestInterface $apiRequest): RequestInterface
    {
        $request = $this->requestFactory->createRequest(
            $apiRequest->getHttpMethod()->value,
            $this->createUri($apiRequest),
        );

        foreach ($this->getHeaders($apiRequest) as [$headerName, $headerValue]) {
            $request = $request->withAddedHeader($headerName, $headerValue);
        }

        return $request->withBody(
            $this->createBody($apiRequest),
        );
    }

    protected function createUri(ApiRequestInterface $apiRequest): UriInterface
    {
        $uri = $this->baseUri . $apiRequest->getUriPath();
        $queryParameters = $this->getQueryParameters($apiRequest);

        if ($queryParameters !== []) {
            $uri .= '?' . http_build_query($queryParameters);
        }

        return $this->requestFactory->createUri($uri);
    }

    protected function isRequestHasBody(ApiRequestInterface $apiRequest): bool
    {
        return !in_array($apiRequest->getHttpMethod(), [HttpMethod::Get, HttpMethod::Head], true);
    }

    protected function getQueryParameters(ApiRequestInterface $apiRequest): array
    {
        if ($this->isRequestHasBody($apiRequest)) {
            return [];
        }

        return $this->serializer->normalize(
            $apiRequest->getPayload(),
        );
    }

    protected function getHeaders(ApiRequestInterface $apiRequest): array
    {
        return [];
    }

    protected function createBody(ApiRequestInterface $apiRequest): StreamInterface
    {
        if (!$this->isRequestHasBody($apiRequest)) {
            return $this->requestFactory->createStream();
        }

        $rawBody = $this->serializer->serialize(
            $apiRequest->getPayload(),
            JsonEncoder::FORMAT,
            $this->getSerializationContext(),
        );

        return $this->requestFactory->createStream(
            $rawBody,
        );
    }

    protected function getSerializationContext(): array
    {
        return [
            JsonEncode::OPTIONS => JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE,
        ];
    }
}
