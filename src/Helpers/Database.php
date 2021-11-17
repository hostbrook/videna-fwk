<?php

/**
 * Class-helper for work with Database
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Helpers;

use PDO;
use \Videna\Core\Log;


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

        if (USE_DB === false) {
            $msgError = 'The request to DB was initialized but DB is not allowed in the config.';
            Log::add(['FATAL Error' => $msgError], 'FATAL ERROR: ' . $msgError);
        }

        if (self::$db === null) {

            // Connect to DB
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
            $opt = [
                // Throw an Exception when an error occurs:
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Set default FETmode @ FETCH_ASSOC:
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Switch oof emulate:
                PDO::ATTR_EMULATE_PREPARES   => false
            ];
            self::$db = new PDO($dsn, DB_USER, DB_PASSWORD, $opt);
        }

        return self::$db;
    }
}
