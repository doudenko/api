<?php

declare(strict_types=1);

namespace Doudenko\Api\Request;

abstract class PayloadContainerRequest extends AbstractApiRequest
{
    public function __construct(
        private readonly mixed $payload,
    ) {
    }

    final public function getPayload(): mixed
    {
        return $this->payload;
    }
}
