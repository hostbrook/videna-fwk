<?php

/**
 * Framework main config file
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */


// Version
define('FWK_VERSION', '2.1');
if (!defined('PHP_VERSION')) define('PHP_VERSION', phpversion());


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

// Describes log levels
define('EMERGENCY', 'EMERGENCY');
define('CRITICAL', 'CRITICAL');
define('ERROR', 'ERROR');
define('FATAL', 'FATAL');
define('WARNING', 'WARNING');
define('ALERT', 'ALERT');
define('NOTICE', 'NOTICE');
define('INFO', 'INFO');
define('DEBUG', 'DEBUG');

// Request types
define('RQST_HTTP', 'WEB HTTP request');
define('RQST_API', 'API JSON request');
define('RQST_AJAX', 'API AJAX request');
define('RQST_CRON', 'Cron job');
