<?php

	/**
	 *	APPLICATION
	 *
	 * 	Determines which Controller and Action to use based on the URL.
	 */
	class Application
	{
		// instance variables for the requested controller, action (method) and additional URL parameters
		private $url_controller = null;
		private $url_action = null;
		private $url_params = array();

		/**
		 * 	"START THE APPLICATION"
		 * 	Analyze the URL elements and calls the according controller/method or the fallback
		 */
		public function __construct()
		{
			// create array with URL parts in $url
			$this->getUrlWithoutModRewrite();

			// get the current user & check if logged in
			$user = new User();
			if (!$user->isLoggedIn()) {

				require APP . 'controller/home.php';
				$page = new Home();
				$page->login();

			// if a Controller was not provided, load the index of the Home controller
			} else if (!$this->url_controller) {

				// check for controller: no controller given ? then load start-page
				require APP . 'controller/home.php';
				$page = new Home();
				$page->index();

			// otherwise check if the requested Controller exists
			} elseif (file_exists(APP . 'controller/' . $this->url_controller . '.php')) {

				// if so, then load this file and create this controller
				require APP . 'controller/' . $this->url_controller . '.php';
				$this->url_controller = new $this->url_controller();

				// check for the requested Action (method) in the Controller
				if (method_exists($this->url_controller, $this->url_action)) {

					// if additional URL parameters are provided
					if (!empty($this->url_params)) {

						// Call the method and pass arguments to it
						call_user_func_array(array($this->url_controller, $this->url_action), $this->url_params);

					} else {

						// If no parameters are given, just call the method without parameters
						$this->url_controller->{$this->url_action}();
					}

				} else {

					// if no Action was provided, load the Controller's index page
					if (strlen($this->url_action) == 0) {
						$this->url_controller->index();
					}
					else {

						// an invalid Action was requested - load the 404 page not found view
						Redirect::to(404);
					}
				}
			} else {

				// an invalid Controller was requested - load the 404 page not found view
				Redirect::to(404);
			}
		}

		/**
     * Get and split the URL
     */
    private function getUrlWithoutModRewrite() {
			
      // TODO the "" is weird
      // get URL ($_SERVER['REQUEST_URI'] gets everything after domain and domain ending), something like
      // array(6) { [0]=> string(0) "" [1]=> string(9) "index.php" [2]=> string(10) "controller" [3]=> string(6) "action" [4]=> string(6) "param1" [5]=> string(6) "param2" }
      // split on "/"
      $url = explode('/', $_SERVER['REQUEST_URI']);
      // also remove everything that's empty or "index.php", so the result is a cleaned array of URL parts, like
      // array(4) { [2]=> string(10) "controller" [3]=> string(6) "action" [4]=> string(6) "param1" [5]=> string(6) "param2" }
      $url = array_diff($url, array('', 'index.php'));
      // to keep things clean we reset the array keys, so we get something like
      // array(4) { [0]=> string(10) "controller" [1]=> string(6) "action" [2]=> string(6) "param1" [3]=> string(6) "param2" }
      $url = array_values($url);

      // if first element of our URL is the sub-folder (defined in config/config.php), then remove it from URL
      if (defined('URL_SUB_FOLDER') && !empty($url[0]) && $url[0] === URL_SUB_FOLDER) {
          // remove first element (that's obviously the sub-folder)
          unset($url[0]);
          // reset keys again
          $url = array_values($url);
      }

      // Put URL parts into according properties
      // By the way, the syntax here is just a short form of if/else, called "Ternary Operators"
      // @see http://davidwalsh.name/php-shorthand-if-else-ternary-operators
      $this->url_controller = isset($url[0]) ? $url[0] : null;
      $this->url_action = isset($url[1]) ? $url[1] : null;

      // Remove controller and action from the split URL
      unset($url[0], $url[1]);

      // Rebase array keys and store the URL params
      $this->url_params = array_values($url);

      // for debugging. uncomment this if you have problems with the URL
      //echo 'Controller: ' . $this->url_controller . '<br>';
      //echo 'Action: ' . $this->url_action . '<br>';
      //echo 'Parameters: ' . print_r($this->url_params, true) . '<br>';
    }
	}
