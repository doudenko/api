<?php

namespace Doudenko\Api\Client;

use Doudenko\Api\Request\ApiRequestInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

interface HttpRequestFactoryInterface
{
    /**
     * @throws ExceptionInterface If an error occurred while processing the request.
     */
    public function create(ApiRequestInterface $request): RequestInterface;
}
