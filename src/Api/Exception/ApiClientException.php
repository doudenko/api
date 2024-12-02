<?php

declare(strict_types=1);

namespace Doudenko\Api\Exception;

use Doudenko\Api\Request\ApiRequestInterface;
use Psr\Http\Client\ClientExceptionInterface;

class ApiClientException extends ApiException
{
    public readonly ApiRequestInterface $request;

    public function __construct(ApiRequestInterface $request, string $message, ClientExceptionInterface $previous)
    {
        parent::__construct($message, 0, $previous);

        $this->request = $request;
    }
}
