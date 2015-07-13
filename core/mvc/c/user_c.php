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
			if(isset($_GET['ajax'])){
				switch ($_GET['ajax']) {
					case 'add':
						$model->add_user();
					break;
					case 'edit':
						if(isset($_POST['uid'])){
							$model->edit_user($_POST['uid']);
						} else {echo 'Error: User ID is not defined.';}
					break;
					case 'update':
						if(isset($_POST['uid'])){
							$model->update_user($_POST['uid']);
						} else {echo 'Error: Parameters are not defined.';}
					break;
					case 'del':
						if(isset($_POST['uid'])){
							$model->del_user($_POST['uid']);
						} else {echo 'Error: User ID is not defined.';}
					break;
				}
			\DB::init()->close();
			exit;
			}
			$view->manage_users($model);
		break;
		case 'change_password':
			if(isset($_GET['ajax'])){
				switch ($_GET['ajax']) {
					case 'passwd':
						if(isset($_POST['pwd'])){
							$model->passwd($_POST['pwd']);
						} else {echo 'Error: pwd parameter is not defined.';}
					break;
				}
			\DB::init()->close();
			exit;
			}
			$view->passwd($model);
		break;
		case 'groups':
			if(isset($_GET['ajax'])){
				switch ($_GET['ajax']) {
					case 'add':
						if(isset($_POST['newgroup'])){
							$model->add_group($_POST['newgroup']);
						} else {echo 'Error: newgroup parameter is not defined.';}
					break;
					case 'edit':
						if(isset($_POST['gid'])){
							$model->edit_group($_POST['gid']);
						} else {echo 'Error: group ID is not defined.';}
					break;
					case 'update':
						if(isset($_POST['gid']) && isset($_POST['editgroup'])){
							$model->update_group($_POST['gid'],$_POST['editgroup']);
						} else {echo 'Error: Parameters are not defined.';}
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