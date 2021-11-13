<?php
// Videna Framework
// File: /Videna/Core/App.php
// Desc: Application Controller class

namespace Videna\Core;


class App {

	public function __construct() {

		Config::init();
		Router::init();

	}


	public function execute($argv = false) {

		// DEFINING ROUTE PARAMETERS
		if ($argv === false) {
			// HTTP requests:
			Router::parse();
		}
		else {
			// Cron jobs:
			Router::$argv = $argv;
			Router::$controller = $argv[1];
		}
		
		// EXECUTE ACTION AT THE CONTROLLER
		$controller = 'App\\Controllers\\' . Router::$controller;
		
		if ( class_exists($controller) ) {

			$controller_object = new $controller();
			$action = Router::$action;
			
			if ( method_exists($controller_object, 'action'.$action) ) {

				$controller_object->$action();

			}
			else {
				// Method/Action doesn't exist

				Log::add([
					"Error: Method  '$action' not found in the controller '$controller'.",
					'URL: ' . htmlspecialchars( URL_ABS . $_SERVER['REQUEST_URI'] )
				]);

				if ( $argv === false) $this->showErrorPage("Method  '$action' not found in the controller '$controller'.");

			}

		}
		else {
			// Class/Controller doesn't exist

			if ( $argv !== false) {
				Log::add([
					"Error: Controller '$controller' not found.",
					'URL: ' . htmlspecialchars( URL_ABS . $_SERVER['REQUEST_URI'] )
				]);
			}
			else $this->showErrorPage("Controller '$controller' not found");

		}
		
	} // END execute()


 /**
	*  Redirect to Error Action if the requested controller (or action) does not exist.
	*  If the Error Controller (or action) does not exist - stop application with a fatal error.
	*/
	private function showErrorPage() {

		if ( Config::get('default controller') === null or Config::get('error action') === null ) {
			// Error Controller or Error Action isn't defined in the App config file

			$errorDescription = 'FATAL Error: Either Default Controller or Error Action isn\'t defined in the config file.';
			Log::add([
				$errorDescription,
				'URL request: ' . htmlspecialchars( URL_ABS . $_SERVER['REQUEST_URI'] )
			], $errorDescription );

		}

		Router::$controller = Config::get( 'default controller' );
		Router::$action = Config::get( 'error action' );
		Router::$response = 404;

		$controller = 'App\\Controllers\\' . Router::$controller;
	
		if ( !class_exists($controller) ) {
			// Error Controller doesn't exist in /App/Controllers/

			$errorDescription = "FATAL Error: The Error Controller '$controller' defined in App config file but doesn't exist in '/App/Controllers/'";
			Log::add([
				$errorDescription,
					'URL request' . htmlspecialchars( URL_ABS . $_SERVER['REQUEST_URI'] )
			], $errorDescription );

		}

		$controllerObject = new $controller();
		$action = Router::$action;
		
		if ( !method_exists($controllerObject, 'action'.$action) ) {
			// Error method doesn't exist in Error Controller

			$errorDescription = "The Error method '$action' is defined in App config file but not found in the defined Error controller '$controller'.";
			Log::add([
				$errorDescription,
				'URL' . htmlspecialchars( URL_ABS . $_SERVER['REQUEST_URI'] )
			], $errorDescription );

		}

		// Call Error Controller -> Error Action:
		$controllerObject->$action();

	} // END showError()



} //END class App