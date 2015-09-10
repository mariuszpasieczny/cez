<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ALL|E_STRICT);
set_time_limit(3600);
ini_set('memory_limit', '3000M');
date_default_timezone_set('Europe/London');
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname($_SERVER["SCRIPT_FILENAME"]) . '/../application'));
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
set_include_path('.' 
   . PATH_SEPARATOR . APPLICATION_PATH . '/../library'
   . PATH_SEPARATOR . './application/models/'
   . PATH_SEPARATOR . get_include_path());
//var_dump($_SERVER);exit;
if($_SERVER['SERVER_PORT'] != '8008') {
    //header("Location: http://{$_SERVER['HTTP_HOST']}:8008{$_SERVER['REQUEST_URI']}");
};

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()
            ->run();