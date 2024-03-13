<?php

/**
 * Default Application's config file
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */


// Default Application constants


return array(

    // Default Application settings
    'error view' => 'error.php',

    'default language' => 'en',
    'supported languages' => array(
        "en" => "English"
    ),

    'default controller' => 'Videna\\Controllers\\HttpController',
    'default api controller' => 'Videna\\Controllers\\ApiController',
    'default app controller' => 'Videna\\Controllers\\AppController',

    'user token expires' => 0, // Valid until browser closed
    'csrf token expires' => 0, // Valid until browser closed

    // Default resposes titles    
    'title 200' => '200 OK',
    'title 400' => 'Error 400: Bad Request',
    'title 401' => 'Error 401: Unauthorized',
    'title 403' => 'Error 403: Forbidden',
    'title 404' => 'Error 404: Not Found',
    'title 500' => 'Error 500: Internal Server Error',

    // Default resposes descriptions
    'description 400' => 'The request cannot be fulfilled due to bad syntax.',
    'description 401' => 'Authentication credentials were missing or incorrect.',
    'description 403' => 'The request is understood, but it has been refused or access is not allowed.',
    'description 404' => 'The URI requested is invalid or the resource requested does not exists.',
    'description 500' => 'This is a generic error-message, given when an unexpected condition was encountered and no more specific message is suitable.',

);