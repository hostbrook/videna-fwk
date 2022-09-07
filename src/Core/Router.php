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
            Log::fatal("FATAL ERROR: Can\'t find application routes file.");
        } else require_once $path;
    }


    /**
     * Parsing the Cron job parameters
     * @return void
     */
    public static function parseCron()
    {
        // the first parameter is controller and action
        list(self::$controller, self::$action) = Route::getControllerAction(self::$argv[1], 'Crone job: ' . self::$argv[1]);
        self::$controller = 'App\\Controllers\\' . self::$controller;
    }


    /**
     * Parsing the requested URI.
     * @return void
     */
    public static function parse()
    {

        // Check SEF URL 

        if (isset($_GET['url'])) {
            $url = strtolower($_GET['url']);
        } else $url = '/';

        // Checking each registered route and trying to find the match
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
            // if match has been found
            
            if ($route['controller'] == null) {
                // if ether view or redirect - set default controller:
                self::$controller = App::getDefaultController();
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

            self::$controller = App::getDefaultController();
            self::$action =  'Error';
            self::$response = 404;

            return;
        }


        $checkCSRF = false;

        // Check other GET parameters (after "?")

        if (!empty($_GET)) {

            // set $argv[0] = false - flag for cron job via HTTP
            self::$argv[0] = false;

            foreach ($_GET as $key => $value) {

                if ($key == 'url') continue;

                if (self::injectionExists($key) or self::injectionExists($value)) {

                    self::$action = 'Error';
                    self::$response = 400;

                    Log::warning([
                        'Injection Warning: Checking GET[] parameters in router',
                        'Requested URI: ' . htmlspecialchars(URL_ABS . $_SERVER['REQUEST_URI'])
                    ]);

                    return;
                }

                self::set([$key => $value]);
                self::$argv[] = $value;
                $checkCSRF = true;
            }
        }


        // Check POST-parameters

        if (!empty($_POST)) {

            foreach ($_POST as $key => $value) {

                if (self::injectionExists($key) or self::injectionExists($value)) {

                    self::$action = 'Error';
                    self::$response = 403;

                    Log::warning([
                        'Injection Warning: Checking POST[] parameters in router',
                        'Requested URI: ' . htmlspecialchars(URL_ABS . $_SERVER['REQUEST_URI'])
                    ]);

                    return;
                }

                self::set([$key => $value]);
                $checkCSRF = true;
            }
        }


        // Check JSON parameters sent by FETCH

        if (App::$requestType == RQST_API) {

            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);

            // Send error to Fetch API, if JSON is broken
            if(! is_array($decoded)) {

                self::$controller = Config::get('default api controller');
                self::$action = 'Error';
                self::$response = 403;

                Log::warning([
                    'Received JSON is improperly formatted',
                    'FETCH content: ' . htmlspecialchars($content)
                ]);                
            }

            foreach ($decoded as $key => $value) {

                if (self::injectionExists($key) or self::injectionExists($value)) {

                    self::$controller = Config::get('default api controller');
                    self::$action = 'Error';
                    self::$response = 403;

                    Log::warning([
                        'Injection Warning: Checking FETCH parameters in router',
                        'Requested URI: key=' . htmlspecialchars($key) . ' value=' . htmlspecialchars($value)
                    ]);

                    return;
                }

                self::set([$key => $value]);
                $checkCSRF = true;
            }

        }
        

        // CSRF Protection 

        if ($checkCSRF) {
            if (self::get('csrf_token') == null || !csrf::valid(self::get('csrf_token'))) {

                self::$controller = Config::get('default api controller');
                self::$action = 'Error';
                self::$response = 403;

                Log::warning([
                    'CSRF token doesn\'t exist or outdated.',
                    'Requested URI: ' . htmlspecialchars(URL_ABS . $_SERVER['REQUEST_URI'])
                ]);

                return;
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

        // Check for injecttions strings only
        if (!is_string($param)) return false;

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
