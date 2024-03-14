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


    /**
     * Detects the language code and returns array from language file 
     * @param string $lang Language from user settings
     */
    public static function detect()
    {
        $lang = null;

        // If just one language is suported, set its code
        if (count(Config::get('supported languages')) == 1) {
            $lang = Config::get('default language');
        }
        elseif (Router::$lang != null) {
            // language code is forced by user in URL (set in router):
            $lang = mb_strtolower(Router::$lang);

            if (!in_array($lang, array_keys(Config::get('supported languages'))))
            if (!in_array($lang, Config::get('supported languages'))) $lang = null;
        }

        // if language code is not detected yet - try get it from current user cookies (if exists):
        if ($lang == null && isset($_COOKIE['lang'])) {

            $lang = mb_strtolower($_COOKIE['lang']);

            if (!in_array($lang, array_keys(Config::get('supported languages'))))
            if (!in_array($lang, Config::get('supported languages'))) $lang = null;
        }

        // if language code is not detected yet - try check if user has preffered language:
        if ($lang == null && User::get('account') > USR_UNREG and User::get('lang') != null) {
            
            $lang = User::get('lang');
        }
        
        // if language code is not detected yet - use default language
        if ($lang == null) $lang = mb_strtolower(Config::get('default language'));

        // Save user's languages in cookies
        setcookie('lang', $lang, 0, '/');
        
        // Load language files
        self::load($lang);

        return $lang;
    }


    /**
     * Load language files
     * @return void
     */
    public static function load($lang)
    {
        // Connect default language file
        $lang_path =  'App/lang/' . Config::get('default language') . '.php';
        if (is_file($lang_path)) self::setAll(include_once $lang_path);
        
        // Connect new language file if required
        if ($lang != Config::get('default language')) {

            $lang_path = 'App/lang/' . $lang . '.php';
            
            if (is_file($lang_path)) self::mergeWith(include_once $lang_path);
        }
    }
}