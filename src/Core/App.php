<?php

/**
 * Application Controller class
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;


class App
{   
    /**
     * Request type (HTTP, JSON API or Cron job)
     * Contains 'Request types' constants from framework config 
     */
    public static $requestType;


    /**
     * Application constructor. Initialization of application.
     * @param array $argv Array of arguments from command line (|| false)
     */
    public function __construct($argv)
    {
        Config::init();
        Router::init();

        self::$requestType = $this->detectRequestType($argv);

        if (self::$requestType == RQST_CRON) Router::$argv = $argv;
        
        if (self::$requestType != RQST_CRON && self::$requestType != RQST_API) Csrf::init();
    }


    /**
     * Execute application.
     * @return void
     */
    public function execute()
    {
        // Defining route parameters
        if (self::$requestType == RQST_CRON) {
            Router::parseCron();
        }
        else Router::parse();

        // Get a controller from router parameters
        $controller = Router::$controller;

        if (class_exists($controller)) {

            $controllerObject = new $controller();
            // Get a action from router parameters
            $action = Router::$action;

            if (method_exists($controllerObject, 'action' . $action)) {
                // Method/Action exist: proceed request
                $controllerObject->$action();
            } else {
                // Method/Action doesn't exist: show error
                Log::fatal([
                    "FATAL Error: Method  '$action' not found in the controller '$controller'.",
                    'URL: ' . htmlspecialchars(URL_ABS . $_SERVER['REQUEST_URI'])
                ]);
            }
        } else {
            // Class/Controller doesn't exist: show error
            Log::fatal([
                "FATAL Error: Controller '$controller' not found.",
                'URL: ' . htmlspecialchars(URL_ABS . $_SERVER['REQUEST_URI'])
            ]);
        }
    }


    /**
     * Trying to detect request type. By default request type is Web HTTP.
     * @param array $argv Array of arguments from command line (|| false)
     * @return string Request type
     */
    private function detectRequestType($argv)
    {
        if ($argv) return RQST_CRON;    

        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType == "application/json") {
            if (isset($_COOKIE['csrf_token'])) return RQST_APP;
            return RQST_API;
        }

        if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) return RQST_APP;

        return RQST_HTTP;
    }


    /**
     * Detect default controller from app config based on request type
     * @return string Default controller
     */
    public static function getDefaultController()
    {
        switch (self::$requestType) {
            case RQST_HTTP:                   
                return Config::get('default controller');
                break;
            case RQST_API:
                return Config::get('default api controller');
                break;
            case RQST_APP:
                return Config::get('default app controller');
                break;
            default: return Config::get('default controller');
        }
    }

}
