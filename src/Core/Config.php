<?php
// Videna Framework
// File: /Videna/Core/Config.php
// Desc: Application config class

namespace Videna\Core;

class Config {

	use DataArray;

	public static function init() {

		// Connect default application config file
		$path = PATH_FWK . 'configs/app.config.def.php';
		if ( is_file($path) ) {
			$config = include_once $path;
		}
		else Log::add( ["FATAL ERROR" => "Application config file not found."], "FATAL ERROR: Application config file not found.");

		// Connect app config file if exists
		$path =  'App/configs/app.config.php';
		if ( is_file($path) ) self::setAll( array_merge($config, include_once $path) );
		
	}
	
}