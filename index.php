<?php
require_once 'QBuilder/lib/QBuilder.php';

$db = new QBuilder();
$result = $db->insert('people', [
  'name' => 'Pedro',
  'lastname' => 'Perez',
  'identification' => '654222222323',
  'age' => 33
])
  ->execute();

var_dump($result);
var_dump($db->insertId());
echo 'Rows :' . $db->numNows();
echo PHP_EOL;