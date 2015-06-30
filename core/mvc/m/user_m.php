<?php
namespace CORE\MVC\M;

class USER_M {

public function login($login,$password){
$login=trim($login);
$password=trim($password);
	if($login!='' && $password!=''){
		if($this->CheckLogin($login) && $this->CheckPassword($password)){
			$DB=\DB::init();
			if($DB->connected()){
				$sth = $DB->dbh->prepare("SELECT * FROM `n-users` WHERE LOWER(`usr-login`) = LOWER(?) LIMIT 1;");
				\CORE::msg('debug','User login check');
				$sth->bindParam(1, $login, \PDO::PARAM_STR);
				$sth->execute();
				$DB->query_count();
				if($sth->rowCount()==1){
					$r=$sth->fetch();
					$salt=$r['usr-salt'];
					$hashpass=md5(md5($password).$salt);
					$sth = $DB->dbh->prepare("SELECT * FROM `n-users` WHERE LOWER(`usr-login`)=LOWER(:login) AND `usr-pwd`=:hashpass LIMIT 1;");
					$sth->execute(array(':login'=>$login,':hashpass'=>$hashpass));
					$DB->query_count();
					\CORE::msg('debug','User login and password check');
						if($sth->rowCount()==1){

							if($r['usr-status']>0){
								$r=$sth->fetch();
								// here will be additional checking via profile data, if needed
								\SESSION::start();
								// here may be some additional records, like when login, which ip, etc
								$uid=(int) $r['usr-uid'];
								$gid=(int) $r['usr-gid'];
								\SESSION::set('uid',$uid);
								\SESSION::set('gid',$gid);
								\SESSION::set('user',$login);
								if(isset($r['usr-pid'])){
									$pid=(int) $r['usr-pid'];
									\SESSION::set('pid',$pid);
								}
								// setcookie(PREFX.'st',1,time()+3600); // 1 hour
								if(isset($_POST['cookie'])){
									//// $time=86400; // 24 hours
									//// setcookie(PREFIX."ul", base64_encode($login), time()+$time, "/");
								}
								$sth = $DB->dbh->prepare("UPDATE `n-users` SET `usr-lastlogin`=CURRENT_TIMESTAMP() WHERE `usr-uid`=?;");
								$sth->execute(array($uid));
								$DB->query_count();
								\CORE::msg('debug','User is logged in');
								header('Location: ./');
								exit;
							} else {
								\CORE::msg('error','Account is currently locked');
							}

						} else { \CORE::msg('error','Incorrect username or password'); }
				} else { \CORE::msg('error','Incorrect username or password'); }

			} else { \CORE::msg('debug','DB is not connected'); } // ?? move to db class
		} else { \CORE::msg('error','Username or password is not valid'); }
	} else { \CORE::msg('error','Empty username or password'); }
}

public function logout(){
	if(\SESSION::get('uid')!=''){
		// session_destroy();
        // session_unset();
		\SESSION::remove_all(); // only for this app
		// setcookie(PREFX.'st',0,1);
		header("Location: ./"); // here we can put session message like "you logged out"
		exit;
	} else {
		\CORE::msg('debug','Not signed in yet');
	}
}

public function CheckLogin($login){
	$len=strlen($login);
	if(\CORE::isValid($login,'/^[a-zA-Z0-9]+$/') && $len>=3 && $len<128){ return true; } else { return false; }
}

public function CheckPassword($password){
	$len=strlen($password);
	if($len>=8 && $len<255){ return true; } else { return false; }
}

public function get_groups(){
	$groups=array();
	$DB=\DB::init();
	if($DB->connected()){
		$sql="SELECT * FROM `n-groups` ORDER BY `gp-sort`;";
		$sth=$DB->dbh->prepare($sql);
		$sth->execute();
		if($sth->rowCount()>0){
			while($r=$sth->fetch()){
				$groups[$r['gp-gid']]=$r['gp-group'];
			}
		}
	}
	return $groups;
}

}