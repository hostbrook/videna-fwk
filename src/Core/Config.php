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
        // Set default envirument values required by framework
        //$_ENV['APP_URL']='localhost';

        // Load enviroment values from .env file if it exists
        self::loadEnv();     

        // Connect default application config file
        $path = PATH_FWK . 'configs/app.config.def.php';
        if (is_file($path)) {
            $config = include_once $path;
        } else Log::fatal("Default application config file not found.");

        // Connect app config file if exists
        $path =  'App/configs/app.config.php';
        if (is_file($path)) {
            self::setAll(array_merge($config, include_once $path));
        } else Log::notice("Application config file not found.");
    }


    private static function loadEnv() 
    {    
        // Check .env file exists
        if (!is_file(PATH_APP_ENV)) {
            Log::warning("Environment file .env not found.");
            return;
        }
        
        // Check .env file is readable
        if (!is_readable(PATH_APP_ENV)) {
            Log::warning("Permission denied for reading the environment file .env");
            return;
        }

        // Get variables from the (.env) file 
        $lines = file(PATH_APP_ENV, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            // Skip comment line
            if (strpos(trim($line), '#') === 0) continue;
            // Skip empty line or incorrect format
            if (!str_contains($line, '=')) continue;

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            // Skip if name doesn't exist
            if ($name == '') continue;

            $_ENV[$name] = self::checkEnvValue($value);
        }
    }


    private static function checkEnvValue($value) 
    {
        $value = trim($value," ");

        if ($value != trim($value,"\'\"")) return trim($value,"\'\"");
        
        if (strtolower($value) == 'true') return true;
        if (strtolower($value) == 'false') return false;

        if (is_numeric($value)) {
            if (is_float($value)) return (float)$value;
            if (is_int($value)) return (int)$value;
        }

        if (is_string($value)) return $value;
    
        return null;
    }
}