<?php
// Videna Framework
// File: /Videna/Core/Lang.php
// Desc: Determine language and get array of words

namespace Videna\Core;


class Lang {

	public $langArray;	
	private $code;

	
	public function setCode($code) {
		$this->code = mb_strtolower($code);
	}

	
	public function getCode() {
		return $this->code;
	}

	
	public function __construct($lang) {

		/*-------------------------------------------------------
			1. Determine language
		-------------------------------------------------------*/

		if ( $lang ) {
			
			$this->setCode($lang);

		} 
		else {

			$this->setCode( Config::get('default language') );

			// [1] (Lowest) priority: browser language (if applicable):
			if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
				$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
				if ( in_array($lang, Config::get('supported languages')) ) $this->setCode($lang);
			}
			
			// [2] (Medium) priority: language from current user coockies (if exists):
			if ( isset($_COOKIE['lang']) ) {
				$lang = $_COOKIE['lang'];
				if ( in_array($lang, Config::get('supported languages')) ) $this->setCode($lang);
			}

		}
		
		// [3] (High) priority: language forced by user (if exists):
		if ( isset(Router::$lang) ) {
			$lang = Router::$lang; 
			if ( in_array($lang, Config::get('supported languages')) ) $this->setCode($lang);
		}
		
		
		/*-------------------------------------------------------
		  2. Connect languages files
		-------------------------------------------------------*/
		
		// Connect default language file
		$lang_path =  'App/lang/'. Config::get('default language') . '.php';
		if ( is_file($lang_path) )  $this->langArray = include_once $lang_path;
		
		// Connect new language file if required
		if ( $this->getCode() != Config::get('default language') ) {
			$lang_path = 'App/lang/'. $this->getCode() . '.php'; 
			if ( is_file($lang_path) )  {
				$new = include_once $lang_path;
				$this->langArray = array_merge($this->langArray, $new);
			}
		}
		
		/*-------------------------------------------------------
		  3. Save user's languages
		-------------------------------------------------------*/
		
		setcookie('lang', $this->getCode(), 0, '/');
	
	} // END init()


} // END class Lang