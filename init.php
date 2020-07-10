<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$_SESSION['PHPSESSID'] = session_id();

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
define('__SITE_PATH', '/home/www/sites/secure');
define('__ROOT_PATH', '/home/www/include');
define('__API_PATH',  '/home/www/sites/secure/api');

require_once __ROOT_PATH . '/co.php';
require_once __API_PATH . '/utils/functions.php';
//require functions as they're instantiated
function __autoload($class_name){
    $utils_fname = __API_PATH . '/utils/' . $class_name . '.php';
    $resp_fname = __API_PATH . '/src/' . $class_name . '.php';
    if (is_file($utils_fname)){
      require_once $utils_fname;
    }
    else{
      require_once $resp_fname;
    }
}



?>
