<?php

/**
 * Detects the locale language and returns array of words
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */

namespace Videna\Core;


class Lang
{

    use DataArray;

    public static $code = null;
    public static $locale = null;

    /**
     * Detects the locale language and returns array of words
     * @param string $lang Language from user settings
     */
    public static function detect()
    {

        /*-------------------------------------------------------
		  1. Detect the locale language
		-------------------------------------------------------*/

        // Try to detect Locale
        // Locale is identified using RFC 4646 language tags
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) self::$locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);

        // Check if user have preffered language:
        if (User::get('account') > USR_UNREG and User::get('lang') != null) {

            self::$code = User::get('lang');

        } else {

            self::$code = mb_strtolower(Config::get('default language'));

            // [1] (Lowest) priority: browser language (if applicable):
            if (self::$locale != null) {

                $lang = substr(self::$locale, 0, 2);

                if (in_array($lang, Config::get('supported languages'))) self::$code = mb_strtolower($lang);
            }
        }

        // [2] (Medium) priority: language from current user coockies (if exists):
        if (isset($_COOKIE['lang'])) {

            $lang = $_COOKIE['lang'];

            if (in_array($lang, Config::get('supported languages'))) self::$code = mb_strtolower($lang);
        }

        // [3] (High) priority: language forced by user (if exists):
        if (isset(Router::$lang) and Router::$lang != null) {

            $lang = Router::$lang;

            if (in_array($lang, Config::get('supported languages'))) self::$code = mb_strtolower($lang);
        }


        /*-------------------------------------------------------
		  2. Connect languages files
		-------------------------------------------------------*/

        // Connect default language file
        $lang_path =  'App/lang/' . Config::get('default language') . '.php';
        if (is_file($lang_path)) self::setAll(include_once $lang_path);

        // Connect new language file if required
        if (self::$code != Config::get('default language')) {

            $lang_path = 'App/lang/' . self::$code . '.php';

            if (is_file($lang_path)) self::mergeWith(include_once $lang_path);
        }

        /*-------------------------------------------------------
		  3. Save user's languages
		-------------------------------------------------------*/

        setcookie('lang', self::$code, 0, '/');
    }
}
