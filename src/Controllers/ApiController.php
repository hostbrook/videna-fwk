<?php

/**
 * Pre-cooked API requests controller
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Controllers;

use \Videna\Core\Router;
use \Videna\Core\View;
use \Videna\Core\Config;
use \Videna\Core\App;
use \Videna\Core\Log;


class ApiController extends \Videna\Core\Controller
{

    /**
     * Filter before the each action
     * @return void
     */
    protected function before()
    {

        // Prepare response:
        $response = Config::get('title '.Router::$statusCode);
        View::set([
            'statusCode' => Router::$statusCode,
            'response' => $response
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

        $response = Config::get('title '.Router::$statusCode) != null ? Config::get('title '.Router::$statusCode) : 'Unexpected Error';

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