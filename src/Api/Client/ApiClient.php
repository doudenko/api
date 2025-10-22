<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\DomainClientException;
use Doudenko\Api\Exception\RequestException;
use Doudenko\Api\Exception\ResponseException;
use Doudenko\Api\Request\ApiRequestInterface;
use Doudenko\Api\Response\ApiResponseInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

readonly class ApiClient implements ApiClientInterface
{
    public function __construct(
        protected HttpRequestFactoryInterface $httpRequestFactory,
        protected ClientInterface $httpClient,
        protected SerializerInterface $serializer,
    ) {
    }

    final public function send(ApiRequestInterface $apiRequest, string $responseClass): ApiResponseInterface
    {
        return $this->sendAsync($apiRequest, $responseClass)->wait();
    }

    final public function sendAsync(ApiRequestInterface $apiRequest, string $responseClass): PromiseInterface
    {
        $request = $this->httpRequestFactory->createRequest($apiRequest);
        $requestOptions = $this->getRequestOptions($request);

        $responsePromise = $this->httpClient->sendAsync($request, $requestOptions);

        return $responsePromise->then(
            fn (ResponseInterface $response) => $this->deserializeResponse($response, $responseClass),
            fn (ClientExceptionInterface $exception) => $this->throwException($apiRequest, $exception),
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRequestOptions(RequestInterface $request): array
    {
        return [];
    }

    /**
     * @template ClassName of ApiResponseInterface
     *
     * @param class-string<ClassName> $responseClass
     *
     * @return ClassName
     * @throws DomainClientException
     * @throws ResponseException
     */
    protected function deserializeResponse(ResponseInterface $response, string $responseClass): ApiResponseInterface
    {
        if (!class_exists($responseClass) || !is_a($responseClass, ApiResponseInterface::class, true)) {
            throw new DomainClientException('The specified class is not a valid API response class.');
        }

        $responsePayload = strval($response->getBody());

        try {
            return $this->serializer->deserialize(
                $responsePayload,
                $responseClass,
                JsonEncoder::FORMAT,
            );
        } catch (ExceptionInterface $exception) {
            throw new ResponseException(
                $response,
                'An error occurred while decoding the API response.',
                previous: $exception,
            );
        }
    }

    /**
     * @throws RequestException
     */
    protected function throwException(ApiRequestInterface $apiRequest, ClientExceptionInterface $exception): never
    {
        throw new RequestException(
            $apiRequest,
            'An error occurred while sending the API request.',
            previous: $exception,
        );
    }
}
