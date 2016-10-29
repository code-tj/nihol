<?php
namespace CORE\MVC\C;

class FRONTPAGE_C {

public function __construct($MODULE){
	//\CORE::msg('construct FRONTPAGE_C');
	// show guest home page, user or admnin dashboard
	\UI::init()->p('FRONTPAGE');
}

}