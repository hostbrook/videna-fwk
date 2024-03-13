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
     * @var string $path is a path to the view that needs to be shown
     */
    private static $path = null;


    /**
     * Set path to the current view
     * @param string $path is a path to the view that needs to be shown
     * @return void
     */
    public static function setPath($path)
    {
        self::$path = $path;
    }


    /**
     * Retrive path to the current view
     * @return string $path is a path to the view that needs to be shown
     */
    public static function getPath()
    {
        if (self::$path == null) return false;
        return self::$path;
    }


    /**
     * Clear path to the current view
     * @return void
     */
    public static function clearPath()
    {
        self::$path = null;
    }


    /**
     * Renders and output the static HTML page
     * @return void Output rendered HTML page
     */
    public static function output()
    {
        if (!self::getPath()) {
            $errorDescription = 'FATAL Error: View was not determined';
            Log::add([
                $errorDescription,
                'Controller: ' . Router::$controller,
                'Action: ' . Router::$action
            ], $errorDescription);
        }

        extract(self::getAll(), EXTR_SKIP);

        http_response_code(Router::$statusCode);
        
        set_include_path(PATH_VIEWS);
        require_once self::getPath();
    }


    /**
     * Returns request result in  JSON
     * @return string Rended JSON
     */
    public static function returnJSON()
    {
        http_response_code(Router::$statusCode);
        echo json_encode(self::getAll());
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
        if (!isset($data['lang'])) $data['lang'] = Lang::$code;
        if (!isset($data['config'])) $data['config'] = Config::getAll();
        if (!isset($data['statusCode'])) $data['statusCode'] = Router::$statusCode;

        extract($data, EXTR_SKIP);

        ob_start();

        include_once $file_path;

        return ob_get_clean();
    }
}
