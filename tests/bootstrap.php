<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use DG\BypassFinals;

/**
 * @var ClassLoader $classAutoloader
 */
$classAutoloader = require_once dirname(__DIR__) . '/vendor/autoload.php';

// Removes keyword "final" from source code
BypassFinals::enable();

// Defines the global helper functions
Mockery::globalHelpers();
