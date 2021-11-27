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

    public $langArray;
    private $code;


    /**
     * Returns locale language code
     * @param string $code Set locale
     * @return void 
     */
    public function setCode($code)
    {
        $this->code = mb_strtolower($code);
    }


    /**
     * Returns locale language code
     * @return string Locale language code
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * Detects the locale language and returns array of words
     * @param string $lang Language from user settings
     */
    public function __construct($lang)
    {

        /*-------------------------------------------------------
		  1. Detect the locale language
		-------------------------------------------------------*/

        if ($lang) {
            // If locale language exists in users settings, set it
            $this->setCode($lang);
        } else {

            $this->setCode(Config::get('default language'));

            // [1] (Lowest) priority: browser language (if applicable):
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                if (in_array($lang, Config::get('supported languages'))) $this->setCode($lang);
            }
        }

        // [2] (Medium) priority: language from current user coockies (if exists):
        if (isset($_COOKIE['lang'])) {
            $lang = $_COOKIE['lang'];
            if (in_array($lang, Config::get('supported languages'))) $this->setCode($lang);
        }

        // [3] (High) priority: language forced by user (if exists):
        if (isset(Router::$lang) and Router::$lang != null) {
            $lang = Router::$lang;
            if (in_array($lang, Config::get('supported languages'))) $this->setCode($lang);
        }


        /*-------------------------------------------------------
		  2. Connect languages files
		-------------------------------------------------------*/

        // Connect default language file
        $lang_path =  'App/lang/' . Config::get('default language') . '.php';
        if (is_file($lang_path))  $this->langArray = include_once $lang_path;

        // Connect new language file if required
        if ($this->getCode() != Config::get('default language')) {
            $lang_path = 'App/lang/' . $this->getCode() . '.php';
            if (is_file($lang_path)) {
                $new = include_once $lang_path;
                $this->langArray = array_merge($this->langArray, $new);
            }
        }

        /*-------------------------------------------------------
		  3. Save user's languages
		-------------------------------------------------------*/

        setcookie('lang', $this->getCode(), 0, '/');
    }
}
