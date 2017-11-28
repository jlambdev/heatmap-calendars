<?php

/*
 * 	Settings Controller
 */
class Settings extends Controller
{
  /*
   * PAGE: index
   */
  public function index() {

    // load views
    Functions::render(get_class(), __FUNCTION__);
  }

  /*
   * PAGE: change password
   */
  public function password() {

    // check input and token
		if(Input::exists()) {

			if (Token::check(Input::get('token'))) {

				// attempt to register new user
				$action = $this->model->changeUserPassword();

				// if registration was successful, redirect to URL
				if($action === true) {

					Redirect::to(URL . 'settings');

				} else {

					// otherwise load up resources with actions required
					$resources = array(
						'errors' => $action
					);

					// render views with new errors
					Functions::render(get_class(), __FUNCTION__, $resources);
				}
			}

		} else {

			// load views as usual
			Functions::render(get_class(), __FUNCTION__);
		}
  }

  /*
   * PAGE: register new user
   */
  public function register() {

    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }

    // check input and token
		if(Input::exists()) {

			if (Token::check(Input::get('token'))) {

				// attempt to register new user
				$action = $this->model->registerNewUser();

				// if registration was successful, redirect to URL
				if($action === true) {

					Redirect::to(URL . 'settings');

				} else {

					// otherwise load up resources with actions required
					$resources = array(
						'errors' => $action
					);

					// render views with new errors
					Functions::render(get_class(), __FUNCTION__, $resources);
				}
			}

		} else {

			// load views as usual
			Functions::render(get_class(), __FUNCTION__);
		}
  }
}
