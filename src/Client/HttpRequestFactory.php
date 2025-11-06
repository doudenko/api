<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

use Doudenko\Api\Request\ApiRequestInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

readonly class HttpRequestFactory implements HttpRequestFactoryInterface
{
    public function __construct(
        protected RequestConfiguration $requestConfiguration,
        protected RequestFactoryInterface $requestFactory,
        protected NormalizerInterface & SerializerInterface $serializer,
    ) {
    }

    final public function create(ApiRequestInterface $request): RequestInterface
    {
        $httpRequest = $this->requestFactory->createRequest(
            $request->httpMethod->value,
            $this->createUri($request),
        );

        foreach ($this->getHeaders($request) as $header => $value) {
            $httpRequest = $httpRequest->withAddedHeader($header, $value);
        }

        $bodyContent = $this->getBodyContent($request);

        return $httpRequest->withBody(
            $this->requestFactory->createStream($bodyContent),
        );
    }

    /**
     * @throws ExceptionInterface If an error occurred while processing the request query.
     */
    final protected function createUri(ApiRequestInterface $request): UriInterface
    {
        $uri = $this->requestConfiguration->baseUri . $request->uri;
        $parameters = $this->getQueryParameters($request);

        if ($parameters !== []) {
            $uri .= '?' . http_build_query($parameters);
        }

        return $this->requestFactory->createUri($uri);
    }

    /**
     * @return array<string, mixed>
     * @throws ExceptionInterface If an error occurred while processing the request query.
     */
    protected function getQueryParameters(ApiRequestInterface $request): array
    {
        if ($request->httpMethod->hasBody()) {
            return [];
        }

        return $this->serializer->normalize($request->getPayload());
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

        return $this->serializer->serialize($request->getPayload(), JsonEncoder::FORMAT);
    }
}
