<?php
namespace CORE\MVC\M;

class ACL_M {

private $modules=array();

function __construct(){
	$this->modules=\CORE::init()->get_modules();
}




}