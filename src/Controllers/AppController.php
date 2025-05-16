<?php

/**
 * Pre-cooked controller handles requests to provide RESTful services.
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Controllers;

use \Videna\Core\Router;
use \Videna\Core\User;
use \Videna\Core\View;
use \Videna\Core\Lang;
use \Videna\Core\App;
use \Videna\Core\Log;
use \Videna\Core\Csrf;


class AppController extends \Videna\Core\Controller
{
    /**
     * Filter before the each action
     * @return void
     */
    protected function before()
    {
        // Determine User account type:
        User::detect();

        // Determine User language:
        App::setLang(Lang::detect());

        // CSRF Protection 
        if (Router::$action != 'Error' && Router::$method != 'GET' && !Csrf::valid()) {
            Router::$action = 'Error';
            Router::$statusCode = 403;
            if (env('APP_DEBUG')) Log::warning('CSRF token doesn\'t exist or outdated.');
            return;
        }
        
        // Prepare response:
        $response = Lang::get('title response ' . Router::$statusCode);        
        View::set([
            'statusCode' => Router::$statusCode,
            'response' => $response
        ]);
        
    }


    /**
     * Action for output the error message
     * This method is triggered if:
     * - redirection from any custom action when error needs to be shown
     * 
     * @param int $errNr statusCode number
     * @return void
     */
    public function actionError($errNr = false)
    {

        if ($errNr) Router::$statusCode = $errNr;
        Router::$action = 'Error';

        $error = 'title response ' . Router::$statusCode;
        $response = Lang::get($error) != null ? Lang::get($error) : 'Unexpected Error';

        View::set([
            'statusCode' => Router::$statusCode,
            'response' => $response
        ]);
    }


    /**
     * Filter "after" each action
     * @return void
     */
    protected function after()
    {
        View::returnJSON();
    }
}