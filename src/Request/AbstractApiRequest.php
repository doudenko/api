<?php

declare(strict_types=1);

namespace Doudenko\Api\Request;

use Doudenko\Api\Exception\DomainClientException;
use Doudenko\Api\Response\ApiResponseInterface;

abstract class AbstractApiRequest implements ApiRequestInterface
{
    public string $responseType {
        final get {
            if (!is_a($this->responseType, ApiResponseInterface::class, true)) {
                throw new DomainClientException('The specified class is not a valid API response class.');
            }

            return $this->responseType;
        }
    }
}
