<?php

declare(strict_types=1);

use DG\BypassFinals;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Removes keyword "final" from source code
BypassFinals::enable();

// Defines the global helper functions
Mockery::globalHelpers();
