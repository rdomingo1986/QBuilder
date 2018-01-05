<?php
require_once './QBuilder/config/DBConfig.php';
require_once 'Functions.php';

class QBuilder {
  
  private $_rawQuery;
  private $_resultSet;
  private $_numRows;
  private $_dbIndex;
  private $_insertId;
  private $_link;

  function __construct($dbIndex = 'default') {
    $this->_dbIndex = $dbIndex;
    $this->_resultSet = $this->_insertId = $this->_numRows = null;
    $this->cleanRawQuery();
  }

  public function connect(DBConfig $config) {
    if($this->_link != null) {
      $this->disconnect();
    }
    $this->_link = new Mysqli($config->host, $config->user, $config->pass, $config->dbname);
  }

  public function disconnect() {
    if($this->_link != null) {
      $this->_link->close();
      $this->_link = null;
    }
  }

  public function setRawQuery($rawQuery) { $this->_rawQuery = $rawQuery; }

  public function getRawQuery() { return $this->_rawQuery; }

  public function cleanRawQuery() { 
    $this->_rawQuery = '';
    return $this;
  }

  public function cleanStatement($rawQuery) {
    if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) { $rawQuery = stripslashes($rawQuery); }
    return $this->_link->escape_string($rawQuery);
  }

  public function openGroup($before = 'AND') {
    if(strpos($this->_rawQuery, 'WHERE') !== false) {
      $this->_rawQuery .= $before . ' ';
    }
    $this->_rawQuery .= '(';
    return $this;
  }

  public function closeGroup() {
    $this->_rawQuery = trim($this->_rawQuery) . ') ';
    return $this;
  }

  public function select($select = '*') {
    $this->_rawQuery .= 'SELECT ' . $select . ' ';
    return $this;
  }

  public function from($table) {
    $this->_rawQuery .= 'FROM ' . $table . ' ';
    return $this;
  }

  public function where() {
    //falta que el segundo o tercer parametro contenga algo parecido a una consulta sql evitar el uso de doble comillas y auto colocar los parentecis
    $args = func_get_args();
    $argsQty = count($args);
    $sqlWord = '';
    
    if($argsQty === 1) {


      if(gettype($args[0]) === 'array') {
        $conditions = $args[0];

        foreach($conditions AS $condition) {
          if(gettype($condition) === 'array') {


            if(count($condition) === 2) {

              $this->where($condition[0], $condition[1]);

            } else if(count($condition) === 3) {

              $this->where($condition[0], $condition[1], $condition[2]);

            } else {
              errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
            }


          } else if(gettype($condition) === 'string') {
            $this->where($condition);
          } else {
            errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
          }
        }


      } else if(gettype($args[0]) === 'string') {
        
        $value = trim($args[0]);
        

        if(strpos($this->_rawQuery, 'WHERE') === false) {
          $sqlWord = 'WHERE ';
          
        } else {
          if(substr(trim($this->_rawQuery), -1) != '(') {
            $sqlWord = 'AND ';
          }
          
        }

        $this->_rawQuery .= $sqlWord . $value . ' ';
        

      } else {
        errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
      }


    
    } else if($argsQty === 2 || $argsQty === 3){
      
      if(strpos($this->_rawQuery, 'WHERE') === false) {
        $sqlWord = 'WHERE ';
      } else {
        if(substr(trim($this->_rawQuery), -1) != '(') {
          $sqlWord = 'AND ';
        }
      }
      
      if($argsQty === 2) {
        $column = $args[0];
        $condition = '=';
        $value = $args[1];
      } else {
        $column = $args[0];
        $condition = $args[1];
        $value = $args[2];

        $alloweds = ['=', '<>', '!=', '<', '<=', '>', '>=', 'LIKE', 'NOT LIKE'];
        if(!paramExistsInAlloweds($condition, $alloweds)) {
          errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
        }
      }
      $this->_rawQuery .= $sqlWord . $column . ' ' . $condition . ' "'. $value .'" ';
      

    } else {
      errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
    }
    return $this;
  }

  public function orWhere() {
    //falta que el segundo o tercer parametro contenga algo parecido a una consulta sql evitar el uso de doble comillas y auto colocar los parentecis
    $args = func_get_args();
    $argsQty = count($args);
    $sqlWord = '';
    
    
    if($argsQty === 1) {


      if(gettype($args[0]) === 'array') {
        $conditions = $args[0];
        
        foreach($conditions AS $condition) {
          
          if(gettype($condition) === 'array') {


            if(count($condition) === 2) {

              $this->orWhere($condition[0], $condition[1]);

            } else if(count($condition) === 3) {

              $this->orWhere($condition[0], $condition[1], $condition[2]);

            } else {
              errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
            }


          } else if(gettype($condition) === 'string') {
            
            $this->orWhere($condition);
          } else {
            errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
          }
        }


      } else if(gettype($args[0]) === 'string') {
        
        $value = trim($args[0]);
        

        if(strpos($this->_rawQuery, 'WHERE') === false) {
          $sqlWord = 'WHERE ';
          
        } else {
          if(substr(trim($this->_rawQuery), -1) != '(') {
            $sqlWord = 'OR ';
          }
          
        }

        $this->_rawQuery .= $sqlWord . $value . ' ';
        

      } else {
        errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
      }


    
    } else if($argsQty === 2 || $argsQty === 3){
      
      if(strpos($this->_rawQuery, 'WHERE') === false) {
        $sqlWord = 'WHERE ';
      } else {
        if(substr(trim($this->_rawQuery), -1) != '(') {
          $sqlWord = 'OR ';
        }
      }
      
      if($argsQty === 2) {
        $column = $args[0];
        $condition = '=';
        $value = $args[1];
      } else {
        $column = $args[0];
        $condition = $args[1];
        $value = $args[2];

        $alloweds = ['=', '<>', '!=', '<', '<=', '>', '>=', 'LIKE', 'NOT LIKE'];
        if(!paramExistsInAlloweds($condition, $alloweds)) {
          errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
        }
      }
      $this->_rawQuery .= $sqlWord . $column . ' ' . $condition . ' "'. $value .'" ';
      

    } else {
      errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
    }
    return $this;
  }

  public function orderBy($column, $order) {
    $alloweds = ['ASC', 'DESC'];
    if(!paramExistsInAlloweds($order, $alloweds)) {
      errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
    }
    if(strpos($this->_rawQuery, 'ORDER BY') === false) {
      $this->_rawQuery .= 'ORDER BY ' . $column . ' ' . $order . ' ';
    } else {
      $this->_rawQuery = trim($this->_rawQuery);
      $this->_rawQuery .= ', ' . $column . ' ' . $order . ' ';
    }
    return $this;
  }

  public function limit($limit = 1, $offset = 0) {
    if(gettype((int)$limit) != 'integer' || gettype((int)$offset) != 'integer') {
      errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
    }
    if($offset === 0) {
      $this->_rawQuery .= 'LIMIT ' . $limit. ' ';
    } else {
      $this->_rawQuery .= 'LIMIT ' . $offset . ', ' . $limit. ' ';
    }
    return $this;
  }

  public function join($table, $condition, $joinType = '') {
    if(trim($joinType) !== '') {
      $alloweds = ['INNER', 'LEFT', 'RIGHT', 'FULL'];
      if(!paramExistsInAlloweds($joinType, $alloweds)) {
        errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
      } else {
        $joinType .= ' '; 
      }
    }
    $this->_rawQuery .= $joinType . 'JOIN ' . $table . ' ON ' . $condition . ' ';
    return $this;
  }

  public function execute() {
    $this->_numRows = null;
    $this->connect(new DBConfig($this->_dbIndex));
    $rawQuery = $this->_rawQuery = trim($this->_rawQuery);
    $this->_resultSet = $this->_link->query($rawQuery);
    if(strpos($rawQuery, 'INSERT') !== false) {
      $this->_insertId = $this->_link->insert_id;
    }
    $this->cleanRawQuery();
    $this->disconnect();
    if(strpos($rawQuery, 'SELECT') !== false) {
      return $this;  
    }
    return $this->_resultSet;
  }

  public function result($serialized = false) {
    $willSerialized = $serialized != false;
    if($willSerialized) {
      $alloweds = [false, 'JSON', 'XML'];
      if(!paramExistsInAlloweds($serialized, $alloweds)) {
        errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
      }
    }
    
    $arr = array();
    $arr[] =$this->_resultSet->fetch_assoc();
    if($arr[0] != null) {
      $this->_numRows = $this->_resultSet->num_rows;
      while($row =$this->_resultSet->fetch_assoc()) {
        $arr[] = $row;
      }
    } else {
      $arr = [];
    }

    if($willSerialized) {
      if($serialized == 'JSON') {
        $arr = json_encode($arr);
      } else {
        $arr = xmlrpc_encode($arr);
      }
    }
    

    $this->_resultSet->free_result();
    return $arr;
  }

  public function row($serialized = false) {
    $willSerialized = $serialized != false;
    if($willSerialized) {
      $alloweds = [false, 'JSON', 'XML'];
      if(!paramExistsInAlloweds($serialized, $alloweds)) {
        errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
      }
    }

    
    $row =$this->_resultSet->fetch_assoc();
    if($row != null) {
      $this->_numRows = 1;
    }

    if($willSerialized) {
      if($serialized == 'JSON') {
        $row = json_encode($row);
      } else {
        $row = xmlrpc_encode($row);
      }
    }

    $this->_resultSet->free_result();
    return $row;
  }

  public function numNows() {
    return $this->_numRows;
  }

  public function insertId() {
    return $this->_insertId;
  }

  public function insert($table, $data) { //last insert
    $columns = '';
    $values = '';
    foreach($data AS $key => $value) {
      $columns .= $key . ', ';
      $values .= '"' . $value . '", ';
    }
    $columns = rtrim($columns, ', ');
    $values = rtrim($values, ', ');
    $this->_rawQuery .= 'INSERT INTO ' . $table . ' (' . $columns . ') VALUES (' . $values . ')';
    return $this;
  }

  public function update($table, $data) {
    $sets = '';
    foreach($data AS $key => $value) {
      $sets .= $key . ' = "' . $value . '", ';
    }
    $sets = rtrim($sets, ', ');
    $this->_rawQuery .= 'UPDATE ' . $table . ' SET ' . $sets . ' ';
    return $this;
  }

  public function delete($table) {
    $this->_rawQuery .= 'DELETE FROM ' . $table . ' ';
    return $this;
  }

  function __destruct() { 
    $this->disconnect();
  }
}