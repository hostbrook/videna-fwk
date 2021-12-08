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

    public static $lang;
    public static $controller;
    public static $action;
    public static $view;
    public static $response;
    public static $argv = [];


    /**
     * Initialization of the router's variables.
     * @return void
     */
    public static function init()
    {

        self::$controller = Config::get('default controller');
        self::$action = Config::get('default action');
        self::$view = Config::get('default controller');
        self::$lang = null;
        self::$response = 200;

        define('STRICT', true);
        define('NOT_STRICT', false);
    }


    /**
     * Parsing the requested URI.
     * @return void
     */
    public static function parse()
    {


        // 1. Check SEF URL ( $_GET['url'] )

        if (isset($_GET['url'])) {

            // 1.1. Strip whitespace and slash from the end of SEF URL
            $url = rtrim($_GET['url'], ' /');
            $url = strtolower($url);

            // 1.2. Remove permitted url suffixes from the end of SEF URL
            if (($suffixes = Config::get('url suffixes')) != null) {
                array_multisort($suffixes, SORT_DESC, SORT_REGULAR);
                $url = str_replace($suffixes, '', $url);
            }

            // 1.3. Check SEF URL on eligible symbols
            if (preg_match("/[^a-z0-9\/\-_]+/", $url) or self::injectionExists($url, STRICT)) {

                self::$action =  Config::get('error action');
                self::$response = 400;

                Log::add([
                    'Injection Warning: Checking GET[\'url\'] in router',
                    'Requested URI: ' . htmlspecialchars(URL_ABS . $_SERVER['REQUEST_URI'])
                ]);

                return;
            }

            // 1.4. Explode SEF URL on components
            $url_arr = explode("/", $url);

            // 1.5. Check if the first parameter is Lang

            if (strlen($url_arr[0]) == 2) {
                self::$lang = $url_arr[0];
                unset($url_arr[0]);
            }

            if (isset($_GET['lang']) and strlen($_GET['lang']) == 2) {
                self::$lang = $_GET['lang'];
            }


            // 1.6. Get the Main Controller

            if (!empty($url_arr)) {

                $first_key = key($url_arr);
                $controller = ucwords($url_arr[$first_key], "_-");

                if (class_exists('\\App\\Controllers\\' . $controller)) {

                    self::$controller = $controller;
                    self::$view = $controller;

                    unset($url_arr[$first_key]);
                }
            }

            // 1.7. if parameters still exist, check if they are SubControllers

            while (!empty($url_arr)) {

                $first_key = key($url_arr);

                $controller = ucwords($url_arr[$first_key], "_-");
                $controller = str_replace(['_', '-'], '', $controller);

                if (class_exists('\\App\\Controllers\\' . self::$controller . '\\' . $controller)) {

                    self::$controller .= '\\' . $controller;
                    self::$view .= '/' . $url_arr[$first_key];

                    unset($url_arr[$first_key]);
                } else break;
            }


            // 1.8. if parameters still exist, check if the first is an Action at the existing controller

            if (!empty($url_arr)) {

                $first_key = key($url_arr);
                $controller =  '\\App\\Controllers\\' . self::$controller;
                $controller_object = new $controller();

                $action = ucwords($url_arr[$first_key], "_-");
                $action = str_replace(['_', '-'], '', $action);

                if (method_exists($controller_object, 'action' . $action)) {
                    self::$action = $action;
                    unset($url_arr[$first_key]);
                }
            }


            // 1.9. if parameters still exist - the rest of them put in the DataArray

            if (!empty($url_arr)) {
                $i = 1;
                foreach ($url_arr as $param) {
                    self::set([$i => $param]);
                    self::$view .= '/' . $param;
                    $i++;
                }
            } else self::$view .= '/' . Config::get('default view');
        } else self::$view .= '/' . Config::get('default view');


        // 2. Check other GET parameters (after "?")

        if (!empty($_GET)) {

            // set $argv[0] = false - flag for cron job via HTTP
            self::$argv[] = false;

            foreach ($_GET as $key => $value) {

                if ($key == 'url') continue;

                if (self::injectionExists($key, STRICT) or self::injectionExists($value, NOT_STRICT)) {

                    self::$action =  Config::get('error action');
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

                if (self::injectionExists($key, STRICT) or self::injectionExists($value, NOT_STRICT)) {

                    self::$action =  Config::get('error action');
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
     * @param boolean $strict Set 'true' if needs more strict check
     * @return boolean Returns 'true' if parameter contains incorrect symbols
     */
    protected static function injectionExists($param, $strict = true)
    {

        // strip_tags() - Remove HTML and PHP tags from a string
        $str = strip_tags($param);

        // trim() - Remove "\n\r\t\v\0" from the beginning and end of a string
        $str = trim($str, "\n\r\t\v\0");

        if ($strict) {

            // htmlspecialchars() - Convert special characters to HTML entities
            $str = htmlspecialchars($str);
        }

        // stripslashes() - Returns a string with backslashes stripped off (\' becomes ' and so on).
        // Double backslashes (\\) are made into a single backslash (\). 
        $str = stripslashes($str);

        if ($str != $param) return true;
        return false;
    }
}
