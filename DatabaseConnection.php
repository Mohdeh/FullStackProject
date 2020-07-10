<?php
/*
Relies on global config file to set connection global variables
*/
class DatabaseConnection {

  private $conn;
  private $env;

  public function __construct() {
    global $data_source;
    $this->env = $data_source;
    $this->open_conn();
  }

  public function __destruct() {
    $this->conn->close();
  }


  private function open_conn() {
    global $db_dev_host, $db_dev_user, $db_dev_pass, $db_live_host, $db_live_user, $db_live_pass;
    if ($this->env  == 'live') {
      $this->conn = new mysqli($db_live_host, $db_live_user, $db_live_pass);
    } else {
      $this->conn = new mysqli($db_dev_host, $db_dev_user, $db_dev_pass);
    }
    if ($this->conn->connect_errno) {
      printf("Connect failed: %s\n", $this->conn->connect_error);
      exit();
    }
  }

  public function reopen_conn() {
    $this->conn->close();
    $this->open_conn();
  }

  public function select_db($db) {
    $this->conn->select_db($db);
  }


  public function error() {
    return $this->conn->error;
  }

  public function insert_id() {
    return $this->conn->insert_id;
  }

  public function affected_rows() {
    return $this->conn->affected_rows;
  }

  public function query($query, $db = null) {
    if (isset($db)){
        $this->conn->select_db($db);
    }
    $result = $this->conn->query($query);
    if (!$result){
        die($this->conn->error);
    }
    return $result;
  }

  public function real_escape_string($string) {
    return $this->conn->real_escape_string($string);
  }

  public function num_rows($result){
    return mysqli_num_rows($result);
  }
}

?>
