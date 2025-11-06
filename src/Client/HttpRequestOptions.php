<?php

declare(strict_types=1);

namespace Doudenko\Api\Client;

use GuzzleHttp\RequestOptions;

readonly class HttpRequestOptions
{
    public function __construct(
        public bool $allowRedirects = false,
        public int $timeout = 0,
        public int $connectionTimeout = 0,
        public null|string $debugFilePath = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            RequestOptions::ALLOW_REDIRECTS => $this->allowRedirects,
            RequestOptions::TIMEOUT => $this->timeout,
            RequestOptions::CONNECT_TIMEOUT => $this->connectionTimeout,
            RequestOptions::DEBUG => $this->debugFilePath !== null ? fopen($this->debugFilePath, 'a') : false,
        ];
    }
}
