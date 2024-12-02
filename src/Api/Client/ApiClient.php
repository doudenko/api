<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\ApiClientException;
use Doudenko\Api\Exception\ApiLogicException;
use Doudenko\Api\Request\ApiRequestInterface;
use Doudenko\Api\Response\ApiResponseInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

readonly class ApiClient implements ApiClientInterface
{
    public function __construct(
        protected HttpRequestFactoryInterface $httpRequestFactory,
        protected ClientInterface $httpClient,
        protected SerializerInterface $serializer,
    ) {}

    final public function send(ApiRequestInterface $apiRequest, string $responseClass): ApiResponseInterface
    {
        return $this->sendAsync($apiRequest, $responseClass)->wait();
    }

    final public function sendAsync(ApiRequestInterface $apiRequest, string $responseClass): PromiseInterface
    {
        $httpRequest = $this->httpRequestFactory->create(
            $apiRequest,
        );

        $promise = $this->httpClient->sendAsync(
            $httpRequest,
            $this->getRequestOptions($httpRequest),
        );

        return $promise->then(
            fn (ResponseInterface $response) => $this->deserializeResponseBody($response, $responseClass),
            fn (ClientExceptionInterface $exception) => $this->throwException($exception, $apiRequest),
        );
    }

    /**
     * @return array<string, string>
     */
    protected function getRequestOptions(RequestInterface $request): array
    {
        return [];
    }

    /**
     * @template ClassName
     *
     * @param class-string<ClassName> $responseClass
     *
     * @return ClassName
     */
    protected function deserializeResponseBody(ResponseInterface $response, string $responseClass): ApiResponseInterface
    {
        if (!class_exists($responseClass) || !is_a($responseClass, ApiResponseInterface::class, true)) {
            throw new ApiLogicException("The specified response class is not supported: {$responseClass}");
        }

        $responsePayload = (string)$response->getBody();

        return $this->serializer->deserialize(
            $responsePayload,
            $responseClass,
            JsonEncoder::FORMAT,
        );
    }

    protected function throwException(ClientExceptionInterface $exception, ApiRequestInterface $apiRequest): never
    {
        throw new ApiClientException(
            $apiRequest,
            'При отправке запроса произошла ошибка',
            $exception,
        );
    }
}
