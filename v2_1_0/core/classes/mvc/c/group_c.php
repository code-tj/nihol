<?php
namespace CORE\MVC\C;

class GROUP_C {

public function __construct($REQUEST,$model,$view){
	switch($REQUEST->get('act')){
		case 'add':
			$model->add();
		break;
		case 'edit':
			$model->edit();
		break;
		case 'update':
			$model->update();
		break;
		case 'del':
			$model->del();
		break;
		default:
			$UI=\CORE\UI::init();
			$UI->pos['main'].=$view->main($model);
		break;
	}
	if(\CORE::init()->is_ajax()){ \DB::init()->close(); exit; }
}

}