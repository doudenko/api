<?php

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\DomainClientException;
use Doudenko\Api\Exception\RequestException;
use Doudenko\Api\Exception\ResponseException;
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
     * @throws DomainClientException
     * @throws RequestException
     * @throws ResponseException
     */
    public function send(ApiRequestInterface $apiRequest, string $responseClass): ApiResponseInterface;

    /**
     * @template ClassName of ApiResponseInterface
     *
     * @param class-string<ClassName> $responseClass
     *
     * @return PromiseInterface<ClassName, ResponseException>
     * @throws DomainClientException
     * @throws RequestException
     */
    public function sendAsync(ApiRequestInterface $apiRequest, string $responseClass): PromiseInterface;
}
