<?php

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\ApiExceptionInterface;
use Doudenko\Api\Request\ApiRequestInterface;
use Doudenko\Api\Response\ApiResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;

interface ApiClientInterface
{
    /**
     * @template ClassName
     *
     * @param class-string<ClassName> $responseClass
     *
     * @throws ApiExceptionInterface
     *
     * @return ClassName
     */
    public function send(ApiRequestInterface $apiRequest, string $responseClass): ApiResponseInterface;

    /**
     * @param class-string $responseClass
     */
    public function sendAsync(ApiRequestInterface $apiRequest, string $responseClass): PromiseInterface;
}
