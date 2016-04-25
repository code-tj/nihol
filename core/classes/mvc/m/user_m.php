<?php
namespace CORE\MVC\M;

class USER_M {

private $pwd_hint=false;

public function hint(){return $this->pwd_hint;}

public function login($login='',$password=''){
	// user data initialization
	if(isset($_POST['login']) && isset($_POST['password'])){
		$login=trim($_POST['login']);
		$password=trim($_POST['password']);
	}
	// /user data initialization
	// $login=trim($login); $password=trim($password);
	if($login!='' && $password!=''){
		if($this->check_login($login) && $this->check_password($password)){
			$DB=\DB::init();
			if($DB->connect()){
				$sth = $DB->dbh->prepare("SELECT * FROM `n-users` WHERE LOWER(`usr-login`) = LOWER(?) LIMIT 1;");
				// \CORE::msg('debug','User login check');
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
								// check profile data here, if needed
								\SESSION::start();
								// here may be some additional records, like when loged in, which ip, etc
								$uid=(int) $r['usr-uid'];
								$gid=(int) $r['usr-gid'];
								\SESSION::set('uid',$uid);
								\SESSION::set('gid',$gid);
								\SESSION::set('user',$login);
								\COOKIE::set('lastuser',$login); // optional
								if(isset($r['usr-pid'])){
									if($r['usr-pid']!=''){
										$pid=(int) $r['usr-pid'];
										\SESSION::set('pid',$pid);
									}									
								}
								// setcookie(PREFX.'st',1,time()+3600); // 1 hour
								if(isset($_POST['cookie'])){
									//// $time=86400; // 24 hours
									//// setcookie(PREFIX."ul", base64_encode($login), time()+$time, "/");
								}
								$sth = $DB->dbh->prepare("UPDATE `n-users` SET `usr-lastlogin`=CURRENT_TIMESTAMP() WHERE `usr-uid`=?;");
								$sth->execute(array($uid));
								$DB->query_count();
								// \CORE::msg('debug','User is logged in');
								header('Location: ./');
								exit;
							} else {
								\CORE::msg('error','Account is currently locked');
							}
						} else { \CORE::msg('error','Incorrect username or password'); }
				} else { \CORE::msg('error','Incorrect username or password'); }
			}
		} else { \CORE::msg('error','Username or password is not valid'); }
	} else { \CORE::msg('error','Empty username or password'); }
}

public function login2($login='',$password='',$remember=0){
	// user data initialization
	if(isset($_POST['login']) && isset($_POST['password'])){
		$login=trim($_POST['login']);
		$password=trim($_POST['password']);
	}
	if(isset($_POST['remember'])){
		$remember=(int) $_POST['remember'];
	}
	// /user data initialization
	// $login=trim($login); $password=trim($password);
	if($login!='' && $password!=''){
		if($this->check_login($login) && $this->check_password($password)){
			$DB=\DB::init();
			if($DB->connect()){
				$sth = $DB->dbh->prepare("SELECT * FROM `n-users` WHERE LOWER(`usr-login`) = LOWER(?) LIMIT 1;");
				// \CORE::msg('debug','User login check');
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
					// \CORE::msg('debug','User login and password check');
						if($sth->rowCount()==1){
							if($r['usr-status']>0){
								$r=$sth->fetch();
								// check profile data here, if needed
								\SESSION::start();
								// here may be some additional records, like when loged in, which ip, etc
								$uid=(int) $r['usr-uid'];
								$gid=(int) $r['usr-gid'];
								\SESSION::set('uid',$uid);
								\SESSION::set('gid',$gid);
								\SESSION::set('user',$login);
								if(isset($r['usr-pid'])){
									if($r['usr-pid']!=''){
										$pid=(int) $r['usr-pid'];
										\SESSION::set('pid',$pid);
									}									
								}
								// setcookie(PREFX.'st',1,time()+3600); // 1 hour
								if($remember==1){
									$ip=''; $xip=''; $agent=''; $xdt='';
									$xdt=date('d.m.Y');
									if(isset($_SERVER['REMOTE_ADDR'])) $ip=$_SERVER['REMOTE_ADDR'];
									if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $xip=$_SERVER['HTTP_X_FORWARDED_FOR'];
									if(isset($_SERVER['HTTP_USER_AGENT'])) $agent=$_SERVER['HTTP_USER_AGENT'];
									\COOKIE::set('lastuser',$login); // optional
									\COOKIE::set('tmr',md5($hashpass.$xdt.$ip.$xip.$agent));
								}
								$sth = $DB->dbh->prepare("UPDATE `n-users` SET `usr-lastlogin`=CURRENT_TIMESTAMP() WHERE `usr-uid`=?;");
								$sth->execute(array($uid));
								$DB->query_count();
								// \CORE::msg('debug','User is logged in');
								// //header('Location: ./');
								// //exit;
								echo 'ok';
							} else {
								\CORE::msg('error','Account is currently locked');
							}
						} else { \CORE::msg('error','Incorrect username or password'); }
				} else { \CORE::msg('error','Incorrect username or password'); }
			}
		} else { \CORE::msg('error','Username or password is not valid'); }
	} else { \CORE::msg('error','Empty username or password'); }
}

public function try_to_remember($lu='',$tmr=''){
	$lu=strtolower(trim($lu)); $tmr=trim($tmr);
	if($lu!='' && $tmr!=''){
		$DB=\DB::init();
		if($DB->connect()){
			$sth = $DB->dbh->prepare("SELECT * FROM `n-users` WHERE LOWER(`usr-login`) = LOWER(:lu) LIMIT 1;");
			$sth->execute(array('lu'=>$lu));
			$DB->query_count();
			if($sth->rowCount()==1){
				$r=$sth->fetch();
				if($r['usr-status']>0){
					$ip=''; $xip=''; $agent=''; $xdt='';
					$xdt=date('d.m.Y');
					if(isset($_SERVER['REMOTE_ADDR'])) $ip=$_SERVER['REMOTE_ADDR'];
					if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $xip=$_SERVER['HTTP_X_FORWARDED_FOR'];
					if(isset($_SERVER['HTTP_USER_AGENT'])) $agent=$_SERVER['HTTP_USER_AGENT'];
					if($tmr==md5($r['usr-pwd'].$xdt.$ip.$xip.$agent)){
						\SESSION::start();
						$uid=(int) $r['usr-uid'];
						$gid=(int) $r['usr-gid'];
						\SESSION::set('uid',$uid);
						\SESSION::set('gid',$gid);
						\SESSION::set('user',$lu);
						if(isset($r['usr-pid'])){
							if($r['usr-pid']!=''){
								$pid=(int) $r['usr-pid'];
								\SESSION::set('pid',$pid);
							}
						}
						if(isset($_SERVER['REQUEST_URI'])) {$uri=$_SERVER['REQUEST_URI'];} else {$uri='./';}
						//header('Location: '.$uri);
						//exit;
						
						//$sth = $DB->dbh->prepare("UPDATE `n-users` SET `usr-lastlogin`=CURRENT_TIMESTAMP() WHERE `usr-uid`=?;");
						//$sth->execute(array($uid));
						//$DB->query_count();
					}
				}
			}
		}
	}
}

public function logout(){
	if(\SESSION::get('uid')!=''){
		// session_destroy();
        // session_unset();
        \COOKIE::set('tmr','');
		\SESSION::remove_all(); // only for this app
		// setcookie(PREFX.'st',0,1);
		header("Location: ./"); // here we can put session message like "you logged out"
		exit;
	} else {
		\CORE::msg('debug','Not signed in yet');
	}
}

public function check_login($login){
	$len=strlen($login);
	if(\CORE::isValid($login,'/^[a-zA-Z0-9]+$/') && $len>=3 && $len<128) {
		return true;
	} else {
		return false;
	}
}

public function check_password($password){
	$len=strlen($password);
	if($len>=8 && $len<255){ return true; } else { return false; }
}

public function generate_salt($n=3) {
  $key='';
  $pattern='1234567890abcdefghijklmnopqrstuvwxyz.,*_-=+';
  $counter=strlen($pattern)-1;
  for($i=0;$i<$n;$i++){$key.=$pattern{rand(0,$counter)};}
  return $key;
}

public function random_pwd($len_min=8,$len_max=0) {
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

public function generate_pwd($pwd='',$min=8,$max=0){
	if($pwd=='') { $pwd=$this->random_pwd($min,$max); }
	$salt=$this->generate_salt();
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

public function is_authorized($full_check=false,$errors=false){
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
					header("Location: ./?c=user&act=logout");
					exit;
				} else { $authorized=true; }
			}
		} else { $authorized=true; }
	}
	if(!$authorized && $errors){
		\CORE::init()->msg('error','You are not logged in');
	}
	return $authorized;
}

public function passwd($pwd='',$uid=0) {
	if($pwd=='' && isset($_POST['pwd'])){ $pwd=trim($_POST['pwd']); }
	if($this->check_password($pwd)){
		if($uid==0) { $uid=(int) \USER::init()->get('uid'); }
		$DB=\DB::init();
		if($DB->connect()){
			$gen_pwd=$this->generate_pwd($pwd);
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

// manage

public function get_users(){
	$users=array();
	$DB=\DB::init();
	if($DB->connect()){
		$sql="SELECT * FROM `n-users` LEFT OUTER JOIN `n-groups` ON `usr-gid`=`gp-gid` 
		ORDER BY `usr-login`;";
		$sth=$DB->dbh->prepare($sql);
		$sth->execute();
		$DB->query_count();
		if($sth->rowCount()>0){
			while($r=$sth->fetch()){
					$created=$r['usr-created'];
					$lastlogin=$r['usr-lastlogin'];
					$status='';
					if($created!='') $created=date('H:i:s, d.m.Y',strtotime($created));
					if($lastlogin!='') $lastlogin=date('H:i:s, d.m.Y',strtotime($lastlogin));
					if($r['usr-status']==1) { $status='enabled'; } elseif ($r['usr-status']==0) {
						$status='disabled';
					}
				$users[$r['usr-uid']]=array(
					'user'=>$r['usr-login'],
					'gid'=>$r['gp-group'],
					'pid'=>$r['usr-pid'],
					'status'=>$status,
					'created'=>$created,
					'lastlogin'=>$lastlogin,
					);
			}
		}
	}
	return $users;
}

public function get_groups(){
	$groups=array();
	$DB=\DB::init();
	$recs=$DB->get_records('n-groups','gp-gid',' ORDER BY `gp-sort`');
	foreach($recs as $key => $val){
		$groups[$key]=$val['gp-group'];
	}
	return $groups;
}

public function add($user='',$pwd='',$gid=0,$status=1,$pid=0){
	$valid=true; $errors='';
	// user data initialization
	if($user=='' && isset($_POST['user'])){
		$user=trim($_POST['user']);
	}
	if($pwd=='' && isset($_POST['password'])){
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
	if($user=='' || !$this->check_login($user)) $valid=false;
	if($pwd=='' || !$this->check_password($pwd)) $valid=false;
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
			$DB->query_count();
			if($sth->rowCount()>0){
				$valid=false;
				// \CORE::init()->msg('error','Such user exists in the database.');
				$errors.='Such user exists in the database.';
			} else {
				$pwd_array=$this->generate_pwd($pwd);
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

public function edit($uid=0){
	$uid=(int) $uid;
	if($uid==0 && isset($_POST['uid'])){ $uid=(int) $_POST['uid']; }
	if($uid>0){
		$DB=\DB::init();
		if($DB->connect()){
			$sql = "SELECT * FROM `n-users` WHERE `usr-uid`=:id;";
			$sth = $DB->dbh->prepare($sql);
			$sth->execute(array('id'=>$uid));
			$DB->query_count();
			if($sth->rowCount()==1){
				$r=$sth->fetch();
				$user=array(
					'uid'=>$r['usr-uid'],
					'gid'=>$r['usr-gid'],
					'pid'=>$r['usr-pid'],
					'user'=>htmlspecialchars($r['usr-login']),
					'status'=>$r['usr-status'],
					);
				echo json_encode($user);
			}
		}
	} else {
		\CORE::msg('error','Incorrect user ID.');
	}
}

public function update($uid=0,$gid=0,$user='',$chpwd=0,$pwd='',$status=1,$pid=0){
	$valid=true; $errors='';
	if($uid==0 && isset($_POST['uid'])){ $uid=(int) $_POST['uid']; }
	// user data initialization
	if($user=='' && isset($_POST['user'])){
		$user=trim($_POST['user']);
	}
	if($pwd=='' && isset($_POST['password'])){
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
	if($user=='' || !$this->check_login($user)) $valid=false;
	if($chpwd!=0){
		if($pwd=='' || !$this->check_password($pwd)) $valid=false;
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
			$DB->query_count();
			if($sth->rowCount()==1){
				$pwd_array=$this->generate_pwd($pwd);
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
				// choose: insert lowercase login or not
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
					$pwd_array=$this->generate_pwd($pwd);
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
					// choose: insert lowercase login or not
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

public function del($uid=0){
	$uid=(int) $uid;
	if($uid==0 && isset($_POST['uid'])){ $uid=(int) $_POST['uid']; }
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

public function iforgot_clean(){
	$DB=\DB::init();
	if($DB->connect()){
		$sql = "DELETE FROM `bw-iforgot` WHERE `ft-time` < NOW() OR `ft-status`=0;";
		$sth = $DB->dbh->prepare($sql);
		$sth->execute();
		$DB->query_count();
	}
}

public function iforgot_get_email($username=''){
	$email='';
	if($username!=''){
		$DB=\DB::init();
		if($DB->connect()){
			$sql = "SELECT `usr-uid`,`usr-login`,`usr-email` FROM `n-users` WHERE `usr-login`=:username;";
			$sth = $DB->dbh->prepare($sql);
			$sth->execute(array('username'=>$username));
			$DB->query_count();
			if($sth->rowCount()==1){
				$r=$sth->fetch();
				$email=trim($r['usr-email']);
			}
		}
	}	
	return $email;
}

public function iforgot_create($ihash='',$username='',$email=''){
	if($ihash!='' && $username!='' && $email!=''){
		$DB=\DB::init();
		if($DB->connect()){
			$sql = "SELECT * FROM `bw-iforgot` WHERE `ft-username`=:username AND `ft-ihash`=:ihash;";
			$sth = $DB->dbh->prepare($sql);
			$sth->execute(array('username'=>$username,'ihash'=>$ihash));
			$DB->query_count();
			if($sth->rowCount()>0){
				\CORE::msg('error','The link already created and email already sent.');
			} else {
				$hash=md5($username.microtime());
				$xip=NULL; $ip1=''; $ip2=''; // $agent='';
				if(isset($_SERVER['REMOTE_ADDR'])) $ip1=$_SERVER['REMOTE_ADDR'];
				if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip2=$_SERVER['HTTP_X_FORWARDED_FOR'];
				//if(isset($_SERVER['HTTP_USER_AGENT'])) $agent=$_SERVER['HTTP_USER_AGENT'];
				if($ip1!='' || $ip2!=''){
					$xip='ADDR: '.$ip1.', XFWD: '.$ip2;
				}		
				$sql = "INSERT INTO `bw-iforgot` SET 
				`ft-hash`=:hash, 
				`ft-username`=:username,
				`ft-email`=:email,
				`ft-time`=NOW() + INTERVAL 12 HOUR,
				`ft-ihash`=:ihash,
				`ft-ip`=:xip,
				`ft-created`=NOW();";
				$sth = $DB->dbh->prepare($sql);
				$sth->execute(array('hash'=>$hash,'username'=>$username,'email'=>$email,'ihash'=>$ihash,'xip'=>$xip));
				$DB->query_count();
				$this->iforgot_email($hash,$username,$email);
				\CORE::msg('info','In a few minutes you will receive an email with instruction to reset your password.');
				\CORE::msg('info','If you did not receive an email within 10 minutes, please contact you system administrator');
			}
		}
	}
}


public function iforgot_check_link($link=''){
	$username='';
	if($link!=''){
		$DB=\DB::init();
		if($DB->connect()){
		$sql = "SELECT * FROM `bw-iforgot` WHERE `ft-hash`=:hash AND `ft-status`=1;";
		$sth = $DB->dbh->prepare($sql);
		$sth->execute(array('hash'=>$link));
		$DB->query_count();
			if($sth->rowCount()==1){
				$r=$sth->fetch();
				$username=$r['ft-username'];
			}
		}
	}
	return $username;
}

public function iforgot_passwd($link='',$pwd=''){
	$link=trim($link); $pwd=trim($pwd);
	if($link!='' && $pwd!=''){
		$username=$this->iforgot_check_link($link);
		if($username!=''){
			$uid=(int) $this->get_uid_via_username($username);
			$this->passwd($pwd,$uid);
			$this->iforgot_deactivate_link($link);
		}
	}
}

public function iforgot_deactivate_link($link=''){
	if($link!=''){
		$DB=\DB::init();
		if($DB->connect()){
			$sql = "UPDATE `bw-iforgot` SET `ft-status`=0 WHERE `ft-hash`=:hash;";
			$sth = $DB->dbh->prepare($sql);
			$sth->execute(array('hash'=>$link));
			$DB->query_count();
		}
	}
}

public function get_uid_via_username($username=''){
	$uid=0;
	if($username!=''){
		$DB=\DB::init();
		if($DB->connect()){
			$sql = "SELECT `usr-uid`,`usr-login`,`usr-email` FROM `n-users` WHERE `usr-login`=:username;";
			$sth = $DB->dbh->prepare($sql);
			$sth->execute(array('username'=>$username));
			$DB->query_count();
			if($sth->rowCount()==1){
				$r=$sth->fetch();
				$uid=(int) $r['usr-uid'];
			}
		}
	}
	return $uid;
}

public function iforgot_email($hash='',$username='',$email=''){
	if($hash!='' && $username!='' && $email!=''){
		$path=DIR_APP.'/classes/mailer/mailbot.php';
		if(is_readable($path)){
			require $path;
			\MAILBOT::msg($email,$hash,$username);
		}		
	}
}


}