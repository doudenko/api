<?php

declare(strict_types=1);

namespace Doudenko\Api\Exception;

use Doudenko\Api\Request\ApiRequestInterface;
use Throwable;

class RequestException extends ClientException
{
    public readonly ApiRequestInterface $apiRequest;

    public function __construct(ApiRequestInterface $apiRequest, string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->apiRequest = $apiRequest;
    }
}
