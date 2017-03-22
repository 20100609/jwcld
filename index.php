<?php
  /*
  *author: Xiafeng
  *date: 2014-05-20
  *description: entrance file
  */

  //definr the application name of website
  define('APP_NAME', 'jwcld');
  define('APP_PATH', './jwcld/');
  define('APP_DEBUG', true);
  define('RUNTIME_PATH',APP_PATH.'Runtime/');
  define('BUILD_DIR_SECURE',true);
  define('DIR_SECURE_FILENAME', 'default.html');
  define('DIR_SECURE_CONTENT', 'Deny Access!');
  define('REAL_PATH',realpath(dirname(__FILE__).'/'));
  define('HTTP_ADDRESS_PORT','http://localhost:8081');
  require './ThinkPHP/ThinkPHP.php';
?>
