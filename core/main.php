<?php
if(!defined('DIR_BASE')){echo '[+_+]'; exit;}

if(is_readable(DIR_CORE.'/bc/core.php')) {
	require(DIR_CORE.'/bc/core.php');
} else {
	echo 'Core class not found';
	exit;
}

$CORE=CORE::init();
$USER=\CORE\BC\USER::init();
$UI=\CORE\BC\UI::init();
$APP=\CORE\BC\APP::init();

$CORE->unload();
$UI->show_template();