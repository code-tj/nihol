<?php
//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
//$t1=microtime(true);
define('CORE', '../core/'); // Nihol's core dir
set_include_path(get_include_path().PATH_SEPARATOR.CORE.'lib/');
spl_autoload_register();
$app = new APP();
$app->run('conf.php');
//echo (microtime(true)-$t1);
?>