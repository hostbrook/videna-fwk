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

            $csrfToken = explode(':', $_COOKIE['csrf_token']);
            $token = $csrfToken[0];
        }
        else {

            $token = sha1(session_id() . time());
            $csrfToken = $token.':'.self::getSessionId();
            $expires = Config::get('csrf token expires');
            setcookie('csrf_token', $csrfToken, $expires, '/', HOST_NAME, (HTP_PROTOCOL == 'https' ? true : false));
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
        if (Router::get('csrf_token') == null || !isset($_COOKIE['csrf_token'])) return false;

        $csrfToken = explode(':', $_COOKIE['csrf_token']);

        if (!is_array($csrfToken) || !isset($csrfToken[1])) return false;

        if ($csrfToken[0] != Router::get('csrf_token') || self::getSessionId() != $csrfToken[1]) return false;

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


    /**
     * Generates current session ID as part of CSRF token based on server and client data
     * @return string sha1
     */
    private static function getSessionId()
    {
        return sha1($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SERVER_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    }

}
