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


abstract class DB extends Database
{


    /**
     * Raw call to database
     * 
     * @param string $query is a SQL statement that does not require to return values
     * @return void
     * 
     * @example 
     *      DB::query('DROP TABLE users');
     */
    public static function query($query)
    {

        $db = static::getDB();

        $stmt = $db->query($query);
    }


    /**
     * Raw SELECT records in database using PDO placeholders
     * 
     * @param string $query is SQL `SELECT` statement with PDO placeholders
     * @param array $criterias is array of criterias
     * @return array with all records
     * 
     * @example 
     *      $users = DB::select(
     *          'SELECT * FROM users WHERE account = :account', 
     *          ['account' => 100]
     *      );
     */
    public static function select($query, $criterias)
    {

        $db = static::getDB();

        $stmt = $db->prepare($query);
        $stmt->execute($criterias);

        return $stmt->fetchAll();
    }


    /**
     * Raw INSERT of records into database using PDO placeholders
     * 
     * @param string $query is SQL `INSERT` statement with PDO placeholders
     * @param array $criterias is array of criterias
     * @return int last added record ID
     * 
     * @example 
     *      $users = DB::insert(
     *          'INSERT INTO `users` (name, email) VALUES (:name, :email)',
     *          ['name' => 'John Doe', 'email' => 'john.doe@domain.com']
     *      );
     */
    public static function insert($query, $criterias)
    {

        $db = static::getDB();

        $stmt = $db->prepare($query);
        $stmt->execute($criterias);

        return $db->lastInsertId();
    }


    /**
     * Raw UPDATE of records in database using PDO placeholders
     * 
     * @param string $query is SQL `UPDATE` statement with PDO placeholders
     * @param array $criterias is array of criteria
     * @return int number of rows affected by the SQL statement
     * 
     * @example 
     *      $users = DB::update(
     *          'UPDATE users SET email = :email WHERE id = :id',
     *          ['id' => 123, 'email' => 'john.doe@domain.com']
     *      );
     */
    public static function update($query, $criterias)
    {

        $db = static::getDB();

        $stmt = $db->prepare($query);
        $stmt->execute($criterias);

        return $stmt->rowCount();
    }


    /**
     * Raw DELETE of records from database using PDO placeholders
     * 
     * @param string $query is SQL `DELETE` statement with PDO placeholders
     * @param array $criterias is array of criterias
     * @return int number of rows affected by the SQL statement
     * 
     * @example 
     *      $users = DB::delete(
     *          'DELETE FROM users WHERE id = :id',
     *          ['id' => 123]
     *      );
     */
    public static function delete($query, $criterias)
    {

        $db = static::getDB();

        $stmt = $db->prepare($query);
        $stmt->execute($criterias);

        return $stmt->rowCount();
    }
}
