<?php
namespace CORE\MVC\C;

class USER_C {

public function __construct($REQUEST,$model,$view){
	switch($REQUEST->get('act')){
		case 'login':
			$model->login();
		break;
		case 'logout':
			$model->logout();
		break;
		case 'chpwd':
			$UI=\CORE\BC\UI::init();
			$UI->pos['main'].=$view->chpwd();
		break;
		case 'passwd':
			$model->passwd();
		break;
		case 'manage':
			if(isset($_GET['do'])){
				switch ($_GET['do']) {
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
				}
			} else {
				$UI=\CORE\BC\UI::init();
				$UI->pos['main'].=$view->manage($model);
			}
		break;
	}
	if(\CORE::init()->is_ajax()){ \DB::init()->close(); exit; }
}

}