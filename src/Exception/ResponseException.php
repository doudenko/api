<?php

declare(strict_types=1);

namespace Doudenko\Api\Exception;

use Doudenko\Api\Request\ApiRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ResponseException extends RequestException
{
    public function __construct(
        public readonly ResponseInterface $response,
        ApiRequestInterface $request,
        string $message,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($request, $message, $code, $previous);
    }
}
