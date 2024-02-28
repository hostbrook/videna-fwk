<?php

/**
 * Class for work with Database
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;

use PDO;


abstract class Database
{

    protected static $db = null;

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        if (!env('DB_HOST') || !env('DB_NAME') || !env('DB_USER'))
            Log::fatal('Can\t connect to database: Host, Databese Name or Database user are not determined in .env file');

        if (self::$db === null) {

            // Connect to DB
            $dsn = 'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME') . ';charset=utf8';
            $opt = [
                // Throw an Exception when an error occurs:
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Set default FETmode @ FETCH_ASSOC:
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Switch oof emulate:
                PDO::ATTR_EMULATE_PREPARES   => false
            ];
            self::$db = new PDO($dsn, env('DB_USER'), env('DB_PASSWORD'), $opt);
        }

        return self::$db;
    }
}
