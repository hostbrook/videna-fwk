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

    use DataArray;


    public static $controller = null;
    public static $action = 'Index';
    public static $lang = null;

    public static $response = 200;
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
     * @return void
     */
    public static function parse()
    {

        // 1. Check SEF URL 

        if (isset($_GET['url'])) {
            $url = strtolower($_GET['url']);
        } else $url = '/';

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

        if ($matches) {

            self::$controller = $route['controller'];
            self::$action = $route['action'];
            View::$show = $route['view'];

            if (isset($matches['lang'])) self::$lang = $matches['lang'];
            if (isset($matches['name'])) Route::$name = $matches['name'];

            self::set($matches);

            if (isset($route['redirect to'])) {
                self::set([
                    'redirect to' => $route['redirect to'],
                    'status code' => $route['status code'],
                ]);
                return;
            }
        } else {

            self::$action =  'Error';
            self::$response = 404;

            return;
        }


        // 2. Check other GET parameters (after "?")

        if (!empty($_GET)) {

            // set $argv[0] = false - flag for cron job via HTTP
            self::$argv[] = false;

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


        // 3. Check POST-parameters

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
}
