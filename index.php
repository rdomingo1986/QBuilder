<?php
  require_once 'QBuilder/lib/QBuilder.php';

  $db = new QBuilder();
  // $result = $db->select('*')
  //   ->from('people')
  //   ->where('id', 1)
  //   ->execute()
  //   ->row();

  // $result = $db->insert('people', [
  //   'name' => 'Luis',
  //   'lastname' => 'Artigas',
  //   'identification' => 1231114567,
  //   'age' => 19
  // ])->execute();

  // $result = $db->update('people', [
  //   'name' => 'Luis',
  //   'lastname' => 'Perez',
  //   'age' => 23
  // ])
  // ->where('identification', 1231114567)
  // ->execute();

  $result = $db->delete('people')
    ->where('id', 1)
    ->execute();

  var_dump($result);
  // echo PHP_EOL;
  echo $db->numNows();
  echo PHP_EOL;
?>