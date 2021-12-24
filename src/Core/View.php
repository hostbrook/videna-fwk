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
     * Renders static page
     * @return string rended HTML
     */
    public static function render()
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
     * Returns AJAX requests results
     * @return string rended JSON
     */
    public static function jsonRender()
    {

        if (empty(self::getAll())) {
            self::set([
                'response' => -1,
                'status' => 'No data to show'
            ]);
        }

        if (self::$show != null) {

            extract(self::getAll(), EXTR_SKIP);

            ob_start();

            include_once PATH_VIEWS . self::$show;

            self::set(['html' => ob_get_clean()]);
        }

        die(json_encode(self::getAll()));
    }


    /**
     * Returns PHP templates and lets to use arguments (like language array), 
     * for example in email messages.
     * 
     * @param string $view path to view 
     * @return string rended html|text
     */
    public static function include($view)
    {

        $file_path =  PATH_VIEWS . $view;

        if (!is_file($file_path)) {
            Log::add(["Error: View file '$view' not found."]);
            return "{Not found: `$view`}";
        }

        extract(View::getAll(), EXTR_SKIP);

        ob_start();

        include_once $file_path;

        return ob_get_clean();
    }
}
