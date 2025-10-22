<?php

declare(strict_types=1);

namespace Doudenko\Api\Tests;

use DG\BypassFinals;
use Mockery;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Removes keyword "final" from source code
BypassFinals::enable();

// Defines the global helper functions
Mockery::globalHelpers();
