<?php
class QBuilder {
  
  protected $_query = 'SELECT{{_SELECT_}}FROM{{_TABLE_}}{{_JOIN_}}WHERE{{_WHERE_}}{{_ORDER_BY_}}';
  protected $_select = array();
  protected $_where = array();
  protected $_join = array();


  function __construct() {}

  public function select($select = '*') {
    $this->_query .= 'SELECT ' . $select;
    return $this;
  }

  public function from($table) {
    $this->_query .= ' FROM ' . $table;
    return $this;
  }

  public function where() {
    $this->_where;
    return $this;
  }

  public function open_group($callback) {
    $this->_query .= '(';
    $callback($this);
    return $this;
  }

  public function close_group() {
    $this->_query .= ')';
    return $this;
  }

  public function queryString() {
    return $this->_query;
  }

  public function test() {
    echo func_get_args()[1];
  }

  function __destruct() {}
}

$db = new QBuilder();
$db->select()
  ->from('users')
  ->where('')
  ->open_group(function ($query) {
    $query->where();
  })
  ->close_group();

var_dump($db->test(10, true));
?>