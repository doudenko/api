<?php

namespace Doudenko\Api\Request;

use Doudenko\Api\Client\HttpMethod;

interface ApiRequestInterface
{
    public HttpMethod $httpMethod {
        get;
    }

    public string $uri {
        get;
    }

    /**
     * @return array<string, mixed> | object
     */
    public function getPayload(): array | object;
}
