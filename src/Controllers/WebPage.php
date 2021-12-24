<?php

/**
 * Pre-cooked Static Page controller
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Controllers;

use \Videna\Core\Log;
use \Videna\Core\User;
use \Videna\Core\Router;
use \Videna\Core\Route;
use \Videna\Core\Config;
use \Videna\Core\View;
use \Videna\Core\Lang;


/**
 * Class to maintain Static Page requests  
 */
class WebPage extends \Videna\Core\Controller
{

    /**
     * Index action is a default action
     */
    public function actionIndex()
    {
    }


    /**
     * Action to show the error page
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

        View::$show = Config::get('error view');

        // Check if Error view file exists.
        if (!is_file(PATH_VIEWS . View::$show)) {

            Log::add([
                'FATAL Error: The Error page not found.',
                'Requested URI' . htmlspecialchars(URL_ABS . $_SERVER['REQUEST_URI']),
                'FATAL Error: The Error page not found.'
            ]);
        }
    }


    /**
     * Filter "before" each action
     * @return void
     */
    protected function before()
    {
    }


    /**
     * Filter "after" each action
     * @return void
     */
    protected function after()
    {

        // Check if view file exists. If not -show 404 page.
        if (!is_file(PATH_VIEWS . View::$show)) $this->actionError(404);

        View::set([
            'user' => (object)User::getAll(),
            '_' => Lang::getAll(),
            'view' => (object)[
                'title' => $this->getMeta('title'),
                'description' => $this->getMeta('description'),
                'lang' => Lang::$code
            ],
            'route' => (object)['name' => Route::$name],
            'config' => Config::getAll()
        ]);

        \Videna\Core\View::render();
    }


    /**
     * Redirect to specific url
     * 
     * @param string $redirect_to A redirection URL 
     * @param int $status_code A redirection status code
     * @return void
     */
    protected function actionRedirect($redirect_to = '/', $status_code = 302)
    {
        if (Router::get('redirect to') != null) $redirect_to = Router::get('redirect to');
        if (Router::get('status code') != null) $status_code = Router::get('status code');

        switch ($status_code) {
            case 301:
                header("HTTP/1.1 301 Moved Permanently");
                break;
            case 302:
                header("HTTP/1.1 302 Found");
                break;
            case 303:
                header("HTTP/1.1 303 See Other");
                break;
            case 304:
                header("HTTP/1.1 304 Not Modified");
                break;
            case 307:
                header("HTTP/1.1 307 Temporary Redirect");
                break;
            case 308:
                header("HTTP/1.1 308 Permanent Redirect");
                break;
            default:
                header("HTTP/1.1 302 Found");
        }

        header("Location: $redirect_to");
    }


    /**
     * Get title and description for meta tags from language file
     * @param string $meta HTML meta teg type
     * @return void
     */
    protected function getMeta($meta)
    {

        if (View::$show == 'error.php') {
            $key = $meta . ' response ' . Router::$response;
            return Lang::get($key) != null ? Lang::get($key) : 'Unknown';
        }

        $key = $meta . ' ' . View::$show;
        if (Lang::get($key) == null) {
            $key = $meta . ' default';
            return Lang::get($key) != null ? Lang::get($key) : '';
        }

        return Lang::get($key);
    }
}
