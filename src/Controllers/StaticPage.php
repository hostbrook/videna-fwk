<?php
// Videna Framework
// File: /Videns/Controllers/StaticPage.php
// Desc: Pre-cooked Static Page controller

namespace Videna\Controllers;

use \Videna\Core\Log;
use \Videna\Core\User;
use \Videna\Core\Router;
use \Videna\Core\Config;
use \Videna\Core\View;
use \Videna\Core\Lang;

/**
 * Class to maintain Static Page requests  
 */
class StaticPage extends \Videna\Core\Controller {

	

	protected $user;   // <- Array of user data


	/**
	 * Index action is a default action
	 */
	public function actionIndex() {

	}


	/**
	 * Action to show the error page
	 * This methot is triggered if:
	 * - injection warning @Router 
	 * - Requested Class or Method not found in App class
	 * - redirection from action if error needs to be shown
	 */
	public function actionError($errNr = false) {

		if ($errNr) {
			Router::$action =  Config::get('error action');
			Router::$response = $errNr;
		}

		Router::$view = Config::get('default controller'). '/'. Config::get('error view');

		// Check if Error view file exists.
		if ( !is_file( PATH_VIEWS . Router::$view  .'.php' ) ) {

			Log::add([ 
				'FATAL Error: The Error page not found.',
				'Requested URI' . htmlspecialchars( URL_ABS . $_SERVER['REQUEST_URI'] ),
				'FATAL Error: The Error page not found.'
			]);
			
		}

	}


	/**
	 * Filter "before" each action
	 */
	protected function before() {

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

	}


  /**
	 * Filter "after" each action
	 */
	protected function after() {

		// Check if view file exists. If not -show 404 page.
		if ( !is_file( PATH_VIEWS . Router::$view  .'.php' ) ) $this->actionError(404);

		View::set([
			'user' => $this->user,
			'title' => $this->getMeta('title'),
		  'description' => $this->getMeta('description'),
		]);
		
		\Videna\Core\View::render();
	
	}


	/**
	 * Get title (for the <title> tag) from language file
	 */
	protected function getMeta($meta) {
		
		if (Router::$action == 'error' ) {
			$title = $meta .' response '. Router::$response;
			return View::get('_')[$title] !== null ? View::get('_')[$title] : 'Unknown';
		}

		$title = $meta .' '. Router::$view;	
		if ( View::get('_')[$title] === null ) {
			$title = $meta .' '. Config::get('default controller'). '/' . Config::get('default view');
			return View::get('_')[$title] !== null ? View::get('_')[$title] : '';
		}

		return View::get('_')[$title];

	}


} // END class StaticPage 