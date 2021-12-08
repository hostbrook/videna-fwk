<?php

/**
 * Application config class
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;

class Config
{

    use DataArray;

    public static function init()
    {

        // Connect default application config file
        $path = PATH_FWK . 'configs/app.config.def.php';
        if (is_file($path)) {
            $config = include_once $path;
        } else Log::add("WARNING: Application config file not found.");

        // Connect app config file if exists
        $path =  'App/configs/app.config.php';
        if (is_file($path)) self::setAll(array_merge($config, include_once $path));
    }
}
