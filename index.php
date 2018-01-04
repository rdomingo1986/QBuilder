<?php
  require_once 'QBuilder/lib/QBuilder.php';

  $db = new QBuilder();
  //crear varios where and dentro de la misma llamada //un solo parametro de typo array
  //escribir un where raw // un solo parametro de tipo string
  $result = $db->select('*')
    ->from('people')
    // ->where('id', 10)
    // ->where('id = "10" AND apellido = "Ramirez"')
    // ->where('name = "Domingo"')
    // ->where([
    //   'id = "10"'
    // ])
    ->where([
      ['name', 'NOT LIKE', 'domingo'],
      'id = "10"',
      ['age', 20],
      'direccion != "valera"'
    ])
    ->getRawQuery();

  echo $result;
  echo PHP_EOL;
  // echo $db->num_rows();
?>