<?php

/**
 * List of routes
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;


class Route
{

    public static $routes = [];
    private static $currentRoute;


    public static function add($route, $callback)
    {

        self::$currentRoute = $route;

        $actionData = explode('@', $callback);
        $controller = $actionData[0] == null ? Config::get('default controller') : $actionData[0];
        $action = $actionData[1] == null ? Config::get('default action') : $actionData[1];
        $view = str_replace(['\\', '@'], '/', $callback);

        self::$routes[strtolower($route)] = [
            'route' => strtolower($route),
            'controller' => $controller,
            'action' => $action,
            'view' => $view
        ];

        return new static();
    }


    public static function view($route, $view)
    {
        self::$currentRoute = $route;

        self::$routes[strtolower($route)] = [
            'route' => strtolower($route),
            'controller' => Config::get('default controller'),
            'action' => Config::get('default action'),
            'view' => $view
        ];

        return new static();
    }


    public static function where($conditions)
    {
        self::$routes[self::$currentRoute]['conditions'] = $conditions;
        return new static();
    }

    public function name($name)
    {
        self::$routes[self::$currentRoute]['name'] = $name;
        return new static();
    }
}
