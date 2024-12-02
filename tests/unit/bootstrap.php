<?php
/**
 * Tests entry point.
 */
declare(strict_types=1);

use DG\BypassFinals;

defined('ROOT_PATH') || define('ROOT_PATH', dirname(__DIR__, 2));

/**
 * Include Composer autoloader.
 */
require_once ROOT_PATH . '/vendor/autoload.php';

// Removes keyword "final" from source code
BypassFinals::enable();

// Defines the global helper functions
Mockery::globalHelpers();
