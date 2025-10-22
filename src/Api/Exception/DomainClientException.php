<?php

declare(strict_types=1);

namespace Doudenko\Api\Exception;

use DomainException;

final class DomainClientException extends DomainException implements ClientExceptionInterface
{
}
