<?php
  require_once 'QBuilder.php';

  $db = new QBuilder();
  $result = $db->select()
    ->from('users')
    ->limit()
    ->get()
    ->getRawQuery();
  echo $result;
?>