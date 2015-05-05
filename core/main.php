<?php
if(!defined('BDIR')){echo '[+_+]'; exit;}

if(is_readable(CDIR.'/bc/core.php')) {require(CDIR.'/bc/core.php');} else {echo 'Core not found'; exit;}

$CORE=CORE::init();
$USER=\CORE\BC\USER::init();
$UI=\CORE\BC\UI::init();
$APP=\CORE\BC\APP::init();

$CORE::unload();
//$CORE->includes();
if($UI->tpl()!=''){include($UI->tpl());}