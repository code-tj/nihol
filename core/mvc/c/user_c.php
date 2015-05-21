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
	}	
}

}