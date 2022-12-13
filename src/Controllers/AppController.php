<?php

/**
 * Pre-cooked Web Application requests controller
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
        Lang::detect();

        // CSRF Protection 
        if (Router::$action != 'Error' && !Csrf::valid()) {
            Router::$action = 'Error';
            Router::$statusCode = 403;
            if (!APP_DEBUG) Log::warning('CSRF token doesn\'t exist or outdated.');
            return;
        }
        
        // Prepare response:
        View::set([
            'statusCode' => Router::$statusCode,
            'response' => Lang::get('title response ' . Router::$statusCode)
        ]);
        
    }


    /**
     * Action for output of error message
     * This methot is triggered if:
     * - redirection from action if error needs to be shown
     * 
     * @param int $errNr statusCode number
     * @return void
     */
    public function actionError($errNr = false)
    {

        if ($errNr) Router::$statusCode = $errNr;

        $error = 'title response ' . Router::$statusCode;
        $error = Lang::get($error) != null ? Lang::get($error) : 'Error';

        View::set([
            'statusCode' => Router::$statusCode,
            'response' => Lang::get('title response ' . Router::$statusCode)
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
