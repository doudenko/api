<?php

declare(strict_types=1);

namespace Doudenko\Api\Request;

use Doudenko\Api\Client\HttpMethod;

abstract class AbstractApiRequest implements ApiRequestInterface
{
    abstract public HttpMethod $method {
        get;
    }

    abstract public string $uri {
        get;
    }
}
