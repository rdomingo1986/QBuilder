<?php
  require_once 'QBuilder/lib/QBuilder.php';

  $db = new QBuilder();
  $result = $db->select('*')
    ->from('people')
    ->get()
    ->result('XML');

  echo $result;
  echo PHP_EOL;
  echo $db->num_rows();
?>