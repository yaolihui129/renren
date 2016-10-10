<?php
header('content-type:text/html;charset=utf-8');
// if(!file_exists('./install/lock.lock')){
// 	header('location:./install/index.php');
// }
define('APP_DEBUG',true);
define('APP_PATH','./App/');
define('RUNTIME_PATH','./Runtime/');
require './ThinkPHP/ThinkPHP.php';