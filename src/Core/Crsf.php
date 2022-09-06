<?php

/**
 * Class to work with CSRF Attack Protection
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;


class Crsf
{

    use DataArray;


    /**
     * Create CRSF token and device ID fpr current session
     * @return void
     */
    public static function init()
    {

        if (isset($_COOKIE['crsf_token'])) {

            $crsfToken = explode(':', $_COOKIE['crsf_token']);
            $token = $crsfToken[0];
        }
        else {

            $token = sha1(session_id() . time());
            $crsfToken = $token.':'.self::getSessionId();
            $expires = Config::get('crsf token expires');
            setcookie('crsf_token', $crsfToken, $expires, '/', HOST_NAME, (HTP_PROTOCOL == 'https' ? true : false));
        }

        self::set([
            'token' => $token,
            'input' => '<input type="hidden" name="crsf_token" value="'.$token.'">',
            'json' => '"crsf_token": "'.$token.'"',
            'meta' => '<meta name="csrf-token" content="'.$token.'">'
        ]);
    }


    /**
     * Validation of CRSF token 
     * @param string $token Token to check
     * @return boolean true (valid token) || false (invalid token)
     */
    public static function valid($token)
    {
        if ( !isset($_COOKIE['crsf_token']) ) return false;

        $crsfToken = explode(':', $_COOKIE['crsf_token']);

        if (!is_array($crsfToken) || !isset($crsfToken[1])) return false;

        if ($crsfToken[0] != $token || self::getSessionId() != $crsfToken[1]) return false;

        return true;
    }


    /**
     * Delete CRSF token
     * @return void
     */
    public static function deleteToken()
    {
        // Delete cookie 'public-key'
        setcookie('crsf_token', '', time() - 3600);

        unset($_COOKIE['crsf_token']);

        self::clear();
    }


    /**
     * Generates current session ID as part of CRSF token based on server and client data
     * @return string sha1
     */
    private static function getSessionId()
    {
        return sha1($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SERVER_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    }

}
