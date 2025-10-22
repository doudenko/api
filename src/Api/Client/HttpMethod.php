<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

enum HttpMethod: string
{
    case Get = 'GET';
    case Post = 'POST';
    case Put = 'PUT';
    case Delete = 'DELETE';
    case Patch = 'PATCH';
    case Options = 'OPTIONS';
    case Head = 'HEAD';

    public function hasBody(): bool
    {
        return !in_array($this, [self::Get, self::Head], true);
    }
}
