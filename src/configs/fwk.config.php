<?php
// Videna Framework
// File: /Videna/fwk.config.php
// Desc: Framework main config file


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

// END fwk.config