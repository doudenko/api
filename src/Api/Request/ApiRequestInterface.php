<?php

namespace Doudenko\Api\Request;

use Doudenko\Api\Client\HttpMethod;

interface ApiRequestInterface
{
    public HttpMethod $method {
        get;
    }

    public string $uri {
        get;
    }

    public function getPayload(): mixed;
}
