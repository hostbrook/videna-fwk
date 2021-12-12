<?php

/**
 * Model to work with table `users`
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Models;


class Users extends Model
{

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
}
