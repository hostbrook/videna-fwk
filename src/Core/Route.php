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
     * @var string $name contains the name of the current route 
     */
    public static $name = null;

    /** 
     * @var string $current contains the current route pattern
     */
    private static $current = null;


    /**
     * Add route for GET request to the registered routes list
     * The HTTP GET method is used to read (or retrieve) a representation of a resource.
     * 
     * @param string $route A route needs to be registered
     * @param string $routeHandler Name of Controller and Action separated by @
     * 
     * @return object
     */
    public static function get($route, $routeHandler)
    {

        self::$current = count(self::$routes);

        list($controller, $action) = self::getControllerAction($routeHandler, $route);

        self::$routes[self::$current] = [
            'route' => strtolower($route),
            'controller' => $controller,
            'action' => $action,
            'method' => 'GET',
            'view' => null        // View has to be determined in controller!
        ];

        return new static();
    }


    /**
     * Add route for POST request to the registered routes list
     * The POST method is most often utilized to create new resources.
     * 
     * @param string $route A route needs to be registered
     * @param string $routeHandler Name of Controller and Action separated by @
     * 
     * @return object
     */
    public static function post($route, $routeHandler)
    {

        self::$current = count(self::$routes);

        list($controller, $action) = self::getControllerAction($routeHandler, $route);

        self::$routes[self::$current] = [
            'route' => strtolower($route),
            'controller' => $controller,
            'action' => $action,
            'method' => 'POST',
            'view' => null        // View has to be determined in controller!
        ];

        return new static();
    }


    /**
     * Add route for PATCH request to the registered routes list
     * The PATCH method applies partial modifications to a resource.
     * 
     * @param string $route A route needs to be registered
     * @param string $routeHandler Name of Controller and Action separated by @
     * 
     * @return object
     */
    public static function patch($route, $routeHandler)
    {

        self::$current = count(self::$routes);

        list($controller, $action) = self::getControllerAction($routeHandler, $route);

        self::$routes[self::$current] = [
            'route' => strtolower($route),
            'controller' => $controller,
            'action' => $action,
            'method' => 'PATCH',
            'view' => null        // View has to be determined in controller!
        ];

        return new static();
    }


    /**
     * Add route for DELETE request to the registered routes list
     * DELETE is used to delete a resource identified by filters or ID.
     * 
     * @param string $route A route needs to be registered
     * @param string $routeHandler Name of Controller and Action separated by @
     * 
     * @return object
     */
    public static function delete($route, $routeHandler)
    {

        self::$current = count(self::$routes);

        list($controller, $action) = self::getControllerAction($routeHandler, $route);

        self::$routes[self::$current] = [
            'route' => strtolower($route),
            'controller' => $controller,
            'action' => $action,
            'method' => 'DELETE',
            'view' => null        // View has to be determined in controller!
        ];

        return new static();
    }


    /**
     * Add route for PUT request to the registered routes list
     * The PUT method replaces all current representations of the target resource with the request payload.
     * 
     * @param string $route A route needs to be registered
     * @param string $routeHandler Name of Controller and Action separated by @
     * 
     * @return object
     */
    public static function put($route, $routeHandler)
    {

        self::$current = count(self::$routes);

        list($controller, $action) = self::getControllerAction($routeHandler, $route);

        self::$routes[self::$current] = [
            'route' => strtolower($route),
            'controller' => $controller,
            'action' => $action,
            'method' => 'PUT',
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
        self::$current = count(self::$routes);

        self::$routes[self::$current] = [
            'route' => strtolower($route),
            'controller' => null,   // For view routes we set 'controller=null' and this is a flag to show a static view
            'action' => 'ShowView',
            'method' => 'GET',
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
        self::$current = count(self::$routes);

        self::$routes[self::$current] = [
            'route' => strtolower($route),
            'controller' => null,
            'action' => 'Redirect',
            'method' => 'GET',
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
        self::$routes[self::$current]['conditions'] = $conditions;
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
        self::$routes[self::$current]['name'] = $name;
        return new static();
    }


    /**
     * Parce route to split route handler on controller and action
     * @param string $routeHandler 'Controller@Action'
     * @param string $route A route name
     * @return array An array with controller [1] and action [2]
     */
    public static function getControllerAction($routeHandler, $route)
    {
        $actionData = explode('@', $routeHandler);
        if (!isset($actionData[0]) or !isset($actionData[1])) {
            $description = "FATAL Error: Incorrect route handler at route `$route`";
            Log::fatal($description);
        }

        return $actionData;
    }
}
