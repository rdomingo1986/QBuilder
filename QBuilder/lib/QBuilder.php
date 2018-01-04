<?php
require_once './QBuilder/config/DBConfig.php';
require_once 'Functions.php';
require_once 'SQLClass.php';

class QBuilder extends SQLClass {
  
  protected $_rawQuery;
  private $_resultSet;
  private $_numRows;
  private $_connectionName;

  function __construct($connectionName = 'default') {
    $this->_connectionName = $connectionName;
    $this->_rawQuery = $rawQuery;
    $this->_resultSet = null;
    $this->_numRows = -1;
  }

  public function setRawQuery($rawQuery) { $this->_rawQuery = $rawQuery; }

  public function getRawQuery() { return $this->_rawQuery; }

  public function cleanRawQuery() { 
    $this->_rawQuery = '';
    return $this;
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

  public function orWhere($column, $condition, $value) { // colocar que si se envia un solo parametro y es array se metan varios where or y que el primero no se le anteponga OR, solo aplica cuando esta dentro de un openGroup
    $argsQty = count(func_get_args());
    $sqlWord = 'OR ';
    if($argsQty === 2) {
      $value = $condition;
      $this->_rawQuery .= $sqlWord . $column . ' = "'. $value .'" ';
    } else if($argsQty === 3) {
      $alloweds = ['=', '<>', '!=', '<', '<=', '>', '>='];
      if(!paramExistsInAlloweds($condition, $alloweds)) {
        errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
      }
      $this->_rawQuery .= $sqlWord . $column . ' ' . $condition . ' "'. $value .'" ';
    } else {
      throw new Exception('2');
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

  public function get() {
    $this->connect(new DBConfig($this->_connectionName));
    $this->_rawQuery = trim($this->_rawQuery);
    $this->_resultSet = $this->query($this->_rawQuery);
    $this->cleanRawQuery();
    $this->disconnect();
    return $this;
  }

  public function result($serialized = false) {
    $willSerialized = $serialized != false;
    if($willSerialized) {
      $alloweds = [false, 'JSON', 'XML'];
      if(!paramExistsInAlloweds($serialized, $alloweds)) {
        errorMessageHandler('MALFORMED_SIGN', 'InvalidArgumentException');
      }
    }
    
    $this->_numRows = $this->count_rows($this->_resultSet);
    $arr = array();
    while($row = $this->fetch_assoc($this->_resultSet)) {
      $arr[] = $row;
    }

    if($willSerialized) {
      if($serialized == 'JSON') {
        $arr = json_encode($arr);
      } else {
        $arr = xmlrpc_encode($arr);
      }
    }

    $this->free_result($this->_resultSet);
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

    $this->_numRows = 1;
    $row = $this->fetch_assoc($this->_resultSet);

    if($willSerialized) {
      if($serialized == 'JSON') {
        $row = json_encode($row);
      } else {
        $row = xmlrpc_encode($row);
      }
    }
    $this->free_result($this->_resultSet);
    return $row;
  }

  public function num_rows() {
    return $this->_numRows;
  }

  function __destruct() { 
    if($this->_link != null) {
      $this->_link->disconnect();
    }
  }
}
?>