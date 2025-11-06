<?php

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\DomainClientException;
use Doudenko\Api\Exception\RequestException;
use Doudenko\Api\Request\ApiRequestInterface;
use Doudenko\Api\Response\ApiResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;

interface ApiClientInterface
{
    /**
     * @throws DomainClientException
     * @throws RequestException
     */
    public function send(ApiRequestInterface $request): ApiResponseInterface;

    /**
     * @throws DomainClientException
     * @throws RequestException
     */
    public function sendAsync(ApiRequestInterface $request): PromiseInterface;
}
