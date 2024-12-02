<?php

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\ApiExceptionInterface;
use Doudenko\Api\Request\ApiRequestInterface;
use Psr\Http\Message\RequestInterface;

interface HttpRequestFactoryInterface
{
    /**
     * @throws ApiExceptionInterface
     */
    public function create(ApiRequestInterface $apiRequest): RequestInterface;
}
