<?php

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\RequestException;
use Doudenko\Api\Request\ApiRequestInterface;
use Psr\Http\Message\RequestInterface;

interface HttpRequestFactoryInterface
{
    /**
     * @throws RequestException
     */
    public function createRequest(ApiRequestInterface $apiRequest): RequestInterface;
}
