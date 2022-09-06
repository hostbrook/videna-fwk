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
        Crsf::init();
    }


    public function execute($argv = false)
    {

        // DEFINING ROUTE PARAMETERS
        Router::parse($argv);

        $controller = Router::$controller;

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
        $controller = Config::get('default controller');
        $controllerObject = new $controller();
        $controllerObject->Error(404);
    }
}
