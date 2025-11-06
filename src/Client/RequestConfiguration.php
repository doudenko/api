<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

readonly class RequestConfiguration
{
    public function __construct(
        public string $baseUri,
    ) {
    }
}
