<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\RequestException;
use Doudenko\Api\Request\ApiRequestInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
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
        try {
            $uri = $this->getUri($apiRequest);
            $bodyContent = $this->getBodyContent($apiRequest);
        } catch (ExceptionInterface $exception) {
            throw new RequestException(
                $apiRequest,
                'An error occurred while encoding the API request.',
                previous: $exception,
            );
        }

        $request = $this->requestFactory->createRequest(
            $apiRequest->method->value,
            $this->requestFactory->createUri($uri),
        );

        foreach ($this->getHeaders($apiRequest) as $header => $value) {
            $request = $request->withAddedHeader($header, $value);
        }

        return $request->withBody(
            $this->requestFactory->createStream($bodyContent),
        );
    }

    /**
     * @throws ExceptionInterface
     */
    protected function getUri(ApiRequestInterface $apiRequest): string
    {
        $uri = $this->configuration->baseUri . $apiRequest->uri;
        $queryParameters = $this->getQueryParameters($apiRequest);

        if ($queryParameters !== []) {
            $uri .= '?' . http_build_query($queryParameters);
        }

        return $uri;
    }

    protected function getHeaders(ApiRequestInterface $apiRequest): array
    {
        return [];
    }

    /**
     * @throws ExceptionInterface
     */
    private function getQueryParameters(ApiRequestInterface $apiRequest): array
    {
        if (!$apiRequest->method->hasEmptyBody()) {
            return [];
        }

        return $this->serializer->normalize(
            $apiRequest->getPayload(),
        );
    }

    /**
     * @throws ExceptionInterface
     */
    private function getBodyContent(ApiRequestInterface $apiRequest): string
    {
        if ($apiRequest->method->hasEmptyBody()) {
            return '';
        }

        return $this->serializer->serialize(
            $apiRequest->getPayload(),
            JsonEncoder::FORMAT,
        );
    }
}
