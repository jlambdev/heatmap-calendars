<?php

// define the path to the application folder
define('APP', 'application/');

// load the initialisation file
require APP . 'core/init.php';

// start the application
$app = new Application();
