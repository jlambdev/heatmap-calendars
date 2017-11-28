<?php

	/**
	 * 	HASH
	 *
	 *  Create hashes of passwords
	 */
	class Hash {

		/**
		 * 	MAKE A HASH OF A PASSWORD
		 */
		public static function make($string, $salt = '') {
			return hash('sha256', $string . $salt);
		}

		/**
		 * 	CREATE A SALT: Improves the security of a password hash
		 */
		public static function salt($length) {
			return bin2hex(mcrypt_create_iv(16));
		}

		/**
		 * 	GENERATE A UNIQUE HASH
		 */
		public static function unique() {
			return self::make(uniqid());
		}

	}
