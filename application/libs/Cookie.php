<?php

	/**
	 *	COOKIE
	 *
	 * 	Allow users to store a cookie on their device so they do not need to repeatedly
	 * 	provide login credentials when they visit the site.
	 */
	class Cookie {

		/**
		 * 	CHECK IF A COOKIE EXISTS
		 */
		public static function exists($name) {

			// if the value exists as a cookie, return true, else return false
			return (isset($_COOKIE[$name])) ? true : false;
		}
		
		/**
		 * 	GET A COOKIE
		 */
		public static function get($name) {
			return $_COOKIE[$name];
		}

		/**
		 * STORE A COOKIE
		 */
		public static function put($name, $value, $expiry) {

			// set a cookie, providing a name, value and expiry time (based on config info)
			if(setcookie($name, $value, time() + $expiry, '/')) {
				return true;
			}

			// false if the operation failed
			return false;
		}

		/**
		 * 	DELETE A COOKIE
		 */
		public static function delete($name) {

			// delete a cookie by removing the value and expiring it
			self::put($name, '', time() - 1);
		}

	}
