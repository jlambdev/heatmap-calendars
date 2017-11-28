<?php

	/**
	 * 	SESSION
	 *
	 * 	Create, check the existence of, return and delete sessions
	 */
	class Session {

		/**
		 * 	CHECK IF SESSION EXISTS
		 */
		public static function exists($name) {
			return (isset($_SESSION[$name])) ? true : false;
		}

		/**
		 * 	CREATE A NEW SESSION
		 */
		public static function put($name, $value) {
			return $_SESSION[$name] = $value;
		}

		/**
		 * 	RETURN A SPECIFIC SESSION
		 */
		public static function get($name) {
			return $_SESSION[$name];
		}

		/**
		 * 	DELETE A SESSION
		 */
		public static function delete($name) {
			if(self::exists($name)) {
				unset($_SESSION[$name]);
			}
		}
	}
