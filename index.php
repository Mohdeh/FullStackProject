<?php
require_once 'init.php';

try{
  $function_name = $_REQUEST['data'];
  //user is privd 
  $response_object = new $function_name;
}
catch(Exception $e){
    http_response_code(404);
    die();
}
?>
