<?php

/**
 * Model to work with table `tokens`
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Models;


class Tokens extends Model
{


    /**
     * Find User ID by 'private-key' and 'public-key'
     *
     * @return array
     */
    public static function getUserId($publicKey, $privateKey)
    {

        $db = static::getDB();

        $sql = 'SELECT user_id FROM tokens 
						WHERE public_key = :public_key AND private_key = :private_key
						LIMIT 1';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            'public_key' => $publicKey,
            'private_key' => $privateKey
        ]);

        $user_id = $stmt->fetchColumn();

        if ($user_id) {
            return $user_id;
        } else return null;
    }


    /**
     * Update 'public-key' and expires time in DB bby 'private-key' and User ID.
     *
     * @return void
     */
    public static function updatePublicKey($publicKey, $userId, $privateKey, $expires = null)
    {

        $db = static::getDB();

        // Check if 'private-key' exists

        $sql = 'SELECT id 
						FROM tokens 
						WHERE private_key = :private_key AND user_id = :user_id
						LIMIT 1';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            'private_key' => $privateKey,
            'user_id' => $userId
        ]);

        $id = $stmt->fetchColumn();

        if ($id) {
            // 'private-key' already exists. Just update 'public-key' value:

            $sql = 'UPDATE tokens  
							SET public_key = :public_key, date=now(), expires = :expires 
				 			WHERE id = :id 
							LIMIT 1';

            $stmt = $db->prepare($sql);

            $stmt->execute([
                'id' => $id,
                'public_key' => $publicKey,
                'expires' => $expires
            ]);
        } else {
            // 'private-key' DOES NOT exist. Add a record with the new 'public-key':

            $sql = 'INSERT INTO tokens (user_id, public_key, private_key, expires) 
							VALUES (:user_id, :public_key, :private_key, :expires)';

            $stmt = $db->prepare($sql);

            $stmt->execute([
                'user_id' => $userId,
                'public_key' => $publicKey,
                'private_key' => $privateKey,
                'expires' => $expires
            ]);
        }
    }


    /**
     * Delete token with 'public-key' from DB
     *
     * @return void
     */
    public static function deleteToken($publicKey, $privateKey)
    {

        $db = static::getDB();

        // Delete all records with 'public-key'
        $sql = 'DELETE FROM tokens 
						WHERE public_key = :public_key OR private_key = :private_key';
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'public_key' => $publicKey,
            'private_key' => $privateKey
        ]);
    }
} // END class Tokens