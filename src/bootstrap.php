<?php

/**
 * Application bootstrap
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;

// 1. Connect the framework configuration file
$path = PATH_FWK . 'configs/fwk.config.php';
is_file($path) ? require_once $path : die('FATAL ERROR: Can\'t find framework config file');

// 2. Connect the application environment file
$path = 'App/configs/env.php';
is_file($path) ? require_once $path : die('FATAL ERROR: Can\'t find application environment config file');

// 3. Composer AutoLoad
require_once 'vendor/autoload.php';

// 4. Catch errors and put them in log file:
set_error_handler('Videna\Core\Error::errorHandler');
set_exception_handler('Videna\Core\Error::exceptionHandler');

// 5. Execute Application
$app = new App();
$app->execute(isset($argv) ? $argv : false);

// END Bootstrap