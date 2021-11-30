<?php

/**
 * Class for work with Database
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Models;

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


    /**
     * Get all records as an associative array for the existing model
     * Note: Name of the current table = class name.
     * 
     * @return array with all records
     */
    public static function getAll()
    {

        $db = static::getDB();

        $stmt = $db->query('SELECT * FROM `' . self::getTableName() . '`');

        return $stmt->fetchAll();
    }


    /**
     * Find record by one or more criterias for the existing model
     * @param $criterias is array of criterias
     * @return array with records data (associative array)
     * @return false if record does not exist
     * 
     * @example
     *      Get all users from table `users`:
     *      $users = Users::get();
     * @example
     *      Get all admin users from table `users`:
     *      $admins = Users::get([ 'account' => 200 ]);
     * @example
     *      Get one user from table `users` by user email:
     *      $user = Users::get(['email' => 'john.doe@domain.com'], 1);
     */
    public static function get($criterias = [], $limit = '*')
    {

        $db = static::getDB();

        $sql = 'SELECT * FROM `' . self::getTableName() . '`';

        if (!empty($criterias)) {
            // Prepare SQL query:
            $first = true;
            $query = '';
            foreach ($criterias as $key => $value) {
                if ($first) {
                    $first = false;
                    $query = '';
                } else {
                    $query .= ', ';
                }
                $query .=  "$key = :$key";
            }

            $sql .= " WHERE $query";
        }

        if ($limit != '*') $sql .= " LIMIT $limit";

        $stmt = $db->prepare($sql);
        $stmt->execute($criterias);

        if ($limit == 1) {
            return $stmt->fetch();
        } else return $stmt->fetchAll();
    }


    /**
     * Count record qty by one or more criterias for the existing model
     * @param $criterias is array
     * @return int records qty
     * 
     * @example
     *      Count all admin users in table `users`:
     *      $adminsQty = Users::count([ 'account' => 200 ]);
     */
    public static function count($criterias)
    {

        $db = static::getDB();

        $sql = 'SELECT * FROM `' . self::getTableName() . '`';

        // Prepare SQL query:
        $first = true;
        $query = '';
        foreach ($criterias as $key => $value) {
            if ($first) {
                $first = false;
                $query = '';
            } else {
                $query .= ', ';
            }
            $query .=  "$key = :$key";
        }

        $sql .= " WHERE $query";

        $stmt = $db->prepare($sql);
        $stmt->execute($criterias);

        return $stmt->rowCount();
    }


    /**
     * Add record into DB for the existing model
     * @param array $criterias is array of inserted data
     * @return int record ID
     * 
     * @example
     *       $userID = Users::add([
     *           'name' => 'John',
     *           'last_name' => 'Doe',
     *           'email' => 'john.doe@domain.com'
     *       ]);
     */
    public static function add($criterias)
    {

        $db = static::getDB();

        // Prepare SQL query:
        $first = true;
        $sql_keys = '';
        $sql_values = '';

        foreach ($criterias as $key => $value) {
            if ($first) {
                $first = false;
                $sql_keys = '';
                $sql_values = '';
            } else {
                $sql_keys .= ', ';
                $sql_values .= ', ';
            }
            $sql_keys .=  $key;
            $sql_values .=  ":$key";
        }

        $sql = "INSERT INTO `" . self::getTableName() . "` ($sql_keys) VALUES ($sql_values)";
        $stmt = $db->prepare($sql);
        $stmt->execute($criterias);

        return $db->lastInsertId();
    }


    /**
     * Delete record(s) from DB by one or more criterias for the existing model
     * @param array $criterias is an array of record data
     * @param string $condition is a condition for multiply criterias
     * @return int number of rows affected by the SQL statement
     * 
     * @example
     *      Users::del(['id' => $userID]);
     */
    public static function del($criterias, $condition = 'AND')
    {
        $db = static::getDB();

        // Prepare SQL query:
        $first = true;
        $query = '';
        foreach ($criterias as $key => $value) {
            if ($first) {
                $first = false;
                $query = '';
            } else {
                $query .= " $condition ";
            }
            $query .=  "$key = :$key";
        }

        // Delete records that match $criterias
        $sql = 'DELETE FROM `' . self::getTableName() . '` WHERE ' . $query;
        $stmt = $db->prepare($sql);
        $stmt->execute($criterias);

        return $stmt->rowCount();
    }


    /**
     * Get current DB table name based on class CamelCase name
     * @return string table name
     */
    private static function getTableName()
    {
        $class_name_arr = explode('\\', get_called_class());
        $table_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', end($class_name_arr)));

        return $table_name;
    }
}
