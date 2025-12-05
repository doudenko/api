<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\ClientException;
use Doudenko\Api\Request\ApiRequestInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

readonly class HttpRequestFactory implements HttpRequestFactoryInterface
{
    public function __construct(
        protected UriFactoryInterface & RequestFactoryInterface & StreamFactoryInterface $httpRequestFactory,
        protected SerializerInterface & NormalizerInterface $converter,
    ) {
    }

    final public function create(ApiConfiguration $configuration, ApiRequestInterface $request): RequestInterface
    {
        $httpRequest = $this->httpRequestFactory->createRequest(
            $request->httpMethod->value,
            $this->createUri($configuration, $request),
        );

        foreach ($this->getHeaders($request) as $header => $value) {
            $httpRequest = $httpRequest->withAddedHeader($header, $value);
        }

        $bodyContent = $this->getBodyContent($request);

        return $httpRequest->withBody(
            $this->httpRequestFactory->createStream($bodyContent),
        );
    }

    /**
     * @throws ClientException If the query parameters are invalid.
     * @throws ExceptionInterface If an error occurred while processing the request query.
     */
    final protected function createUri(ApiConfiguration $configuration, ApiRequestInterface $request): UriInterface
    {
        $uri = $configuration->baseUri . $request->uri;
        $queryParameters = $this->getQueryParameters($request);

        if ($queryParameters !== []) {
            $uri .= '?' . http_build_query($queryParameters);
        }

        return $this->httpRequestFactory->createUri($uri);
    }

    /**
     * @return array<string, mixed>
     * @throws ClientException If the query parameters are invalid.
     * @throws ExceptionInterface If an error occurred while processing the request query.
     */
    protected function getQueryParameters(ApiRequestInterface $request): array
    {
        if ($request->httpMethod->hasBody()) {
            return [];
        }

        $queryParameters = $this->converter->normalize(
            $request->getPayload(),
        );

        if (!is_array($queryParameters)) {
            throw new ClientException('The query parameters are invalid.');
        }

        return $queryParameters;
    }

    /**
     * @return array<string, string>
     */
    protected function getHeaders(ApiRequestInterface $request): array
    {
        return [];
    }

    /**
     * @throws ExceptionInterface If an error occurred while processing the request body.
     */
    protected function getBodyContent(ApiRequestInterface $request): string
    {
        if (!$request->httpMethod->hasBody()) {
            return '';
        }

        return $this->converter->serialize(
            $request->getPayload(),
            JsonEncoder::FORMAT,
        );
    }
}
