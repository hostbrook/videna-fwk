<?php

/**
 * Application bootstrap
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;

// Connect the framework configuration file
$path = PATH_FWK . 'configs/fwk.config.php';
is_file($path) ? require_once $path : die('FATAL ERROR: Can\'t find framework config file');

// Composer AutoLoad
require_once 'vendor/autoload.php';

// Load global Helpers functions
require_once 'vendor/hostbrook/videna-fwk/src/Helpers/helpers.php';

// Catch errors and put them in log file:
set_error_handler('Videna\Core\Error::errorHandler');
set_exception_handler('Videna\Core\Error::exceptionHandler');

// Execute Application
$app = new App(isset($argv) ? $argv : false);
$app->execute();

// END Bootstrap