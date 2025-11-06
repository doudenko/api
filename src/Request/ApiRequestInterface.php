<?php

namespace Doudenko\Api\Request;

use Doudenko\Api\Client\HttpMethod;

interface ApiRequestInterface
{
    public string $responseType {
        get;
    }

    public HttpMethod $httpMethod {
        get;
    }

    public string $uri {
        get;
    }

    public function getPayload(): mixed;
}
