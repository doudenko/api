<?php

declare(strict_types=1);

namespace Doudenko\Api\Request;

abstract class PayloadContainerRequest extends AbstractApiRequest
{
    /**
     * @param array<string, mixed> | object $payload
     */
    public function __construct(
        private readonly array | object $payload,
    ) {
    }

    final public function getPayload(): array | object
    {
        return $this->payload;
    }
}
