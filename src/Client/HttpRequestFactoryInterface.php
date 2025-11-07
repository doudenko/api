<?php

namespace Doudenko\Api\Client;

use Doudenko\Api\Exception\ClientException;
use Doudenko\Api\Request\ApiRequestInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

interface HttpRequestFactoryInterface
{
    /**
     * @throws ClientException If the query parameters are invalid.
     * @throws ExceptionInterface If an error occurred while processing the request.
     */
    public function create(ApiConfiguration $configuration, ApiRequestInterface $request): RequestInterface;
}
