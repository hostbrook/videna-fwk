<?php

/**
 * Framework main config file
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

// By default application runs in debug mode.
$_ENV['APP_DEBUG'] = true;

// Version
define('FWK_VERSION', '3.5');
if (!defined('PHP_VERSION')) define('PHP_VERSION', phpversion());


// Project paths, used in PHP scripts to include files
define('PATH_VIEWS', 'App/Views/');
define('PATH_APP_ENV', 'App/configs/.env');

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
define('RQST_APP', 'Web Application request');
define('RQST_CRON', 'Cron job');
