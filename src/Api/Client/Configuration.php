<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

final readonly class Configuration
{
    public function __construct(
        public string $baseUri,
    ) {
    }
}
