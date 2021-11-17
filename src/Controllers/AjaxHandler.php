<?php
// Videna Framework
// File: /Videna/Controllers/AjaxHandler.php
// Desc: Pre-cooked Ajax requests controller

namespace Videna\Controllers;

use \Videna\Core\Router;
use \Videna\Core\Config;
use \Videna\Core\View;
use \Videna\Core\Lang;
use \Videna\Core\User;

/**
 * Class to maintain Ajax requests  
 */

class AjaxHandler extends \Videna\Core\Controller {


	protected $user;   // <- Array of user data


	/**
	 * Default action, executes if action was missed in ajax request
	 */
	public function actionIndex(){

		View::set([
			'response' => 404,
		  'status' => View::get('title response 404'),

			'text' => 'Action/Method not found in class \'' . Router::$controller .'\'',
		  'html' => '<p>Action/Method not found in class \''. Router::$controller .'\'</p>'
		]);

	}


	/**
	 * Action for output of error message
	 * 
	 * This methot is triggered if:
	 * - injection warning @Router 
	 * - redirection from action if error needs to be shown
	 */
	public function actionError(){

		View::set([
			'response' => Router::$response,
			'status' => View::get('title response ' . Router::$response),
		]);

		$description = 'description response ' . Router::$response;
		View::set([
			'text' => isset($this->lang->langArray[$description]) ? $this->lang->langArray[$description] : 'Unknown error is occurred.',
			'html' => '<p>'.isset($this->lang->langArray[$description]) ? $this->lang->langArray[$description] : 'Unknown error is occurred.'.'</p>'
		]);

	}

	/**
	 * Filter before the each action
	 */
	protected function before() {

		if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
			http_response_code(403);
			die("Access denied.");
		}

		// Determine User's account type:
		$this->user = User::detect();

		// Check if user have preffered language:
		if ( $this->user['account'] > USR_UNREG and isset($this->user['lang'])) {
			$userLang = $this->user['lang'];
		}
		else $userLang = false;
		

		// Set language:
		$lang = new Lang($userLang);
		View::set([
			'_' => $lang->langArray,
			'lang' => $lang->getCode()
		]);

		// Prepare response:
		View::set([
			'response' => Router::$response,
			'status' => $lang->langArray['title response ' . Router::$response]
		]);

		Router::$view = false;

	}

  /**
	 * Filter "after" each action
	 */
	protected function after() {

		View::set(['user' => $this->user]);
		\Videna\Core\View::jsonRender();
	
	}

} // END class AjaxHandler 