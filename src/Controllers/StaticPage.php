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
use \Videna\Core\Config;
use \Videna\Core\View;
use \Videna\Core\Lang;


/**
 * Class to maintain Static Page requests  
 */
class StaticPage extends \Videna\Core\Controller
{

    protected $user;   // <- Array of user data


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
     * @param int $errNr Response number
     * @return void
     */
    public function actionError($errNr = false)
    {

        if ($errNr) {
            Router::$action =  Config::get('error action');
            Router::$response = $errNr;
        }

        Router::$view = Config::get('default controller') . '/' . Config::get('error view');

        // Check if Error view file exists.
        if (!is_file(PATH_VIEWS . Router::$view  . '.php')) {

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

        // Determine User's account type:
        $this->user = User::detect();

        // Check if user have preffered language:
        if ($this->user['account'] > USR_UNREG and isset($this->user['lang'])) {
            $userLang = $this->user['lang'];
        } else $userLang = false;

        // Set language:
        $lang = new Lang($userLang);
        View::set([
            '_' => $lang->langArray,
            'lang' => $lang->getCode()
        ]);
    }


    /**
     * Filter "after" each action
     * @return void
     */
    protected function after()
    {

        // Check if view file exists. If not -show 404 page.
        if (!is_file(PATH_VIEWS . Router::$view  . '.php')) $this->actionError(404);

        View::set([
            'user' => $this->user,
            'title' => $this->getMeta('title'),
            'description' => $this->getMeta('description'),
        ]);

        \Videna\Core\View::render();
    }


    /**
     * Redirect to specific url
     * @param string $url
     * @return void
     */
    protected function redirect($url)
    {
        header("HTTP/1.1 302 Found");
        header("Location: $url");
    }


    /**
     * Get title (for the <title> tag) from language file
     * @param string $meta HTML meta teg type
     * @return void
     */
    protected function getMeta($meta)
    {

        if (Router::$action == 'error') {
            $key = $meta . ' response ' . Router::$response;
            return isset(View::get('_')[$key]) ? View::get('_')[$key] : 'Unknown';
        }

        $key = $meta . ' ' . Router::$view;
        if (!isset(View::get('_')[$key])) {
            $key = $meta . ' ' . Config::get('default controller') . '/' . Config::get('default view');
            return isset(View::get('_')[$key]) ? View::get('_')[$key] : '';
        }

        return View::get('_')[$key];
    }
}
