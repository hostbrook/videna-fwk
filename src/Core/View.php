<?php
// Videna Framework
// File: /Videna/Core/View.php
// Desc: Base view class

namespace Videna\Core;


class View {


	public static function render($viewArgs = []) {

		extract($viewArgs, EXTR_SKIP);

		require_once PATH_VIEWS . Router::$view  .'.php';

	}


	public static function jsonRender( $viewArgs = [] ) {

		if ( empty($viewArgs) ) {
			$viewArgs['ajax']['response'] = -1;
			$viewArgs['ajax']['status'] = 'No data to show';
		}

		if ( Router::$view ) {

			extract($viewArgs, EXTR_SKIP);
	
			ob_start();
			
			include_once PATH_VIEWS . Router::$view  .'.php';
			
			$viewArgs['ajax']['html'] = ob_get_clean();

		}

		die( json_encode($viewArgs['ajax']) );

	}


} // END class View