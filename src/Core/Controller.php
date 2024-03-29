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
        // Filter "before" - executes before action starts
        $this->before();

        // Set requested action:
        $method = 'action' . Router::$action;

        call_user_func_array([$this, $method], $args);

        // Filter "after" - executes after action is completed
        $this->after();
    }

    // Default error action
    abstract protected function actionError();

    // Filter "before" - before action starts
    abstract protected function before();

    // Filter "after" - after action is completed
    abstract protected function after();
}
