<?php

declare(strict_types=1);

namespace Doudenko\Api\Exception;

use Doudenko\Api\Request\ApiRequestInterface;
use Throwable;

class RequestException extends ClientException
{
    public function __construct(
        public readonly ApiRequestInterface $request,
        string $message,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
