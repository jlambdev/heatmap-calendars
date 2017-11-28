<?php

	/**
	 *	USER
	 *
	 * 	Wrapper class for users: set sessions, allow logging in/out, change password or other details.
	 */
	class User {

		// store connection to database as private instance variable
		private $_db,
				$_data,
				$_sessionName,
				$_cookieName,
				$_isLoggedIn;

		/**
		 * 	CONSTRUCTOR: creating a new User object looks for the current session and cookie name,
		 * 	which can be used for checking details for the current user logged in
		 */
		public function __construct($user = null) {

			// connect to DB by getting reference to instance
			$this->_db = DB::getInstance();

			// set the user's session & cookie
			$this->_sessionName = Config::get('session/session_name');
			$this->_cookieName = Config::get('remember/cookie_name');

			// if the user has not yet been defined
			if(!$user) {
				if(Session::exists($this->_sessionName)) {
					$user = Session::get($this->_sessionName);

					if($this->find($user)) {

						// set a flag to indicate that the user is logged in
						$this->_isLoggedIn = true;
					} else {
						// process logout
					}

				}
			} else {

				// if the user has been defined
				$this->find($user);
			}

		}

		/**
		 *	UPDATE USER DETAILS
		 */
		public function update($property, $value, $id = null) {

			// mentioned here that administrator panel activity could be supported, i.e. change other users' details
			if(!$id && $this->isLoggedIn()) {
				$id = $this->data()->user_id;
			}

			$this->_db->query(
				"UPDATE `sys_users` SET `{$property}` = '{$value}' WHERE `user_id` = '{$id}'"
			);

		}

		/**
		 *	CREATE NEW USER
		 */
		public function create($username, $password, $salt, $name, $group) {

			$this->_db->query(
				"INSERT INTO `sys_users` (`username`, `password`, `salt`, `name`, `group_id`)
				VALUES ('{$username}', '{$password}', '{$salt}', '{$name}', '{$group}');"
			);
		}

		/**
		 *	FIND USER
		 */
		public function find($user = null) {

			if($user) {
				$field = (is_numeric($user)) ? 'user_id' : 'username';
				$data = $this->_db->query(
					"SELECT * FROM `sys_users` WHERE `{$field}` = '{$user}'"
				);

				// if the user does exist...
				if($data->count()) {
					$this->_data = $data->first();
					return true;
				}
			}

			return false;

		}

		/**
		 *	LOGIN USER IN
		 */
		public function login($username = null, $password = null, $remember = false) {

			// check if existing user is active & no password or username has been provided (Cookie login)
			if(!$username && !$password && $this->exists()) {

				// log user in
				Session::put($this->_sessionName, $this->data()->user_id);

			} else {

				// check if the user exists
				$user = $this->find($username);
				if($user) {

					// check password
					if($this->data()->password === Hash::make($password, $this->data()->salt)) {

						// set user session if password entered matches
						Session::put($this->_sessionName, $this->data()->user_id);

						// check if hash is stored in the database
						if($remember) {
							$hash = Hash::unique();
							$hashCheck = $this->_db->query(
								"SELECT * FROM `sys_users_session` WHERE `user_id` = '{$this->data()->user_id}'"
							)->results();

							// if it is not in the database, insert a hash into the database (limit of one record per user)
							if(empty($hashCheck)) {

								$this->_db->query(
									"INSERT INTO `sys_users_session` (`user_id`, `hash`)
									VALUES ('{$this->data()->user_id}', '{$hash}')"
								);
							} else {
								$hash = $hashCheck->first()->hash;
							}

							Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
						}

						return true;
					}

				}
			}

			return false;

		}

		/**
		 *	CHECK USER PERMISSIONS
		 */
		public function hasPermission($key) {
			$group = $this->_db->query(
				"SELECT * FROM `sys_groups` WHERE `group_id` = '{$this->data()->group_id}'"
			);

			// extract permissions
			if($group->count()) {
				$permissions = json_decode($group->first()->permissions, true);

				// decode from JSON (2x '=' deliberate)
				if($permissions[$key] == true) {
					return true;
				}
			}
			return false;

		}

		/**
		 *	CHECK USER EXISTS
		 */
		public function exists() {
			return (!empty($this->_data)) ? true : false;
		}

		/**
		 *	LOG USER OUT: delete session and cookie
		 */
		public function logout() {

			// remove hash from the database
			$this->_db->query(
				"DELETE FROM `sys_users_session` WHERE `user_id` = '{$this->data()->user_id}'"
			);

			Session::delete($this->_sessionName);
			if (Cookie::exists($this->_cookieName)) {
				Cookie::delete($this->_cookieName);
			}
		}

		/**
		 *	RETURN DATA ABOUT USER
		 */
		public function data() {
			return $this->_data;
		}

		/**
		 *	RETURN LOGIN STATUS
		 */
		public function isLoggedIn() {
			return $this->_isLoggedIn;
		}
	}
