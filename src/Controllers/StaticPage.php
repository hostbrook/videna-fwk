<?php
// Videna Framework
// File: /Videns/Controllers/StaticPage.php
// Desc: Pre-cooked Static Page controller

namespace Videna\Controllers;

use \Videna\Core\Log;
use \Videna\Core\User;
use \Videna\Core\Router;
use \Videna\Core\Config;

/**
 * Class to maintain Static Page requests  
 */
class StaticPage extends \Videna\Core\Controller {

	protected $viewArgs;


	/**
	 * Index action is a default action
	 */
	public function actionIndex() {

	}


	/**
	 * Action for output of error page
	 * 
	 * This methot is triggered if:
	 * - injection warning @Router 
	 * - Requested Class or Method not found in App class
	 * - redirection from action if error needs to be shown
	 */
	public function actionError() {

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

	}


  /**
	 * Filter "after" each action
	 */
	protected function after() {

		// Check if view file exists. If not -show 404 page.
		if ( !is_file( PATH_VIEWS . Router::$view  .'.php' ) ) {

			Router::$action =  Config::get('error action');
			Router::$response = 404;
			
			$this->actionError();

		}

		$this->viewArgs['_'] = $this->lang->langArray;
		$this->viewArgs['user'] = $this->user;

		$this->viewArgs['title'] = $this->getTitle();
		$this->viewArgs['description'] = $this->getDescription();		
		$this->viewArgs['lang'] = $this->lang->getCode();
		
		\Videna\Core\View::render($this->viewArgs);
	
	}


	/**
	 * Get title (for the <title> tag) from language file
	 */
	protected function getTitle() {
		
		if (Router::$action == 'error' ) {
			$title = 'title response ' . Router::$response;
			return isset($this->lang->langArray[$title]) ? $this->lang->langArray[$title] : 'Unknown';
		}

		$title = 'title ' . Router::$view;	
		if ( !isset($this->lang->langArray[$title]) ) {
			$title = 'title ' . Config::get('default controller'). '/' . Config::get('default view');
			return isset($this->lang->langArray[$title]) ? $this->lang->langArray[$title] : '';
		}

		return $this->lang->langArray[ $title ];

	}


	/**
	 * Get description (for the <description> tag) from language file
	 */ 
	protected function getDescription() {

		if ( Router::$action == 'error' ) {
			$description = 'description response ' . Router::$response;
			return isset($this->lang->langArray[$description]) ? $this->lang->langArray[$description] : 'Unknown error is occurred.';
		}
		
		$description = 'description ' . Router::$view;	
		if ( !isset($this->lang->langArray[$description]) ) {
			$description = 'description ' . Config::get('default controller') . '/' . Config::get('default view');
			return isset($this->lang->langArray[$description]) ? $this->lang->langArray[$description] : '';
		}	

		return 	$this->lang->langArray[ $description ];

	}


} // END class StaticPage 