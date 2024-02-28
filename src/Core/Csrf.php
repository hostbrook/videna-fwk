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
            setcookie('csrf_token', $token, $expires, '/', env('SERVER_NAME'));
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
            if (env('REQUEST_URI')) $logArr[] = 'Requested URI: ' . htmlspecialchars(env('REQUEST_URI'));
            if (env('REMOTE_ADDR')) $logArr[] = 'Remote address: ' . htmlspecialchars(env('REMOTE_ADDR'));
            if (env('HTTP_USER_AGENT')) $logArr[] = 'User agent: ' . htmlspecialchars(env('HTTP_USER_AGENT'));
            
            if (env('APP_DEBUG')) Log::warning($logArr);
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
