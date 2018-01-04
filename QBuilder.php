<?php
require_once 'SQLClass.php';

class QBuilder {
  
  protected $_rawQuery;
  private $_resultSet;


  function __construct($rawQuery = '') {
    $this->_rawQuery = $rawQuery;
    $this->_resultSet = null;
  }

  public function setRawQuery($rawQuery) { $this->_rawQuery = $rawQuery; }

  public function getRawQuery() { return $this->_rawQuery; }

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

  public function where($column, $condition, $value) { // colocar que si se envia un solo parametro y es array se metan varios where and
    $argsQty = count(func_get_args()); // que condition sea mandatorio y que pueda escribir un whera raw
    if(strpos($this->_rawQuery, 'WHERE') === false) {
      $sqlWord = 'WHERE ';
    } else {
      if(substr(trim($this->_rawQuery), -1) != '(') {
        $sqlWord = 'AND ';
      }
    }
    if($argsQty === 2) {
      $value = $condition;
      $this->_rawQuery .= $sqlWord . $column . ' = "'. $value .'" ';
    } else if($argsQty === 3) {
      $alloweds = ['=', '<>', '!=', '<', '<=', '>', '>=', 'LIKE', 'NOT LIKE'];
      if(!array_search($condition, $alloweds, true)) {
        throw new Exception();
      }
      $this->_rawQuery .= $sqlWord . $column . ' ' . $condition . ' "'. $value .'" ';
    } else {
      throw new Exception();
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
      if(!array_search($condition, $alloweds, true)) {
        // throw new Exception();
      }
      $this->_rawQuery .= $sqlWord . $column . ' ' . $condition . ' "'. $value .'" ';
    } else {
      // throw new Exception();
    }
    return $this;
  }

  public function orderBy() {
    return $this;
  }

  public function limit($limit = 1, $offset = 0) {
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
      if(!array_search($joinType, $alloweds, true)) {
        // throw new Exception();
      } else {
        $joinType .= ' '; 
      }
    }
    $this->_rawQuery .= $joinType . 'JOIN ' . $table . ' ON ' . $condition . ' ';
    return $this;
  }

  public function get($table = '') {
    var_dump(strpos($this->_rawQuery, 'FROM'));
    $tableExists = trim($table) !== '';
    if($tableExists && strpos($this->_rawQuery, 'FROM') !== false) {
      throw new Exception('1');
    }
    if($tableExists && trim($this->_rawQuery) === '') {
      $this->_rawQuery = 'SELECT * FROM ' . $table;
    } else if($tableExists && strpos($this->_rawQuery, 'SELECT') !== false) {
      $this->_rawQuery .= 'FROM ' . $table;
    } else if(!$tableExists && trim($this->_rawQuery) === '') {
      throw new Exception('2');
    }
    //hacer el query de la consulta en base de datos
    return $this;
  }

  public function result() {
    return $this->_resultSet;
  }

  public function row() {
    return $this;
  }

  function __destruct() { }
}
?>