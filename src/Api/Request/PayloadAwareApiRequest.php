<?php

declare(strict_types=1);

namespace Doudenko\Api\Request;

abstract class PayloadAwareApiRequest implements ApiRequestInterface
{
    public readonly mixed $payload;

    public function __construct(mixed $payload)
    {
        $this->payload = $payload;
    }

    final public function getPayload(): mixed
    {
        return $this->payload;
    }
}
