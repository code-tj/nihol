<?php
if(!defined('DIR_BASE')){echo '[+_+]'; exit;}

if(is_readable(DIR_CORE.'/classes/core.php')) {
	require(DIR_CORE.'/classes/core.php');
} else {
	echo 'class CORE not found';
	exit;
}

$CORE=CORE::init();
$USER=USER::init();
$UI=\CORE\UI::init();
$APP=\CORE\APP::init();

$APP->run();
$APP->stop();

$UI->render();