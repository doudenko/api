<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

enum HttpMethod: string
{
    private const array EMPTY_BODY_METHODS = [
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

    public function hasEmptyBody(): bool
    {
        return in_array($this, self::EMPTY_BODY_METHODS, true);
    }
}
