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

);
