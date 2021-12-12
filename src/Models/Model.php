<?php

/**
 * Class for work with Database
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Models;

use \Videna\Core\Database;


abstract class Model extends Database
{

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
     * Find record by one or more criteria for the existing model
     * @param array $criteria is array of search criteria
     * @param int $limit is number of records to get
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
    public static function get($criteria = [], $limit = '*')
    {

        $db = static::getDB();

        $sql = 'SELECT * FROM `' . self::getTableName() . '`';

        if (!empty($criteria)) {
            // Prepare SQL query:
            $first = true;
            $query = '';
            foreach ($criteria as $key => $value) {
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
        $stmt->execute($criteria);

        if ($limit == 1) {
            return $stmt->fetch();
        } else return $stmt->fetchAll();
    }


    /**
     * Count record qty by one or more criteria for the existing model
     * @param $criteria is array of count criteria
     * @return int records qty
     * 
     * @example
     *      Count all admin users in table `users`:
     *      $adminsQty = Users::count([ 'account' => 200 ]);
     */
    public static function count($criteria)
    {

        $db = static::getDB();

        $sql = 'SELECT * FROM `' . self::getTableName() . '`';

        // Prepare SQL query:
        $first = true;
        $query = '';
        foreach ($criteria as $key => $value) {
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
        $stmt->execute($criteria);

        return $stmt->rowCount();
    }


    /**
     * Add record into DB for the existing model
     * @param array $criteria is array of crieria for inserted records
     * @return int record ID
     * 
     * @example
     *       $userID = Users::add([
     *           'name' => 'John',
     *           'last_name' => 'Doe',
     *           'email' => 'john.doe@domain.com'
     *       ]);
     */
    public static function add($criteria)
    {

        $db = static::getDB();

        // Prepare SQL query:
        $first = true;
        $sql_keys = '';
        $sql_values = '';

        foreach ($criteria as $key => $value) {
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
        $stmt->execute($criteria);

        return $db->lastInsertId();
    }


    /**
     * Delete record(s) from DB by one or more criteria for the existing model
     * @param array $criteria is an array of criteria fro deleted records
     * @param string $condition is a condition for multiply criteria
     * @return int number of rows affected by the SQL statement
     * 
     * @example
     *      Users::del(['id' => $userID]);
     */
    public static function del($criteria, $condition = 'AND')
    {
        $db = static::getDB();

        // Prepare SQL query:
        $first = true;
        $query = '';
        foreach ($criteria as $key => $value) {
            if ($first) {
                $first = false;
                $query = '';
            } else {
                $query .= " $condition ";
            }
            $query .=  "$key = :$key";
        }

        // Delete records that match $criteria
        $sql = 'DELETE FROM `' . self::getTableName() . '` WHERE ' . $query;
        $stmt = $db->prepare($sql);
        $stmt->execute($criteria);

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
