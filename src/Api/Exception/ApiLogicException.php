<?php

declare(strict_types=1);

namespace Doudenko\Api\Exception;

use LogicException;

class ApiLogicException extends LogicException implements ApiExceptionInterface
{
}
