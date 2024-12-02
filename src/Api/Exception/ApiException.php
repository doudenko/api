<?php

declare(strict_types=1);

namespace Doudenko\Api\Exception;

use RuntimeException;

class ApiException extends RuntimeException implements ApiExceptionInterface
{
}
