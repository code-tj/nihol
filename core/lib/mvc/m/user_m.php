<?php
namespace mvc\m;

class user_m extends \model
{

    public function login($login='',$pwd='',$remember=false)
    {
      $app=\app::init();
      $login=strtolower(trim($login)); $pwd=trim($pwd);
      if($login!='' && $pwd!='')
      {
        if($this->valid('login',$login) && $this->valid('pwd',$pwd))
        {
          if($app->db())
          {
            $db=$app->db;
            $stmt=$db->h->prepare('SELECT * FROM `users` WHERE `user` = ?;');
            $stmt->execute([$login]);
            $db->qcount();
            if($stmt->rowCount()==1)
            {
              $r=$stmt->fetch();
              $salt=$r['salt'];
              $hpwd=md5(md5($pwd).$salt);
              if($hpwd==$r['pwd'])
              {
                if($r['enabled']==1)
                {
                  //\app::init()->log('debug','[user] authorization...');
                  $ses=array(
                    'uid'=>0,
                    'gid'=>0,
                    'gids'=>array(),
                    'user'=>$r['user'],
                  );
                  $ses['uid']=(int) $r['uid'];
  								$gids_tmp=explode(",",$r['gids']);
                  foreach ($gids_tmp as $index => $gid_str) {
                    $ses['gids'][]=(int) $gid_str;
                  }
                  $ses['gid']=$ses['gids'][0];
                  // save in session
                  if(!isset($_COOKIE['PHPSESSID'])){session_start();}
                  $user=$app->user;
                  $user->session_set('uid',$ses['uid']);
                  $user->session_set('gid',$ses['gid']);
                  $user->session_set('gids',$ses['gids']);
                  $user->session_set('user',$ses['user']);
                  // remember username
                  setcookie(AL.'_lu', base64_encode(strrev(base64_encode($ses['user']))), time() + (86400 * 7), "/"); // 86400 = 1 day
                  // extend session (longer session)
                  $app->user->es_make($ses);
                  ///\app::init()->log('debug',print_r($gids,true));
                  header('Location: ./'); exit;
                } else {
                  $app->log('err','Account is currently locked.');
                }
              } else {
                $app->log('err','Incorrect username or password.');
                $app->data(\mvc\v\user_v::loginForm());
              }
            } else {
              $app->log('err','Such user does not exist.');
              $app->data(\mvc\v\user_v::loginForm());
            }
          }
        } else {
          $app->log('err','Username or password is not valid.');
          $app->data(\mvc\v\user_v::loginForm());
        }
      } else {
        $app->log('err','Username or password is empty.');
        $app->data(\mvc\v\user_v::loginForm());
      }
    }

    public function logout()
    {
      $app=\app::init();
      $user=$app->user;
      if($user->session_get('uid')!='')
      {
        $app->user->es_clean(); // clean - extend session
        $user->session_remove_all(); // removes session data only for specific app
        header('Location: ./'); exit;
      } else {
        $app->log('err','You not signed in.');
      }
    }

    public function passwd($uid=0,$pwd='')
    {
      $app=\app::init();
      if($uid=0){$uid=$app->user->get('uid');}
      if($uid>0 && $pwd!='')
      {
        if($this->valid('pwd',$pwd))
        {
          if($app->db())
          {
            $db=$app->db;
            $sql='UPDATE `users` SET ``';
            $stmt=$db->h->prepare($sql);
            $stmt->execute(array(':user'=>$username));
            $db->qcount();
          }
        } else {
          $app->log('err','Password is not valid');
        }
      }
    }

    public function valid($type,$value)
    {
      $app=\app::init();
      $valid=false;
      $len=strlen($value);
      // validation ...
      switch ($type) {
        case 'login':
          if($app::regex($value,'/^[a-z0-9]+$/') && ($len>=3 && $len<128))
          {
            $valid=true;
          }
          break;
        case 'pwd':
          if($len>=8 && $len<256)
          {
            $valid=true;
          }
          break;
          case 'link':
            if($app::regex($value,'/^[a-zA-Z0-9]+$/'))
            {
              $valid=true;
            }
            break;
      }
      return $valid;
    }

    public function iforgot($user,$vcode,$cp_alias)
    {
      $ok=false;
      $app=\app::init();
      if(isset($_SESSION[$cp_alias]['code']))
      {
        $captcha_code=$_SESSION[$cp_alias]['code'];
        if(strtolower($vcode)==strtolower($captcha_code))
        {
          // check username and email
          $username='';
          if($this->valid('login',$user)){$username=$user;}
          if($username!='')
          {
            if($app->db())
            {
              $db=$app->db;
              $sql='SELECT `uid`,`profile`,`user`,`pid`,`email` FROM `users` LEFT OUTER JOIN `hr-people` ON `profile`=`pid` WHERE `user` = :user;';
              $stmt=$db->h->prepare($sql);
              $stmt->execute(array(':user'=>$username));
              $db->qcount();
              if($stmt->rowCount()==1)
              {
                $r=$stmt->fetch();
                $email=trim($r['email']);
                $uid=(int) $r['uid'];
                if($email!='')
                {
                  // make hash
                  $hash=md5($username.microtime());
                  $this->iforgot_clean();
                  // get some client info
                  $info='';
                  if(isset($_SERVER['REMOTE_ADDR'])) $info.='ADDR: '.$_SERVER['REMOTE_ADDR'].';';
          				if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $info.=' XFWD: '.$_SERVER['HTTP_X_FORWARDED_FOR'].';';
                  $sql = "INSERT INTO `user-forgot` SET
                  `ft-hash`=:hash,
                  `ft-uid`=:uid,
                  `ft-user`=:user,
                  `ft-email`=:email,
                  `ft-time`=(NOW() + INTERVAL 12 HOUR),
                  `ft-info`=:info";
                  $stmt=$db->h->prepare($sql);
                  $stmt->execute(array(
                    ':hash'=>$hash,
                    ':uid'=>$uid,
                    ':user'=>$username,
                    ':email'=>$email,
                    ':info'=>$info,
                  ));
                  $db->qcount();
                  // send email
                  $ok=$this->iforgot_sendmail($email,$hash);
                  if($ok)
                  {
                    $app->log('info','Special message has been sent to your E-mail with the appropriate instructions');
                  } else {
                    $app->log('err','Some problems with sending email');
                  }
                } else {
                  $app->log('err','There is no linked email address');
                }
              }
            }
          } else {
            $app->log('err','Username is not valid');
          }
        } else {
          $app->log('err','[CAPTCHA]: Verification code did not match');
        }
      }
      return $ok;
    }

    public function iforgot_clean()
    {
      if($app->db())
      {
        $db=$app->db;
        // clean old records
        $sql='DELETE FROM `user-forgot` WHERE `ft-time` < NOW() OR `ft-status`=1;';
        $stmt=$db->h->prepare($sql);
        $stmt->execute();
        $db->qcount();
      }
    }

    public function iforgot_sendmail($email='',$hash=''){
      $result=false;
    	if($email!='' && $hash!=''){
        $mailbot_path=APP.'/lib/ext/phpmailer/mailbot.php';
        if(is_readable($mailbot_path))
        {
          require($mailbot_path);
          $result = \mailbot::iforgot_message($email,$hash);
        }
    	}
      return $result;
    }

    public function iforgot_link($link='')
    {
      $app=\app::init();
      $result=false;
      if($link!='' && $this->valid('link',$link))
      {
        if($app->db())
        {
          $db=$app->db;
          $sql="SELECT * FROM `user-forgot` WHERE `ft-hash`=:hash AND `ft-status`=0;";
          $stmt=$db->h->prepare($sql);
          $stmt->execute(array(':hash'=>$link));
          $db->qcount();
          if($stmt->rowCount()==1)
          {
            $result=true;
          }
        }
      } else {
        $app->log('error','Link is not valid');
      }
      return $result;
    }

    public function iforgot_passwd()
    {
      $app=\app::init();
      //$app->log('info','passwd');
      
    }

}
