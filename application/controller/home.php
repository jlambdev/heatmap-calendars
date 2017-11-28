<?php

/*
 * 	Home Controller
 */
class Home extends Controller
{
  /*
   * 	PAGE: index
   */
  public function index() {

      // load views & pass resources
      Functions::render(get_class(), __FUNCTION__);
  }

	/*
   * 	PAGE: login
   */
  public function login() {

  	// prepare array for user notices if they occur
  	$resources = array();

  	// check for input
  	if(Input::exists()) {

  		// check token
  		if(Token::check(Input::get('token'))) {

  			// attempt to log the user in
  			$action = $this->model->logUserIn();

  			// if successful, redirect user to home page
  			if ($action === true) {

  				Redirect::to(URL);

  			} else {

  				// otherwise prepare resources with a list of actions required
  				$resources['errors'] = $action;
  			}
  		}
  	}

	  // load views & pass resources
    Functions::render(get_class(), __FUNCTION__, $resources);
  }

	/*
   * 	ACTION: log user out
   */
  public function logout() {

    // get current user, log them out and redirect to the home page
		$user = new User();
		$user->logout();
		Redirect::to(URL);
  }

}
