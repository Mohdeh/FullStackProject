<?php 
function user_is_logged_in(){
  return (isset($_SESSION['user_ID']) && $_SESSION['user_ID'] != "");
}
?>
