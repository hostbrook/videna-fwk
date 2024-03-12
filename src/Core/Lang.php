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

    // ISO 639-1 Language Codes
    // https://www.w3schools.com/tags/ref_language_codes.asp
    public static $code = null;

    // Locale is identified using RFC 4646 language tags, for example: en-US
    public static $locale = null;

    /**
     * Detects the locale language and returns array of words
     * @param string $lang Language from user settings
     */
    public static function detect()
    {

        // Try to detect Locale
        if (env('HTTP_ACCEPT_LANGUAGE')) self::$locale = \Locale::acceptFromHttp(env('HTTP_ACCEPT_LANGUAGE'));

        // If just one language is suported, set its code
        if (count(Config::get('supported languages')) == 1) {
            self::$code = Config::get('default language');
        }
        elseif (self::$code != null) {
            // language code is forced by user in URL (set in router):
            $lang = mb_strtolower(self::$code);

            if ( 
                in_array($lang, array_keys(Config::get('supported languages'))) ||
                in_array($lang, Config::get('supported languages'))
            ) self::$code = $lang;
        }

        // if language code is not detected yet - try get it from current user cookies (if exists):
        if (self::$code == null && isset($_COOKIE['lang'])) {

            $lang = mb_strtolower($_COOKIE['lang']);

            if ( 
                in_array($lang, array_keys(Config::get('supported languages'))) ||
                in_array($lang, Config::get('supported languages'))
            ) self::$code = $lang;
        }

        // if language code is not detected yet - try check if user has preffered language:
        if (self::$code == null && User::get('account') > USR_UNREG and User::get('lang') != null) {
            
            self::$code = User::get('lang');
        } 
                
        // if language code is not detected yet - try pull it from locale (if applicable):
        if (self::$code == null && self::$locale != null) {
            
            $lang = mb_strtolower(substr(self::$locale, 0, 2));

            if ( 
                in_array($lang, array_keys(Config::get('supported languages'))) ||
                in_array($lang, Config::get('supported languages'))
            ) self::$code = $lang;
        }
        
        if (self::$code == null) self::$code = mb_strtolower(Config::get('default language'));


        // Connect default language file
        self::loadDefault();

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


    /**
     * Load default language file
     * @return void
     */
    public static function loadDefault()
    {
        $lang_path =  'App/lang/' . Config::get('default language') . '.php';
        if (is_file($lang_path)) self::setAll(include_once $lang_path);
    }
}