<?php

/**
 * Framework main config file
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */


// Version
define('FWK_VERSION', '1.12');
define('PHP_VERSION', phpversion());


// Project paths, used in PHP scripts to include files
define('PATH_VIEWS', 'App/Views/');

// Logs/Debug/Error files
define('PATH_APP_LOG', 'logs/videna.log');
define('PATH_PHP_LOG', 'error_log');
define('PATH_SRV_LOG', 'logs/error.php');

// Account types 0..255
define('USR_UNREG', 0);
define('USR_REG', 100);
define('USR_ADMIN', 200);

// Token lenght for user identifications
define('TOKEN_LENGTH', 20); // 20 symbols
