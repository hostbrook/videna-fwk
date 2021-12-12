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

    public function __construct()
    {
        Config::init();
        Router::init();
    }


    public function execute($argv = false)
    {

        // DEFINING ROUTE PARAMETERS

        if ($argv === false) {
            // HTTP requests.
            Router::parse();
        } else {
            // Cron Job request.
            Router::$argv = $argv;
            Router::$controller = $argv[1];
            Router::$action = 'Index';
        }


        // EXECUTE ACTION AT THE CONTROLLER

        if (Router::$controller == null) {
            // Controller can be null only in case if route is view-type
            $controller = 'Videna\\Controllers\\WebPage';
        } else $controller = 'App\\Controllers\\' . Router::$controller;

        if (class_exists($controller)) {

            $controllerObject = new $controller();
            $action = Router::$action;

            if (method_exists($controllerObject, 'action' . $action)) {

                $controllerObject->$action();
            } else {
                // Method/Action doesn't exist

                Log::add([
                    "Error: Method  '$action' not found in the controller '$controller'.",
                    'URL: ' . htmlspecialchars(URL_ABS . $_SERVER['REQUEST_URI'])
                ]);

                if ($argv === false) $this->showErrorPage();
            }
        } else {
            // Class/Controller doesn't exist

            Log::add([
                "Error: Controller '$controller' not found.",
                'URL: ' . htmlspecialchars(URL_ABS . $_SERVER['REQUEST_URI'])
            ]);

            if ($argv === false) $this->showErrorPage();
        }
    }


    /**
     *  Redirect to Error Action if the requested controller (or action) does not exist.
     *  If the Error Controller (or action) does not exist - stop application with a fatal error.
     */
    private function showErrorPage()
    {

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            // Set base controller for AJAX request to return the error:
            $controller = 'Videna\\Controllers\\AjaxHandler';
        } // Set base controller for http request to show error: 
        else $controller = 'Videna\\Controllers\\WebPage';

        $controllerObject = new $controller();
        $controllerObject->Error(404);
    }
}
