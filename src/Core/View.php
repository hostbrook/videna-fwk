<?php

/**
 * Base class to render views to show as HTML or retrn in JSON
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;


class View
{
    use DataArray;

    /**
     * @var string $route is a route to the view that needs to be shown
     */
    public static $show = null;


    /**
     * Renders and output static page
     * @return string rended HTML
     */
    public static function output()
    {
        if (self::$show == null) {
            $errorDescription = 'FATAL Error: View was not determined';
            Log::add([
                $errorDescription,
                'Controller: ' . Router::$controller,
                'Action: ' . Router::$action
            ], $errorDescription);
        }

        extract(self::getAll(), EXTR_SKIP);

        require_once PATH_VIEWS . self::$show;
    }


    /**
     * Returns AJAX request result
     * @return string rended JSON
     */
    public static function returnJSON()
    {
        die(json_encode(self::getAll()));
    }


    /**
     * Render view using provided data as a parameters.
     * 
     * @param string $view a path to view 
     * @param mixed $data rendered data in view
     * 
     * @return string rended html|text
     */
    public static function render($view, $data = [])
    {

        $file_path =  PATH_VIEWS . $view;

        if (!is_file($file_path)) {
            Log::add(["Error: View file '$view' not found."]);
            return "Error 404. View file `$view` not found.";
        }

        if (!isset($data['user'])) $data['user'] = (object)User::getAll();
        if (!isset($data['csrf'])) $data['csrf'] = (object)Csrf::getAll();
        if (!isset($data['_'])) $data['_'] = Lang::getAll();
        if (!isset($data['view'])) $data['view'] = (object)[
            'lang' => Lang::$code,
            'locale' => Lang::$locale
        ];
        if (!isset($data['config'])) $data['config'] = Config::getAll();

        extract($data, EXTR_SKIP);

        ob_start();

        include_once $file_path;

        return ob_get_clean();
    }
}
