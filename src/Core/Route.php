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

    /** 
     * @var array|null $routes contains list of registered routes 
     */
    public static $routes = [];

    /** 
     * @var string $name contains name of the current route 
     */
    public static $name;


    /**
     * Add route to the registered routes list
     * 
     * @param string $route A route needs to be registered
     * @param string $request Name of Controller and Action separated by @
     * 
     * @return object
     */
    public static function add($route, $request)
    {

        self::$name = strtolower($route);

        list($controller, $action) = self::getControllerAction($request, $route);

        self::$routes[self::$name] = [
            'route' => self::$name,
            'controller' => $controller,
            'action' => $action,
            'view' => null        // View has to be determined in controller!
        ];

        return new static();
    }


    /**
     * Add a View route to the registered routes list
     * 
     * @param string $route A route needs to be registered
     * @param string $view A path to the view needs to be shown
     * 
     * @return object
     */
    public static function view($route, $view)
    {
        self::$name = strtolower($route);

        self::$routes[self::$name] = [
            'route' => self::$name,
            'controller' => null,   // For view routes we set 'controller=null' and this is a flag to show a static view
            'action' => 'Index',
            'view' => $view
        ];

        return new static();
    }


    /**
     * Add a route to the registered routes list, that require redirection
     * 
     * @param string $route A route needs to be registered, 'URL redirection from'
     * @param string $redirect_to A 'URL redirection to'
     * @param int $status_code A a redirection status code
     * 
     * @return void
     */
    public static function redirect($route, $redirect_to, $status_code = 302)
    {
        self::$name = strtolower($route);

        self::$routes[self::$name] = [
            'route' => self::$name,
            'controller' => null,
            'action' => 'Redirect',
            'redirect to' => $redirect_to,
            'status code' => $status_code,
            'view' => null
        ];
    }


    /**
     * Property to set conditions for route parameters
     * 
     * @param array $conditions A name of the parameter and a regular expression defining how the parameter should be constrained.
     * 
     * @return object
     */
    public static function where($conditions)
    {
        self::$routes[self::$name]['conditions'] = $conditions;
        return new static();
    }


    /**
     * Property to set route name
     * 
     * @param string $name A name of the route
     * 
     * @return object
     */
    public function name($name)
    {
        self::$routes[self::$name]['name'] = $name;
        return new static();
    }


    /**
     * Parce route to split request on controller and action
     * @param string $request 'Controller@Action'
     * @param string $route A route name
     * @return array An array with controller [1] and action [2]
     */
    public static function getControllerAction($request, $route)
    {
        $actionData = explode('@', $request);
        if (!isset($actionData[0]) or !isset($actionData[1])) {
            $description = "FATAL Error: Incorrect request handler at route `$route`";
            Log::add($description, $description);
        }

        return $actionData;
    }
}
