<?php

/**
 * Model to work with table `users`
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Models;

use PDO;


class Users extends \Videna\Helpers\Database
{

    /**
     * Get all the users as an associative array
     * @return array with all users data
     */
    public static function getAll()
    {

        $db = static::getDB();

        $stmt = $db->query('SELECT * FROM users ORDER BY id');

        return $stmt->fetchAll();
    }


    /**
     * Find user by one or more criterias
     * @param $arguments is array, for example: [ 'email' => 'john@email.com' ]
     * @return array with user data if user exists
     * @return false if user DOES NOT exist
     */
    public static function getUser($arguments)
    {

        $db = static::getDB();

        // Prepare SQL query:
        $first = true;
        $query = '';
        foreach ($arguments as $key => $value) {
            if ($first) {
                $first = false;
                $query = '';
            } else {
                $query .= ', ';
            }
            $query .=  "$key = :$key";
        }

        $sql = "SELECT * FROM users WHERE $query LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute($arguments);

        return $stmt->fetch();
    }


    /**
     * Add new user in DB
     * @param $arguments is array of user data
     * @return int User ID (`user_id`)
     */
    public static function addUser($arguments)
    {

        if (!isset($arguments['name'])) return false;
        if (!isset($arguments['email'])) return false;
        if (!isset($arguments['account'])) $arguments['account'] = USR_REG;

        $db = static::getDB();

        // Prepare SQL query:
        $first = true;
        $sql_keys = '';
        $sql_values = '';

        foreach ($arguments as $key => $value) {
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

        $sql = "INSERT INTO users ($sql_keys) VALUES ($sql_values)";
        $stmt = $db->prepare($sql);
        $stmt->execute($arguments);

        return $db->lastInsertId();
    }


    /**
     * Delete user(s) from DB by one or more criterias
     * @param $arguments is array of user data
     * @return bool 
     */
    public static function delete($arguments)
    {
        $db = static::getDB();

        // Prepare SQL query:
        $first = true;
        $query = '';
        foreach ($arguments as $key => $value) {
            if ($first) {
                $first = false;
                $query = '';
            } else {
                $query .= ' AND ';
            }
            $query .=  "$key = :$key";
        }

        // Delete records that match $arguments criteria
        $sql = 'DELETE FROM users WHERE ' . $query;
        $stmt = $db->prepare($sql);
        $stmt->execute($arguments);
    }
}
