<?php
  require_once 'QBuilder.php';

  $db = new QBuilder();
  $result = $db->select()
    ->from('users')
    ->orderBy('id', 'ASC')
    ->limit()
    ->get()
    ->getRawQuery();
  echo $result;
?>