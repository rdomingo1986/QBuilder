<?php
class SQLClass {

  protected $_link = null;

  function __construct(DBConfig $conn) {
    $this->connect($conn);
  }

  public function connect(DBConfig $conn) {
    if($this->_link != null) {
      $this->disconnect();
    }
    
    $this->_link = new mysqli($conn->host, $conn->user, $conn->pass, $conn->dbname);
    
    return $this->_link;
  }

  public function query($rawQuery){
    $query = $this->_link->query($rawQuery);
    return !$query ? false : $query;
  }

  public function clean($rawQuery) {
    if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) { $rawQuery = stripslashes($rawQuery); }
    return mysqli_real_escape_string($rawQuery);
  }

  public function fetch_assoc($resultSet) { return $resultSet->fetch_assoc(); }

  public function free_result($resultSet){ $resultSet->free_result(); }

  public function count_rows($resultSet){ return $resultSet->num_rows; }
  
  // public function fetch_row($resultSet){ return $resultSet->fetch_row(); }

  public function disconnect() { 
    mysqli_close($this->_link);
    $this->_link = null;
  }
}
?>