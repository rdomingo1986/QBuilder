<?php
  require_once 'QBuilder/lib/QBuilder.php';

  $db = new QBuilder();
  $result = $db->select('*')
    ->from('people')
    ->get()
    ->result();
    
  echo json_encode($result);
  echo PHP_EOL;
  echo $db->num_rows();
?>