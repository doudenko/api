<?php

declare(strict_types=1);

namespace Doudenko\Api\Request;

abstract class PayloadItselfApiRequest implements ApiRequestInterface
{
    final public function getPayload(): self
    {
        return $this;
    }
}
