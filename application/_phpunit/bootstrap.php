<?php

// declare DB connection info here
$GLOBALS['config'] = array(
  'mysql' => array(
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'magma2185top',
    'db' => 'dvp_v13'
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

include_once('AutoLoader.php');

// Register the directory to your include files
AutoLoader::registerDirectory('..' . DIRECTORY_SEPARATOR . 'controller');
AutoLoader::registerDirectory('..' . DIRECTORY_SEPARATOR . 'libs');
AutoLoader::registerDirectory('..' . DIRECTORY_SEPARATOR . 'model');

// print_r all classes autoloaded by AutoLoader
Autoloader::printClassNames();
