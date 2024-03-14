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
     * Application language code tag by IETF BCP 47
     * https://www.w3schools.com/tags/ref_language_codes.asp
     */
    private static $lang = null;


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
                    'URL: ' . htmlspecialchars(env('APP_URL') . env('REQUEST_URI'))
                ]);
            }
        } else {
            // Class/Controller doesn't exist: show error
            Log::fatal([
                "FATAL Error: Controller '$controller' not found.",
                'URL: ' . htmlspecialchars(env('APP_URL') . env('REQUEST_URI'))
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

        $contentType = env('CONTENT_TYPE') ? trim(env('CONTENT_TYPE')) : '';
        if ($contentType == "application/json") {
            if (isset($_COOKIE['csrf_token'])) return RQST_APP;
            return RQST_API;
        }

        if ((env('HTTP_X_REQUESTED_WITH')) and env('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') return RQST_APP;

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


    /**
     * Return application language code tag within IETF BCP 47
     * @return string 2-symbols language tag or null
     */
    public static function getLang()
    {
        return self::$lang;
    }


    /**
     * Set application language code tag within IETF BCP 47
     *  @return bool
     */
    public static function setLang($lang)
    {
        if (in_array($lang, array_keys(Config::get('supported languages'))) || in_array($lang, Config::get('supported languages'))) {
            self::$lang = $lang;
            return true;
        } 
        return false;
    }
}
