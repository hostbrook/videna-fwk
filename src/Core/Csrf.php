<?php

/**
 * Class to work with CSRF Attack Protection
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;


class Csrf
{

    use DataArray;


    /**
     * Create CSRF token and device ID fpr current session
     * @return void
     */
    public static function init()
    {

        if (isset($_COOKIE['csrf_token'])) {
            $token = $_COOKIE['csrf_token'];
        }
        else {
            $token = sha1(time());
            $expires = Config::get('csrf token expires');
            setcookie('csrf_token', $token, $expires, '/', HOST_NAME, (HTP_PROTOCOL == 'https' ? true : false));
        }

        self::set([
            'token' => $token,
            'input' => '<input type="hidden" name="csrf_token" value="'.$token.'">',
            'json' => '"csrf_token": "'.$token.'"',
            'meta' => '<meta name="csrf_token" content="'.$token.'">'
        ]);
    }


    /**
     * Validation of CSRF token 
     * @return boolean true (valid csrf_token) || false (invalid csrf_token)
     */
    public static function valid()
    {

        if (Router::get('csrf_token') == null || !isset($_COOKIE['csrf_token'])) {

            $logArr = [];
            if (Router::get('csrf_token') == null) $logArr[] = 'CSRF token doesn\'t provided by agent.';
            if (!isset($_COOKIE['csrf_token'])) $logArr[] = 'Cookie "csrf_token" doesn\'t exist.';
            if (isset($_SERVER['REQUEST_URI'])) $logArr[] = 'Requested URI: ' . htmlspecialchars($_SERVER['REQUEST_URI']);
            if (isset($_SERVER['REMOTE_ADDR'])) $logArr[] = 'Remote address: ' . htmlspecialchars($_SERVER['REMOTE_ADDR']);
            if (isset($_SERVER['HTTP_USER_AGENT'])) $logArr[] = 'User agent: ' . htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
            
            if (APP_DEBUG) Log::warning($logArr);
            return false;
        }

        return true;
    }


    /**
     * Delete CSRF token
     * @return void
     */
    public static function deleteToken()
    {
        // Delete cookie 'public-key'
        setcookie('csrf_token', '', time() - 3600);

        unset($_COOKIE['csrf_token']);

        self::clear();
    }

}
