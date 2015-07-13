<?php
namespace CORE\MVC\M;

class USER_M {

private $hint=false;
public function hint(){return $this->hint;}

public function login($login,$password){
$login=trim($login);
$password=trim($password);
	if($login!='' && $password!=''){
		if($this->CheckLogin($login) && $this->CheckPassword($password)){
			$DB=\DB::init();
			if($DB->connect()){
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

// ..

public function GenSalt($n=3) {
  $key='';
  $pattern='1234567890abcdefghijklmnopqrstuvwxyz.,*_-=+';
  $counter=strlen($pattern)-1;
  for($i=0;$i<$n;$i++){$key.=$pattern{rand(0,$counter)};}
  return $key;
}

public function randPwd($len_min=8,$len_max=0) {
	if($len_max>$len_min){
		$len=rand($len_min,$len_max);
	} else {
		$len=(int) $len_min;
	}
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); // remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; // put the length -1 in cache
    for ($i = 0; $i < $len; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); // turn the array into a string
}

public function pwdGen($pwd='',$min=8,$max=0){
if($pwd=='') { $pwd=$this->randPwd($min,$max); }
$salt=$this->GenSalt();
$pwd_hash=md5(md5($pwd).$salt);
$hint=base64_encode($pwd);
$pd=array(
	'pwd' => $pwd,
	'hash' => $pwd_hash,
	'salt' => $salt,
	'hint' => $hint,
	);
return $pd;
}

public function auth($full_check=false,$errors=false){
	$authorized=false;
	if(\SESSION::get('uid')!=''){
		if($full_check){
			$DB=\DB::init();
			if($DB->connect()){
				$uid = (int) \SESSION::get('uid');
				$sql="SELECT `usr-login` FROM `n-users` WHERE `usr-uid`=:uid LIMIT 1;";
				$sth = $DB->dbh->prepare($sql);
				$sth->execute(array('uid'=>$uid));
				\CORE::init()->msg('debug','Checking is user authorized via database.');
				if($sth->rowCount()!=1){
					header("Location: ./?c=user&act=logout"); exit;
				} else { $authorized=true; }
			}
		} else { $authorized=true; }
	}
	if(!$authorized && $errors){
		\CORE::init()->msg('error','You are not logged in.');
	}
	return $authorized;
}

public function passwd($pwd='',$uid=0) {
	if($this->CheckPassword($pwd)){
		if($uid==0) { $uid=(int) \CORE\BC\USER::init()->get('uid'); }
		$DB=\DB::init();
		if($DB->connect()){
			$gen_pwd=$this->pwdGen($pwd);
			$pwd=array(
				'hash'=>$gen_pwd['hash'],
				'salt'=>$gen_pwd['salt'],
				'hint'=>$gen_pwd['hint'],
				'uid'=>$uid,
				);
			if(!$this->hint()) $pwd['hint']=NULL;
			$sql = "UPDATE `n-users` SET `usr-pwd`=:hash, `usr-salt`=:salt, `usr-hint`=:hint WHERE `usr-uid`=:uid;";
			$sth = $DB->dbh->prepare($sql);
			$sth->execute($pwd);
			$DB->query_count();
			\CORE::init()->msg('info','Password successfully changed.');
		}
	} else {
		\CORE::init()->msg('error','Password is not valid.');
	}
}

// /..

public function get_users(){
	$users=array();
	$DB=\DB::init();
	if($DB->connect()){
		$sql="SELECT * FROM `n-users` 
		LEFT OUTER JOIN `n-groups` ON `usr-gid`=`gp-gid`
		ORDER BY `usr-login`;";
		$sth=$DB->dbh->prepare($sql);
		$sth->execute();
		$DB->query_count();
		if($sth->rowCount()>0){
			$statuses=array('0'=>'disabled','1'=>'active');
			while($r=$sth->fetch()){
					$created=$r['usr-created'];
					$lastlogin=$r['usr-lastlogin'];
					$status='';
					if($created!='') $created=date('H:i:s, d.m.Y',strtotime($created));
					if($lastlogin!='') $lastlogin=date('H:i:s, d.m.Y',strtotime($lastlogin));
					if(isset($statuses[$r['usr-status']])) $status=$statuses[$r['usr-status']];
				$users[$r['usr-uid']]=array(
					'username'=>$r['usr-login'],
					'gid'=>$r['gp-group'],
					'pid'=>$r['usr-pid'],
					'status'=>$status,
					'created'=>$created,
					'lastlogin'=>$lastlogin
					);
			}
		}
	}
	return $users;
}

public function add_user($user='',$pwd='',$gid=0,$status=1,$pid=0){
	$valid=true; $errors='';
	// user data initialization
	if($user=='' && $_POST['user']){
		$user=trim($_POST['user']);
	}
	if($pwd=='' && $_POST['password']){
		$pwd=trim($_POST['password']);
	}
	if($gid==0 && isset($_POST['gid'])) {
		$gid=(int) $_POST['gid'];
	}
	if($status==1 && isset($_POST['status'])) {
		$status=(int) $_POST['status'];
	}
	if($pid==0 && isset($_POST['pid'])) {
		$pid=(int) $_POST['pid'];
	}
	// validation
	if($user=='' || !$this->CheckLogin($user)) $valid=false;
	if($pwd=='' || !$this->CheckPassword($pwd)) $valid=false;
	if($gid==0) $valid=false;
	if($status<0 || $status>1) $valid=false;
	if($pid==0) $pid=NULL;
	// if valid for with db
	if($valid){
		$DB=\DB::init();
		if($DB->connect()){
			// check is exists
			$sql = "SELECT * FROM `n-users` WHERE LOWER(`usr-login`)=LOWER(:user);";
			$sth = $DB->dbh->prepare($sql);
			$sth->execute(array('user'=>$user));
			if($sth->rowCount()>0){
				$valid=false;
				// \CORE::init()->msg('error','Such user exists in the database.');
				$errors.='Such user exists in the database.';
			} else {
				$pwd_array=$this->pwdGen($pwd);
				$usr=array(
					'login'=>$user,
					'hash'=>$pwd_array['hash'],
					'salt'=>$pwd_array['salt'],
					'hint'=>$pwd_array['hint'],
					'gid'=>$gid,
					'pid'=>$pid,
					'status'=>$status
					);
				if(!$this->hint()) $usr['hint']=NULL;
				// insert lowercase login or not?
				$sql = "INSERT INTO `n-users` SET 
				`usr-login`=LOWER(:login), 
				`usr-pwd`=:hash, 
				`usr-salt`=:salt, 
				`usr-hint`=:hint, 
				`usr-gid`=:gid, 
				`usr-pid`=:pid, 
				`usr-status`=:status;";
				$sth = $DB->dbh->prepare($sql);
				$sth->execute($usr);
				$DB->query_count();
				\CORE::init()->msg('info','New user successfully added.');
			}
		}
	} else {
		// \CORE::init()->msg('error','User data is incorrect.');
		$errors.='User data is incorrect.';
	}
	if($errors!=''){echo json_encode(array('errors'=>$errors));}
}

public function edit_user($uid=0){
	$uid=(int) $uid;
	if($uid>0){
		$DB=\DB::init();
		if($DB->connect()){
			$sql = "SELECT * FROM `n-users` WHERE `usr-uid`=:id;";
			$sth = $DB->dbh->prepare($sql);
			$sth->execute(array('id'=>$uid));
			$DB->query_count();
			if($sth->rowCount()==1){
				$r=$sth->fetch();
				$json=json_encode(array(
					'uid'=>$r['usr-uid'],
					'gid'=>$r['usr-gid'],
					'user'=>htmlspecialchars($r['usr-login']),
					'status'=>$r['usr-status'],
					));
				echo $json;
			}
		}
	} else {
		\CORE::msg('error','Incorrect user ID.');
	}
}

public function update_user($uid=0,$gid=0,$user='',$chpwd=0,$pwd='',$status=1,$pid=0){
	$valid=true; $errors='';
	// user data initialization
	if($user=='' && $_POST['user']){
		$user=trim($_POST['user']);
	}
	if($pwd=='' && $_POST['password']){
		$pwd=trim($_POST['password']);
	}
	if($uid==0 && isset($_POST['uid'])) {
		$uid=(int) $_POST['uid'];
	}
	if($gid==0 && isset($_POST['gid'])) {
		$gid=(int) $_POST['gid'];
	}
	if($chpwd==0 && isset($_POST['chpwd'])) {
		$chpwd=(int) $_POST['chpwd'];
	}
	if($status==1 && isset($_POST['status'])) {
		$status=(int) $_POST['status'];
	}
	if($pid==0 && isset($_POST['pid'])) {
		$pid=(int) $_POST['pid'];
	}
	// validation
	if($user=='' || !$this->CheckLogin($user)) $valid=false;
	if($chpwd!=0){
		if($pwd=='' || !$this->CheckPassword($pwd)) $valid=false;
	}
	if($uid==0) $valid=false;
	if($gid==0) $valid=false;
	if($status<0 || $status>1) $valid=false;
	if($pid==0) $pid=NULL;
	// if valid for with db
	if($valid){
		$DB=\DB::init();
		if($DB->connect()){
			$sql = "SELECT * FROM `n-users` WHERE LOWER(`usr-login`)=LOWER(:user) AND `usr-uid`=:uid;";
			$sth = $DB->dbh->prepare($sql);
			$sth->execute(array('user'=>$user,'uid'=>$uid));
			if($sth->rowCount()==1){
				$pwd_array=$this->pwdGen($pwd);
				$usr=array(
					'hash'=>$pwd_array['hash'],
					'salt'=>$pwd_array['salt'],
					'hint'=>$pwd_array['hint'],
					'gid'=>$gid,
					'pid'=>$pid,
					'status'=>$status,
					'uid'=>$uid
					);
				if(!$this->hint()) $usr['hint']=NULL;
				// insert lowercase login or not?
				if($chpwd>0){
					$sql = "UPDATE `n-users` SET 
					`usr-pwd`=:hash, 
					`usr-salt`=:salt, 
					`usr-hint`=:hint, 
					`usr-gid`=:gid, 
					`usr-pid`=:pid, 
					`usr-status`=:status
					WHERE `usr-uid`=:uid;";
				} else {
					$usr=array(
					'gid'=>$gid,
					'pid'=>$pid,
					'status'=>$status,
					'uid'=>$uid
					);
					$sql = "UPDATE `n-users` SET 
					`usr-gid`=:gid, 
					`usr-pid`=:pid, 
					`usr-status`=:status
					WHERE `usr-uid`=:uid;";
				}				
				$sth = $DB->dbh->prepare($sql);
				$sth->execute($usr);
				$DB->query_count();
				\CORE::init()->msg('info','User data successfully updated.');
			} else {
				// check is exists
				$sql = "SELECT * FROM `n-users` WHERE LOWER(`usr-login`)=LOWER(:user);";
				$sth = $DB->dbh->prepare($sql);
				$sth->execute(array('user'=>$user));
				if($sth->rowCount()>0){
					$valid=false;
					// \CORE::init()->msg('error','Such user exists in the database.');
					$errors.='Such user exists in the database.';
				} else {
					$pwd_array=$this->pwdGen($pwd);
					$usr=array(
						'login'=>$user,
						'hash'=>$pwd_array['hash'],
						'salt'=>$pwd_array['salt'],
						'hint'=>$pwd_array['hint'],
						'gid'=>$gid,
						'pid'=>$pid,
						'status'=>$status,
						'uid'=>$uid
						);
					if(!$this->hint()) $usr['hint']=NULL;
					// insert lowercase login or not?
					if($chpwd>0){
						$sql = "UPDATE `n-users` SET 
						`usr-login`=LOWER(:login), 
						`usr-pwd`=:hash, 
						`usr-salt`=:salt, 
						`usr-hint`=:hint, 
						`usr-gid`=:gid, 
						`usr-pid`=:pid, 
						`usr-status`=:status
						WHERE `usr-uid`=:uid;";
					} else {
						$usr=array(
						'login'=>$user,
						'gid'=>$gid,
						'pid'=>$pid,
						'status'=>$status,
						'uid'=>$uid
						);
						$sql = "UPDATE `n-users` SET 
						`usr-login`=LOWER(:login), 
						`usr-gid`=:gid, 
						`usr-pid`=:pid, 
						`usr-status`=:status
						WHERE `usr-uid`=:uid;";
					}				
					$sth = $DB->dbh->prepare($sql);
					$sth->execute($usr);
					$DB->query_count();
					\CORE::init()->msg('info','User data successfully updated.');
				}
			}
		}
	} else {
		// \CORE::init()->msg('error','User data is incorrect.');
		$errors.='User data is incorrect.';
	}
	if($errors!=''){echo json_encode(array('errors'=>$errors));}


}

public function del_user($uid=0){
	$uid=(int) $uid;
	if($uid>0){
		$DB=\DB::init();
		if($DB->connect()){
			if($DB->del('n-users','usr-uid',$uid)){
				\CORE::msg('info','User successfully deleted.');
			} else {
				\CORE::msg('error','User was not deleted.');
			}
		} else {
			\CORE::msg('error','Incorrect ID.');
		}
	}
}

// User groups

public function get_groups(){
	$groups=array();
	$DB=\DB::init();
	if($DB->connect()){
		$sql="SELECT * FROM `n-groups` ORDER BY `gp-sort`;";
		$sth=$DB->dbh->prepare($sql);
		$sth->execute();
		$DB->query_count();
		if($sth->rowCount()>0){
			while($r=$sth->fetch()){
				$groups[$r['gp-gid']]=array('groupname'=>$r['gp-group']);
			}
		}
	}
	return $groups;
}

public function add_group($group_name=''){
	$group_name=trim($group_name);
	if($group_name!=''){
		$DB=\DB::init();
		if($DB->connect()){
			if($DB->isUnique('n-groups','gp-group',$group_name)){
				$sql = "INSERT INTO `n-groups` SET `gp-group`=:groupname;";
				$sth = $DB->dbh->prepare($sql);
				$sth->execute(array('groupname'=>$group_name));
				$DB->query_count();
				\CORE::msg('info','Group successfully added.');
			}
		}
	} else {
		\CORE::msg('error','Group name could not be empty.');
	}
}

public function edit_group($gid=0){
	$gid=(int) $gid;
	if($gid>0){
		$DB=\DB::init();
		if($DB->connect()){
			$sql = "SELECT * FROM `n-groups` WHERE `gp-gid`=:id;";
			$sth = $DB->dbh->prepare($sql);
			$sth->execute(array('id'=>$gid));
			$DB->query_count();
			if($sth->rowCount()==1){
				$r=$sth->fetch();
				echo json_encode(array('group'=>htmlspecialchars($r['gp-group'])));
			}
		}
	} else {
		\CORE::msg('error','Incorrect group ID.');
	}
}

public function update_group($gid=0,$group=''){
	$gid=(int) $gid;
	$group=trim($group);
	if($gid>0 && $group!=''){
		$DB=\DB::init();
		if($DB->connect()){
			if($DB->isUnique('n-groups','gp-group',$group)){
				$sql = "UPDATE `n-groups` SET `gp-group`=:group WHERE `gp-gid`=:gid;";
				$sth = $DB->dbh->prepare($sql);
				$sth->execute(array('group'=>$group,'gid'=>$gid));
				$DB->query_count();
				\CORE::msg('info','Group successfully updated.');
			}
		}
	} else {
		\CORE::msg('error','Incorrect group data.');
	}
}

public function del_group($gid=0){
	$gid=(int) $gid;
	if($gid>0){
		$DB=\DB::init();
		if($DB->connect()){
			if($DB->del('n-groups','gp-gid',$gid)){
				\CORE::msg('info','Group successfully deleted.');
			} else {
				\CORE::msg('error','Group was not deleted.');
			}
		}
	} else {
		\CORE::msg('error','Incorrect ID.');
	}
}

}