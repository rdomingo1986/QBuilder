<?php 
class DBConfig {

  private $default = 'default';

  private $connections = [
    'default' => [
      'host' => 'localhost',
      'user' => 'root',
      'pass' => '12345678',
      'dbname' => 'globaltech_jobs'
    ],
    'another_one' => [
      'host' => 'host',
      'user' => 'user',
      'pass' => 'pass',
      'dbname' => 'dbname'
    ]
  ];

  function __construct($connectionName = 'default') {
    foreach($this->connections[$connectionName] AS $key => $val) {
      $this->{$key} = $val;
    }
  }
}