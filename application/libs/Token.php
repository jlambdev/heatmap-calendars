<?php

	/**
	 * 	TOKEN
	 *
	 * 	Protect against cross-site request forgery attacks
	 */
	class Token {

		/**
		 * 	GENERATE A UNIQUE TOKEN
		 */
		public static function generate() {
			return Session::put(Config::get('session/token_name'), md5(uniqid()));
		}

		/**
		 * 	CHECK A TOKEN
		 */
		public static function check($token) {
			$tokenName = Config::get('session/token_name');

			// check if the token provided by function is equal to session stored by the user
			if(Session::exists($tokenName) && $token === Session::get($tokenName)) {
				Session::delete($tokenName);
				return true;
			}

			return false;
		}

	}
