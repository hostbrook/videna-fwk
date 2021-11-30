<?php

/**
 * Base Controller class
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;


abstract class Controller
{


    public function __call($name, $args)
    {

        // Determine User account type:
        User::detect();

        // Determine User language:
        Lang::detect();

        // Filter "before" - executes before action starts
        $this->before();

        // Set requested action:
        $method = 'action' . Router::$action;

        call_user_func_array([$this, $method], $args);

        // Filter "after" - executes after action is completed
        $this->after();

        // Finally send response to client:
        http_response_code(Router::$response);
    }


    abstract protected function actionIndex();

    abstract protected function actionError();

    // Filter "before" - before action starts
    abstract protected function before();

    // Filter "after" - after action is completed
    abstract protected function after();
}
