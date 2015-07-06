<?php
namespace CORE\MVC\C;

class USER_C {

public function __construct($REQUEST,$model,$view){
	switch($REQUEST->get('act')){
		case 'login':
			$login=''; $password='';
			if(isset($_POST['login']) && isset($_POST['password'])){
				$login=trim($_POST['login']);
				$password=trim($_POST['password']);
			}
			if($login!='' && $password!=''){
				$model->login($login,$password);
			} else {
				\CORE::msg('error','Empty username or password');
			}
		break;
		case 'logout':
			$model->logout();
		break;
		case 'profile':
			$view->profile($model);
		break;
		case 'manage':
			$view->manage_users($model);
		break;
		case 'groups':
			if(isset($_GET['ajax'])){
				switch ($_GET['ajax']) {
					case 'add':
						if(isset($_POST['groupname'])){
							$model->add_group($_POST['groupname']);
						} else {echo 'Error: groupname parameter is not defined.';}
					break;
					case 'del':
						if(isset($_POST['gid'])){
							$model->del_group($_POST['gid']);
						} else {echo 'Error: group ID is not defined.';}
					break;
				}
			\DB::init()->close();
			exit;
			}
			$view->manage_groups($model);
		break;
	}	
}

}