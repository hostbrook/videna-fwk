<?php

/**
 * Pre-cooked controller handles HTTP 'POST' and 'GET' requests to provide static and dynamic pages.
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
use \Videna\Core\App;
use \Videna\Core\Lang;
use \Videna\Core\Csrf;


class HttpController extends \Videna\Core\Controller
{

    /**
     *  Default action for quick views in route list (route::view())
     */
    public function actionShowView()
    {
    }


    /**
     * Action to show the error page
     * This action is triggered if:
     * - injection warning @Router 
     * - requested Class or Method not found in App class
     * - redirection from action if error needs to be shown
     * 
     * @param int $statusCode statusCode number
     * @return void
     */
    public function actionError($statusCode = false)
    {

        if ($statusCode) Router::$statusCode = $statusCode;
        Router::$action = 'Error';

        View::setPath(Config::get('error view'));

        // Check if Error view file exists.
        if (!is_file(PATH_VIEWS . View::getPath())) {

            Log::add([
                'FATAL Error: The Error page not found.',
                'Requested URI: ' . htmlspecialchars(env('APP_URL') . env('REQUEST_URI'))                
            ], 'FATAL Error: The Error page not found.');
        }
    }


    /**
     * Filter "before" each action
     * @return void
     */
    protected function before()
    {
        // Determine User account type:
        User::detect();
        
        // Determine User language:
        App::setLang(Lang::detect());

        // CSRF Protection 
        if (Router::$action != 'Error' && Router::$method == 'POST' && !csrf::valid()) {
            Router::$action = 'Error';
            Router::$statusCode = 403;
            if (env('APP_DEBUG')) Log::warning('CSRF token doesn\'t exist or outdated.');
            return;
        }
    }


    /**
     * Filter "after" each action
     * @return void
     */
    protected function after()
    {

        // Check if view file exists. If not -show 404 page.
        if (!is_file(PATH_VIEWS . View::getPath())) $this->actionError(404);

        View::set([
            'user' => (object)User::getAll(),
            'csrf' => (object)Csrf::getAll(),
            '_' => Lang::getAll(),
            'view' => (object)[
                'title' => $this->getMeta('title'),
                'description' => $this->getMeta('description')
            ],
            'app' => (object)['lang' => App::getLang()],
            'route' => (object)['name' => Route::$name],
            'config' => Config::getAll(),
            'statusCode' => Router::$statusCode
        ]);

        View::output();
    }


    /**
     * Redirect to specific url
     * 
     * @param string $redirect_to A redirection URL 
     * @param int $statusCode A redirection status code
     * @return void
     */
    protected function actionRedirect($redirect_to = '/', $statusCode = 302)
    {
        if (Router::get('redirect to') != null) $redirect_to = Router::get('redirect to');
        if (Router::get('status code') != null) $statusCode = Router::get('status code');

        switch ($statusCode) {
            case 200:
                header("HTTP/1.1 200 OK");
                break;
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
        exit;
    }


    /**
     * Get title and description for meta tags of the language file
     * @param string $meta HTML meta teg type
     * @return void
     */
    protected function getMeta($meta)
    {

        if (View::getPath() == Config::get('error view')) {
            $key = $meta . ' response ' . Router::$statusCode;
            return Lang::get($key) != null ? Lang::get($key) : 'Unknown';
        }

        $key = $meta . ' ' . View::getPath();
        if (Lang::get($key) == null) {
            $key = $meta . ' default';
            return Lang::get($key) != null ? Lang::get($key) : '';
        }

        return Lang::get($key);
    }
}