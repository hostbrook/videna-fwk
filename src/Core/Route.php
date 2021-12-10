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


    public static function add($route, $requestHandler)
    {

        self::$currentRoute = strtolower($route);

        $actionData = explode('@', $requestHandler);
        if (!isset($actionData[0]) or !isset($actionData[1])) {
            $description = "FATAL Error: Incorrect request handler at route `$route`";
            Log::add($description, $description);
        }

        $controller = $actionData[0];
        $action = $actionData[1];

        self::$routes[self::$currentRoute] = [
            'route' => self::$currentRoute,
            'controller' => $controller,
            'action' => $action,
            'view' => null        // View has to be determined in controller!
        ];

        return new static();
    }


    public static function view($route, $view)
    {
        self::$currentRoute = strtolower($route);

        self::$routes[self::$currentRoute] = [
            'route' => self::$currentRoute,
            'controller' => null,   // For view routes we set 'controller=null' and this is a flag to show a static view
            'action' => 'Index',
            'view' => $view
        ];

        return new static();
    }


    public static function redirect($route, $redirect_to, $status_code = 302)
    {
        self::$currentRoute = strtolower($route);

        self::$routes[self::$currentRoute] = [
            'route' => self::$currentRoute,
            'controller' => null,
            'action' => 'Redirect',
            'redirect to' => $redirect_to,
            'status code' => $status_code,
            'view' => null
        ];
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
