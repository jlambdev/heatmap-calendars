<?php

	// enable sessions
	session_start();

	// enable error reporting and allow errors to be displayed
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	// define URL info: public folder, protocol, domain, sub folder and URL index path
  define('URL_PROTOCOL', 'http://');
  define('URL_DOMAIN', $_SERVER['HTTP_HOST']);
  define('URL_SUB_FOLDER', 'dvp');
  define('URL_INDEX_FILE', 'index.php' . '/');

  // the final URLs, constructed with the elements above
  if (defined('URL_SUB_FOLDER')) {
      define('URL', URL_PROTOCOL . URL_DOMAIN . '/' . URL_SUB_FOLDER . '/');
      define('URL_WITH_INDEX_FILE', URL_PROTOCOL . URL_DOMAIN . '/' . URL_SUB_FOLDER . '/' . URL_INDEX_FILE);
  } else {
      define('URL', URL_PROTOCOL . URL_DOMAIN . '/');
      define('URL_WITH_INDEX_FILE', URL_PROTOCOL . URL_DOMAIN . '/' . URL_INDEX_FILE);
  }

	// define database connection info, cookie values and session values
	$GLOBALS['config'] = array(
		'mysql' => array(
			'host' => 'localhost',
			'username' => 'root',
			'password' => 'magma2185top',
			'db' => 'dvp_14'
		),
		'remember' => array(
			'cookie_name' => 'hash',
			'cookie_expiry' => 604800
		),
		'session' => array(
			'session_name' => 'user',
			'token_name' => 'token'
		)
	);

	// auto-load all helper classes in libs directory
	spl_autoload_register(function($class) {
		require APP . 'libs/' . $class . '.php';
	});

	// if cookie exists and user session does not exist
	if (Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))) {

		// grab cookie hash and check within the database
		$hash = Cookie::get(Config::get('remember/cookie_name'));
		$hashCheck = DB::getInstance()->query(
			"SELECT * FROM `sys_users_session` WHERE `hash` = '{$hash}';"
		)->results();

		if(empty($hashCheck)) {

			// log the user in
			$user = new User($hashCheck->first()->user_id);
			$user->login();

		}
	}
