<?php

declare(strict_types=1);

namespace Doudenko\Api\Exception;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class ResponseException extends ClientException
{
    public readonly ResponseInterface $response;

    public function __construct(ResponseInterface $response, string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }
}
