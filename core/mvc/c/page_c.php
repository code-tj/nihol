<?php
namespace CORE\MVC\C;

class PAGE_C {

public function __construct($REQUEST,$model,$view){
	switch($REQUEST->get('act')){
		default:
			\CORE\BC\UI::init()->static_page($REQUEST->get('act'));
		break;
	}	
}

}