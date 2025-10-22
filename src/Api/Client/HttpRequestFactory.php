<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\RequestException;
use Doudenko\Api\Request\ApiRequestInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

readonly class HttpRequestFactory implements HttpRequestFactoryInterface
{
    public function __construct(
        protected RequestFactoryInterface $requestFactory,
        protected SerializerInterface $serializer,
        protected Configuration $configuration,
    ) {
    }

    final public function createRequest(ApiRequestInterface $apiRequest): RequestInterface
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
        $uri = $this->configuration->baseUri . $apiRequest->getUriPath();
        $queryParameters = $this->getQueryParameters($apiRequest);

        if ($queryParameters !== []) {
            $uri .= '?' . http_build_query($queryParameters);
        }

        return $this->requestFactory->createUri($uri);
    }

    protected function getQueryParameters(ApiRequestInterface $apiRequest): array
    {
        if ($apiRequest->getHttpMethod()->hasBody()) {
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

    /**
     * @throws RequestException
     */
    protected function createBody(ApiRequestInterface $apiRequest): StreamInterface
    {
        if (!$apiRequest->getHttpMethod()->hasBody()) {
            return $this->requestFactory->createStream();
        }

        try {
            $rawBody = $this->serializer->serialize(
                $apiRequest->getPayload(),
                JsonEncoder::FORMAT,
                $this->getSerializationContext(),
            );
        } catch (ExceptionInterface $exception) {
            throw new RequestException(
                $apiRequest,
                'An error occurred while encoding the API request.',
                previous: $exception,
            );
        }

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
