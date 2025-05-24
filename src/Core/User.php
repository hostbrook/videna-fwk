<?php

/**
 * Class to work with users
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;

use \Videna\Models\Users;
use \Videna\Models\Tokens;


class User
{

    use DataArray;

    private static $PublicKey = null;
    private static $PrivateKey = null;


    /**
     * Detect user account type and pull user's data if user is registered
     * @return array with user data
     */
    public static function detect()
    {
        
        // if session ID was received via cookie, user was registered. 
        // In this case - start session:
        if (isset($_REQUEST[session_name()])) {

            session_start();

            if (isset($_SESSION['user_id'])) {
                // user has been already  logged-in. return user info array:
                self::setAll(Users::get(['id' => $_SESSION['user_id']], 1));
                session_write_close();
                return;
            }

            session_write_close();
        }
        
        // At this point session expired or user wasn't registered

        // Try to recovery user login via cookies
        if (!isset($_COOKIE['public-key'])) {
            // user wasn't registered yet
            self::set(['account' => USR_UNREG]);
            return;
        }
        
        // At this point 'public-key' exists, that means user was registered
        // but session was expired. We need to check if there is a recod about the user in DB
        // So, first we get 'private-key' - footprint of client:

        $userId = Tokens::getUserId($_COOKIE['public-key'], self::getPrivateKey());

        if ($userId == null) {
            // no records with token in DB
            self::set(['account' => USR_UNREG]);
            return;
        }

        // Token exists, so just pull user info from DB:
        $user = Users::get(['id' => $userId], 1);
        if ($user == false) {
            self::set(['account' => USR_UNREG]);
            return;
        }

        // Add user info in session
        session_start();
        $_SESSION['user_id'] = $user['id'];
        session_write_close();

        // Set array with user's data
        self::setAll($user);
    }


    /**
     * Log in user into a application
     * NOTE: Before call this function, user needs to be authenticated and User ID known 
     * @param $userId is `user_id` in DB table `users`
     * @param $expires is The time the cookie expires, is in number of seconds since the epoch. 
     *        For instance, time()+60*60*24*30 will set the cookie to expire in 30 days.
     *        If set to 0, the cookie will expire at the end of the session (when the browser closes).
     * @return void
     */
    public static function login($userId, $expires=0)
    {

        // Add user ID info in session:
        session_start();
        $_SESSION['user_id'] = $userId;
        $_REQUEST[session_name()] = session_id();
        session_write_close();

        // Update public key in DB:
        Tokens::updatePublicKey(self::getPublicKey(true), $userId, self::getPrivateKey(), $expires);

        // Update public key in cookies:
        setcookie('public-key', self::getPublicKey(), $expires, '/', env('SERVER_NAME'));

        // Update CSRF token
        Csrf::deleteToken();
        Csrf::init();

        self::detect();
    }


    /**
     * Log-out user from the application
     * @return void
     */
    public static function logout()
    {

        session_start();

        // Delete session cookies
        setcookie(session_name(), '', time() - 3600);

        // Destroy session:
        session_unset();
        session_destroy();

        // Delete cookie 'public-key'
        setcookie('public-key', '', time() - 3600);

        if (isset($_COOKIE['public-key'])) {
            // Delete token record from DB:
            Tokens::deleteToken($_COOKIE['public-key'], self::getPrivateKey());

            unset($_COOKIE['public-key']);
        }

        Csrf::deleteToken();

        self::clear();
        self::set(['account' => USR_UNREG]);
    }


    /**
     * Generates 'private-key': browser footprint
     * @return string sha1
     */
    private static function getPrivateKey()
    {
        if (self::$PrivateKey == null)
            self::$PrivateKey = sha1(env('HTTP_USER_AGENT') . env('REMOTE_ADDR'));
        return self::$PrivateKey;
    }


    /**
     * Generates 'public-key' random string
     * @return string sha1
     */
    private static function getPublicKey($generateNew = false)
    {
        if (self::$PublicKey == null or $generateNew)
            self::$PublicKey = sha1(rand() . time());
        return self::$PublicKey;
    }


    /**
     * Generates 'token' - random string
     * @return string
     */
    public static function getToken()
    {
        return substr(md5(time() . rand()), 0, TOKEN_LENGTH);
    }
}
