<?php

namespace Doudenko\Api\Request;

use Doudenko\Api\Client\HttpMethod;

interface ApiRequestInterface
{
    public function getHttpMethod(): HttpMethod;

    public function getUriPath(): string;

    public function getPayload(): mixed;
}
