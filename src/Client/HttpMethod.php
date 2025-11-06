<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

enum HttpMethod: string
{
    private const array WITHOUT_BODY_METHODS = [
        self::Get,
        self::Head,
    ];

    case Get = 'GET';
    case Post = 'POST';
    case Put = 'PUT';
    case Delete = 'DELETE';
    case Patch = 'PATCH';
    case Options = 'OPTIONS';
    case Head = 'HEAD';

    public function hasBody(): bool
    {
        return !in_array($this, self::WITHOUT_BODY_METHODS, true);
    }
}
