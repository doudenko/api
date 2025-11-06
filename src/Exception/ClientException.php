<?php

declare(strict_types=1);

namespace Doudenko\Api\Exception;

use RuntimeException;

class ClientException extends RuntimeException implements ClientExceptionInterface
{
}
