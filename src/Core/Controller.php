<?php
// Videna Framework
// File: /Videna/Core/Controller.php
// Desc: Base controller class

namespace Videna\Core;

abstract class Controller {

	protected $lang;   // <- Object
	protected $user;   // <- Array


	public function __call($name, $args) {

		// Determine User's account type:
		$this->user = User::detect();

		// Check if user have preffered language:
		if ( $this->user['account'] > USR_UNREG and isset($this->user['lang'])) {
			$userLang = $this->user['lang'];
		}
		else $userLang = false;

		// Set language:
		$this->lang = new Lang($userLang);

		// Filter "before" - before action starts
		// At this filter you maybe want to change action.
		$this->before();

		// Set requested action:
		$method = 'action' . Router::$action;

		call_user_func_array([$this, $method], $args);

		// Filter "after" - after action is completed
		$this->after();

		// Finally send response to client:
		http_response_code(Router::$response);
		
	}

	abstract protected function actionIndex();

	abstract protected function actionError();

	// Filter "before" - before action starts
	abstract protected function before();

	// Filter "after" - after action is completed
	abstract protected function after();

} // END class Controller