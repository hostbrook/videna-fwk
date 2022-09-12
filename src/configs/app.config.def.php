<?php

/**
 * Default Application's config file
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */


// Default Application constants

define('URL_ABS', HTP_PROTOCOL . '://' . HOST_NAME);
define('URL_REL', '//' . HOST_NAME);


return array(

    // Default Application settings
    'error view' => 'error.php',

    'default language' => 'en',
    'supported languages' => ['en'],

    'default controller' => 'Videna\\Controllers\\HttpController',
    'default api controller' => 'Videna\\Controllers\\ApiController',
    'default app controller' => 'Videna\\Controllers\\AppController',

    'user token expires' => 0, // Valid until browser closed
    'csrf token expires' => 0, // Valid until browser closed

);
