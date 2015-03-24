<?php
$start=microtime(true); // count script execution time
if(is_readable('./conf.php')){require('./conf.php');} else {echo 'Configuration not found';exit;}
if(is_readable(CDIR.'/core.php')){require(CDIR.'/core.php');} else {echo 'Core not loaded';exit;}
// if(XDEBUG) echo (microtime(true)-$start); // show script exec. time
?>