<?php
require_once 'QBuilder/lib/QBuilder.php';

$db = new QBuilder();
$result = $db->select('*')
  ->from('tours')
  ->execute()
  ->result('JSON');

var_dump($result);
echo PHP_EOL;
echo 'Rows :' . $db->numNows();
echo PHP_EOL;