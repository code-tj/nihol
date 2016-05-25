<?php
define('DIR_CORE','core2');
if(is_readable(DIR_CORE.'/core.php'))
{
	require(DIR_CORE.'/core.php');
	CORE::APP()->run('conf.php');
} else {
	echo 'Core script not found';
}
?>