<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\ClientException;
use Doudenko\Api\Exception\DomainClientException;
use Doudenko\Api\Request\ApiRequestInterface;
use Doudenko\Api\Response\ApiResponseInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

readonly class ApiClient implements ApiClientInterface
{
    public function __construct(
        protected HttpRequestFactoryInterface $httpRequestFactory,
        protected ClientInterface $httpClient,
        protected SerializerInterface $serializer,
        protected HttpRequestOptions $httpRequestOptions = new HttpRequestOptions(),
    ) {
    }

    final public function send(ApiRequestInterface $apiRequest, string $responseClass): ApiResponseInterface
    {
        return $this->sendAsync($apiRequest, $responseClass)->wait();
    }

    final public function sendAsync(ApiRequestInterface $apiRequest, string $responseClass): PromiseInterface
    {
        if (!class_exists($responseClass) || !is_a($responseClass, ApiResponseInterface::class, true)) {
            throw new DomainClientException('The specified class is not a valid API response class.');
        }

        $request = $this->httpRequestFactory->createRequest($apiRequest);
        $requestOptions = $this->httpRequestOptions->toArray();

        $promise = $this->httpClient->sendAsync($request, $requestOptions);

        return $promise
            ->then(fn (ResponseInterface $response) => $this->deserializeResponseBody($response, $responseClass))
            ->otherwise(fn (Throwable $throwable) => $this->throwClientException($throwable));
    }

    /**
     * @template ClassName of ApiResponseInterface
     *
     * @param class-string<ClassName> $responseClass
     *
     * @return ClassName
     * @throws ExceptionInterface If the response cannot be decoded or denormalized.
     */
    protected function deserializeResponseBody(ResponseInterface $response, string $responseClass): ApiResponseInterface
    {
        $bodyContent = (string)$response->getBody();

        return $this->serializer->deserialize(
            $bodyContent,
            $responseClass,
            JsonEncoder::FORMAT,
        );
    }

    /**
     * @throws ClientException
     */
    protected function throwClientException(Throwable $throwable): never
    {
        throw new ClientException(
            'An error occurred while sending the API request.',
            previous: $throwable,
        );
    }
}
