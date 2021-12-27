<?php

/**
 * Parcing the request to get: Controller, Action and all parameters
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;


class Router
{

    /**
     * Use use setter/getter to get access to POST and GET parameters
     */
    use DataArray;

    /**
     * Current controller name (with namespace)
     * @var string $controller
     */
    public static $controller;

    /**
     * Current action name
     */
    public static $action;

    /**
     * Locale (if exist in route)
     */
    public static $lang = null;

    /**
     * HTTP response
     */
    public static $response = 200;

    /**
     * Arguments if cron job request
     */
    public static $argv = [];


    /**
     * Initialization of the router's variables.
     * @return void
     */
    public static function init()
    {
        // Add all routes in data array of class Route:
        $path = 'App/configs/routes.php';

        if (!is_file($path)) {
            $errorDescription = "FATAL ERROR: Can\'t find application routes file.";
            Log::add($errorDescription, $errorDescription);
        } else require_once $path;
    }


    /**
     * Parsing the requested URI.
     * @param array|false An array with arguments if crone job
     * @return void
     */
    public static function parse($argv)
    {
        // Check if cron job

        if ($argv) {
            // the first parameter is controller and action
            list(self::$controller, self::$action) = Route::getControllerAction($argv[1], 'Crone job: ' . $argv[1]);
            self::$controller = 'App\\Controllers\\' . self::$controller;
            self::$argv = $argv;
            return;
        }

        // Check SEF URL 

        if (isset($_GET['url'])) {
            $url = strtolower($_GET['url']);
        } else $url = '/';

        // Check each registered route and try to find match
        foreach (Route::$routes as $route) {
            $pattern = $route['route'];

            if (isset($route['conditions'])) {
                foreach ($route['conditions'] as $param => $regex) {
                    $pattern = preg_replace("/\{($param)\}/", "(?P<$1>$regex)", $pattern);
                }
            }

            $pattern = preg_replace("/\{(.*?)\}/", "(?P<$1>[\w-]+)", $pattern);

            $pattern = "#^" . trim($pattern, '/') . "$#";

            preg_match($pattern, trim($url, '/'), $matches);

            if ($matches) break;
        }

        //print_r ($matches);
       // die;

        if ($matches) {
            // if match has been found

            if ($route['controller'] == null) {
                // if ether view or redirect - set default controller:
                self::$controller = self::getDefaultController();
            } else self::$controller = 'App\\Controllers\\' . $route['controller'];

            self::$action = $route['action'];

            View::$show = $route['view'];

            self::$lang = isset($matches['lang']) ? $matches['lang'] : null;
            Route::$name = isset($route['name']) ? $route['name'] : null;

            self::set($matches);

            if (isset($route['redirect to'])) {
                self::set([
                    'redirect to' => $route['redirect to'],
                    'status code' => $route['status code'],
                ]);
                return;
            }
        } else {
            // if no matches found - user try to go to unregistered route

            self::$controller = self::getDefaultController();
            self::$action =  'Error';
            self::$response = 404;

            return;
        }


        // Check other GET parameters (after "?")

        if (!empty($_GET)) {

            // set $argv[0] = false - flag for cron job via HTTP
            self::$argv[0] = false;

            foreach ($_GET as $key => $value) {

                if ($key == 'url') continue;

                if (self::injectionExists($key) or self::injectionExists($value)) {

                    self::$action = 'Error';
                    self::$response = 400;

                    Log::add([
                        'Injection Warning: Checking GET[] parameters in router',
                        'Requested URI: ' . htmlspecialchars(URL_ABS . $_SERVER['REQUEST_URI'])
                    ]);

                    return;
                }

                self::set([$key => $value]);
                self::$argv[] = $value;
            }
        }


        // Check POST-parameters

        if (!empty($_POST)) {

            foreach ($_POST as $key => $value) {

                if (self::injectionExists($key) or self::injectionExists($value)) {

                    self::$action = 'Error';
                    self::$response = 403;

                    Log::add([
                        'Injection Warning: Checking POST[] parameters in router',
                        'Requested URI: ' . htmlspecialchars(URL_ABS . $_SERVER['REQUEST_URI'])
                    ]);

                    return;
                }

                self::set([$key => $value]);
            }
        }
    }


    /**
     * Check parameter for injection
     * 
     * @param string $param Parameter to check
     * @return boolean Returns 'true' if parameter contains incorrect symbols
     */
    protected static function injectionExists($param)
    {

        // strip_tags() - Remove HTML and PHP tags from a string
        $str = strip_tags($param);

        // trim() - Remove "\n\r\t\v\0" from the beginning and end of a string
        $str = trim($str, "\n\r\t\v\0");

        // stripslashes() - Returns a string with backslashes stripped off (\' becomes ' and so on).
        // Double backslashes (\\) are made into a single backslash (\). 
        $str = stripslashes($str);

        if ($str != $param) return true;
        return false;
    }


    /**
     * Check HTTP request type and returns default controller name
     * @return string Returns the default controller with namespace
     */
    public static function getDefaultController()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            // Set base controller for AJAX request to return the error:
            return 'Videna\\Controllers\\AjaxHandler';
        }

        // Set base controller for http request to show error: 
        return 'Videna\\Controllers\\WebPage';
    }
}
