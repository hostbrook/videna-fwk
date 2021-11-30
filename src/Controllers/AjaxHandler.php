<?php

/**
 * Pre-cooked Ajax requests controller
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Controllers;

use \Videna\Core\Router;
use \Videna\Core\View;
use \Videna\Core\Lang;
use \Videna\Core\User;


class AjaxHandler extends \Videna\Core\Controller
{

    /**
     * Default action, executes if action was missed in ajax request
     * @return void
     */
    public function actionIndex()
    {

        View::set([
            'response' => 404,
            'status' => View::get('title response 404'),

            'text' => 'Action/Method not found in class \'' . Router::$controller . '\'',
            'html' => '<p>Action/Method not found in class \'' . Router::$controller . '\'</p>'
        ]);
    }


    /**
     * Action for output of error message
     * This methot is triggered if:
     * - injection warning @Router 
     * - Requested Class or Method not found in App class
     * - redirection from action if error needs to be shown
     * 
     * @param int $errNr Response number
     * @return void
     */
    public function actionError($errNr = false)
    {

        if ($errNr) Router::$response = $errNr;

        View::set([
            'response' => Router::$response,
            'status' => View::get('title response ' . Router::$response),
        ]);

        $description = 'description response ' . Router::$response;
        View::set([
            'text' => Lang::get($description) != null ? Lang::get($description) : 'Unknown error is occurred.',
            'html' => '<p>' . Lang::get($description) != null ? Lang::get($description) : 'Unknown error is occurred.' . '</p>'
        ]);
    }


    /**
     * Filter before the each action
     * @return void
     */
    protected function before()
    {

        if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            http_response_code(403);
            die("Access denied.");
        }

        // Prepare response:
        View::set([
            'response' => Router::$response,
            'status' => Lang::get('title response ' . Router::$response)
        ]);

        Router::$view = false;
    }


    /**
     * Filter "after" each action
     * @return void
     */
    protected function after()
    {

        View::set([
            'user' => User::getAll(),
            'lang' => Lang::$code
        ]);

        if (Router::$view) View::set(['_' => Lang::getAll()]);

        \Videna\Core\View::jsonRender();
    }
}
