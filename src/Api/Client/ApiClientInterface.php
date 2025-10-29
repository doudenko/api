<?php

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\ClientException;
use Doudenko\Api\Exception\DomainClientException;
use Doudenko\Api\Request\ApiRequestInterface;
use Doudenko\Api\Response\ApiResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;

interface ApiClientInterface
{
    /**
     * @template ClassName of ApiResponseInterface
     *
     * @param class-string<ClassName> $responseClass
     *
     * @return ClassName
     * @throws DomainClientException If the specified class is not a valid API response class.
     * @throws ClientException
     */
    public function send(ApiRequestInterface $apiRequest, string $responseClass): ApiResponseInterface;

    /**
     * @template ClassName of ApiResponseInterface
     *
     * @param class-string<ClassName> $responseClass
     *
     * @return PromiseInterface<ClassName, ClientException>
     * @throws DomainClientException If the specified class is not a valid API response class.
     * @throws ClientException
     */
    public function sendAsync(ApiRequestInterface $apiRequest, string $responseClass): PromiseInterface;
}
