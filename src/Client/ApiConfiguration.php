<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

class ApiConfiguration
{
    public function __construct(
        private(set) string $baseUri {
            set => str_ends_with($value, '/')
                ? rtrim($value, '/')
                : $value;
        },
        public readonly HttpOptions $httpOptions = new HttpOptions(),
    ) {
    }
}
