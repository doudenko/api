<?php

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\DomainClientException;
use Doudenko\Api\Exception\RequestException;
use Doudenko\Api\Request\ApiRequestInterface;
use Doudenko\Api\Response\ApiResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * @template ResponseType of ApiResponseInterface
 */
interface ApiClientInterface
{
    /**
     * @param class-string<ResponseType> $responseType
     *
     * @return ResponseType
     * @throws DomainClientException
     * @throws RequestException
     */
    public function send(ApiRequestInterface $request, string $responseType): ApiResponseInterface;

    /**
     * @param class-string<ResponseType> $responseType
     *
     * @throws DomainClientException
     * @throws RequestException
     */
    public function sendAsync(ApiRequestInterface $request, string $responseType): PromiseInterface;
}
