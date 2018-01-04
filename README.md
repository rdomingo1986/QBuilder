QBuilder
===============

QBuilder is a simple query builder for create and execute SQL statements.

Installation
-----------

Download or clone the source code and require the QBuilder.php file.
``` sh
<?php
  require_once 'QBuilder/lib/QBuilder.php';
```

Basic SELECT query
-----------

``` sh
  $QB = new QBuilder();

  $result = $QB->select()
               ->from('users')
               ->get()
               ->result();

  // Resultant query 'SELECT * FROM users'
  // This return all rows from the users table
```