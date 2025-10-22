<?php

declare(strict_types=1);

namespace Doudenko\Api\Request;

abstract class SelfPayloadApiRequest extends AbstractApiRequest
{
    final public function getPayload(): self
    {
        return $this;
    }
}
