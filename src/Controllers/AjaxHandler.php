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
use \Videna\Core\Log;


class AjaxHandler extends \Videna\Core\Controller
{

    /**
     * Default action to show view by ajax request
     * @return void
     */
    public function actionIndex()
    {
        if (!is_file(PATH_VIEWS . View::$show)) {
            Log::error('Error 404. File not found: ' . PATH_VIEWS . View::$show);
            $this->actionError(404);
        }
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

        $error = 'title response ' . Router::$response;
        $error = Lang::get($error) != null ? Lang::get($error) : 'Error';

        $description = 'description response ' . Router::$response;
        $description = Lang::get($description) != null ? Lang::get($description) : 'Unknown error is occurred';

        View::set([
            'response' => Router::$response,
            'status' => Lang::get('title response ' . Router::$response),
            'view' => "<h3>$error</h3><p>$description</p>"
        ]);

        View::$show = null;
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
    }


    /**
     * Filter "after" each action
     * @return void
     */
    protected function after()
    {
        // For quick show view via route only:
        if (View::$show != null) View::set(['view' => View::render(View::$show)]);

        View::returnJSON();
    }
}
