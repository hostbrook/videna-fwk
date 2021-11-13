<?php
// Videna Framework
// File: /Videna/Controllers/AjaxHandler.php
// Desc: Pre-cooked Ajax requests controller

namespace Videna\Controllers;

use \Videna\Core\Router;
use \Videna\Core\Config;

/**
 * Class to maintain Ajax requests  
 */

class AjaxHandler extends \Videna\Core\Controller {

	protected $viewArgs;


	/**
	 * Default action - if action is missed at ajax request
	 */
	public function actionIndex(){

		$this->viewArgs['ajax']['response'] = 404;
		$this->viewArgs['ajax']['status'] = $this->lang->langArray['title response 404'];

		$this->viewArgs['ajax']['text'] = 'Action/Method not found in class \'' . Router::$controller .'\'';
		$this->viewArgs['ajax']['html'] = '<p>Action/Method not found in class \''. Router::$controller .'\'</p>';

	}


	/**
	 * Action for output of error message
	 * 
	 * This methot is triggered if:
	 * - injection warning @Router 
	 * - redirection from action if error needs to be shown
	 */
	public function actionError(){

		$this->viewArgs['ajax']['response'] = Router::$response;
		$this->viewArgs['ajax']['status'] = $this->lang->langArray['title response ' . Router::$response];

		$description = 'description response ' . Router::$response;
		$this->viewArgs['ajax']['text'] = isset($this->lang->langArray[$description]) ? $this->lang->langArray[$description] : 'Unknown error is occurred.';
		$this->viewArgs['ajax']['html'] = '<p>'.isset($this->lang->langArray[$description]) ? $this->lang->langArray[$description] : 'Unknown error is occurred.'.'</p>';

	}

	/**
	 * Filter before the each action
	 */
	protected function before() {

		Router::$view = false;

		if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
			http_response_code(403);
			die("Access denied.");
		}

		$this->viewArgs['ajax']['response'] = Router::$response;
		$this->viewArgs['ajax']['status'] = $this->lang->langArray['title response ' . Router::$response];

	}

  /**
	 * Filter "after" each action
	 */
	protected function after() {

		if ( Router::$view ) {

			$this->viewArgs['_'] = $this->lang->langArray;			
			$this->viewArgs['lang'] = $this->lang->getCode();

		}

		\Videna\Core\View::jsonRender($this->viewArgs);
	
	}

} // END class AjaxHandler 