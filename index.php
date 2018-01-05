<?php
require_once 'QBuilder/lib/QBuilder.php';

  $db = new QBuilder();
  $result = $db->select()
    ->from('people')
    ->execute()
    ->result('JSON');

  var_dump($result);
  var_dump($db->insertId());
  echo 'Rows :' . $db->numNows();
  echo PHP_EOL;

$result = $db->insert('people', [
  'name' => 'Petra',
  'lastname' => 'Gomez',
  'identification' => 876627832,
  'age' => 25
])->execute();

var_dump($result);
var_dump($db->insertId());
echo 'Rows :' . $db->numNows();
echo PHP_EOL;