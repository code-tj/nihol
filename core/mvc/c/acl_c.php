<?php
namespace CORE\MVC\C;

class ACL_C {

public function __construct($REQUEST,$model,$view){

	switch($REQUEST->get('act')){
		default:
			$view->main($model);
		break;
	}	
}

}