<?php

/**
 * Trait to work with array data
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;


trait DataArray
{

    private static $data = [];

    public static function get($field)
    {
        if (array_key_exists($field, self::$data)) return self::$data[$field];
        return null;
    }

    public static function set($fields)
    {
        foreach ($fields as $key => $value) {
            self::$data[$key] = $value;
        }
    }

    public static function getAll()
    {
        return self::$data;
    }

    public static function setAll($dataArray)
    {
        self::$data = $dataArray;
    }
}
