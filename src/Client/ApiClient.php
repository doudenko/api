<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\RequestException;
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

    final public function send(ApiRequestInterface $request): ApiResponseInterface
    {
        return $this->sendAsync($request)->wait();
    }

    final public function sendAsync(ApiRequestInterface $request): PromiseInterface
    {
        $responseType = $request->responseType;

        try {
            $httpRequest = $this->httpRequestFactory->create($request);
            $httpRequestOptions = $this->httpRequestOptions->toArray();
        } catch (ExceptionInterface $exception) {
            $this->throwRequestException($exception, $request);
        }

        return $this->httpClient
            ->sendAsync($httpRequest, $httpRequestOptions)
            ->then(fn (ResponseInterface $httpResponse) => $this->deserializeResponseBody($httpResponse, $responseType))
            ->otherwise(fn (Throwable $throwable) => $this->throwRequestException($throwable, $request))
        ;
    }

    /**
     * @template ResponseObject of ApiResponseInterface
     * @template ResponseType of string | class-string<ResponseObject>
     *
     * @param ResponseType $className
     *
     * @return ResponseObject
     * @throws ExceptionInterface If an error occurred while processing the response body.
     */
    protected function deserializeResponseBody(ResponseInterface $httpResponse, string $className): ApiResponseInterface
    {
        $bodyContent = $httpResponse->getBody()->getContents();

        return $this->serializer->deserialize(
            $bodyContent,
            $className,
            JsonEncoder::FORMAT,
        );
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
