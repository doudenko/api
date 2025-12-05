<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\DomainClientException;
use Doudenko\Api\Exception\RequestException;
use Doudenko\Api\Request\ApiRequestInterface;
use Doudenko\Api\Response\ApiResponseInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

readonly class ApiClient implements ApiClientInterface
{
    public function __construct(
        protected HttpRequestFactoryInterface $httpRequestFactory,
        protected ClientInterface $httpClient,
        protected SerializerInterface & NormalizerInterface $converter,
        protected ApiConfiguration $configuration,
    ) {
    }

    /**
     * @template ResponseType of ApiResponseInterface
     *
     * @param class-string<ResponseType> $responseType
     *
     * @return ResponseType
     */
    final public function send(ApiRequestInterface $request, string $responseType): ApiResponseInterface
    {
        $responsePromise = $this->sendAsync($request, $responseType);

        /**
         * @var ResponseType $response
         */
        $response = $responsePromise->wait();

        return $response;
    }

    final public function sendAsync(ApiRequestInterface $request, string $responseType): PromiseInterface
    {
        if (!is_a($responseType, ApiResponseInterface::class, true)) {
            throw new DomainClientException('The specified class is not a valid API response class.');
        }

        try {
            $httpRequest = $this->httpRequestFactory->create($this->configuration, $request);
            $httpRequestOptions = $this->configuration->httpOptions->toArray();
        } catch (ExceptionInterface $exception) {
            $this->throwRequestException($exception, $request);
        }

        return $this->httpClient
            ->sendAsync($httpRequest, $httpRequestOptions)
            ->then(fn (ResponseInterface $httpResponse) => $this->deserializeResponse($httpResponse, $responseType))
            ->otherwise(fn (Throwable $throwable) => $this->throwRequestException($throwable, $request))
        ;
    }

    /**
     * @template ResponseType of ApiResponseInterface
     *
     * @param class-string<ResponseType> $responseType
     *
     * @return ResponseType
     * @throws ExceptionInterface If an error occurred while processing the response body.
     */
    protected function deserializeResponse(ResponseInterface $httpResponse, string $responseType): ApiResponseInterface
    {
        $responseBodyContent = $httpResponse->getBody()->getContents();

        /**
         * @var ResponseType $responsePayload
         */
        $responsePayload = $this->converter->deserialize(
            $responseBodyContent,
            $responseType,
            JsonEncoder::FORMAT,
        );

        return $responsePayload;
    }

    protected function throwRequestException(Throwable $throwable, ApiRequestInterface $request): never
    {
        throw new RequestException(
            $request,
            message: 'An error occurred while processing the request.',
            previous: $throwable,
        );
    }
}
