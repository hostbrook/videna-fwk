<?php
// Videna Framework
// File: /Videna/Core/View.php
// Desc: Base view class

namespace Videna\Core;


class View {

	use DataArray;


	public static function render() {

		extract(self::getAll(), EXTR_SKIP);

		require_once PATH_VIEWS . Router::$view  .'.php';

	}


	public static function jsonRender() {

		if ( empty( self::getAll() ) ) {
			self::set([
				'response' => -1,
			  'status' => 'No data to show'
			]);
		}

		if ( Router::$view ) {

			extract(self::getAll(), EXTR_SKIP);
	
			ob_start();
			
			include_once PATH_VIEWS . Router::$view  .'.php';
			
			self::set([ 'html' => ob_get_clean() ]);

		}

		die( json_encode(self::getAll()) );

	}


} // END class View