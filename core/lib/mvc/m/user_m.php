<?php
namespace mvc\m;

class user_m extends \model
{

    public function login($login='',$pwd='',$remember=false)
    {
      $login=strtolower(trim($login)); $pwd=trim($pwd);
      if($login!='' && $pwd!='')
      {
        if($this->valid('login',$login) && $this->valid('pwd',$pwd))
        {
          $db=\my::module('db');
          if($db->connected())
          {
            $stmt=$db->h->prepare('SELECT * FROM `users` WHERE `u-user` = ?;');
            $stmt->execute([$login]);
            $db->qcount();
            if($stmt->rowCount()==1)
            {
              $r=$stmt->fetch();
              $salt=$r['u-salt'];
              $hpwd=md5(md5($pwd).$salt);
              if($hpwd==$r['u-pwd'])
              {
                if($r['u-enabled']==1)
                {
                  $ses=array(
                    'uid'=>0,
                    'gid'=>0,
                    'gids'=>array(),
                    'user'=>'',
                    'pid'=>0,
                  );
                  $ses['uid']=(int) $r['u-id'];
  								$gids_tmp=explode(",",$r['u-gids']);
                  foreach ($gids_tmp as $index => $gid_str) {
                    $ses['gids'][]=(int) $gid_str;
                  }
                  $ses['gid']=$ses['gids'][0];
                  $ses['user']=$r['u-user'];
                  $ses['pid']=(int) $r['u-profile'];
                  // save session data
                  if(session_status()==PHP_SESSION_NONE){session_start();}
                  $user=\my::user();
                  $user->session->set('uid',$ses['uid']);
                  $user->session->set('gid',$ses['gid']);
                  $user->session->set('gids',$ses['gids']);
                  $user->session->set('user',$ses['user']);
                  $user->session->set('pid',$ses['pid']);
                  // store username in cookie
                  setcookie(AL.'_lu', base64_encode(strrev(base64_encode($ses['user']))), time() + (86400 * 7), "/"); // 86400 = 1 day (*7=1 week)
                  $user->session->extend($ses['uid'],$ses); // extend session (if enabled) [is executed once during authorization, if we will move to user obj - should exec more?]
                  header('Location: ./'); exit;
                } else {
                  \my::log('err','Account is currently locked.');
                }
              } else {
                \my::log('err','Incorrect username or password.');
                \my::data(\mvc\v\user_v::loginForm());
              }
            } else {
              \my::log('err','Such user does not exist.');
              \my::data(\mvc\v\user_v::loginForm());
            }
          }
        } else {
          \my::log('err','Username or password is not valid.');
          \my::data(\mvc\v\user_v::loginForm());
        }
      } else {
        \my::log('err','Username or password is empty.');
        \my::data(\mvc\v\user_v::loginForm());
      }
    }

    public function logout()
    {
      $user=\my::user();
      $uid=$user->get('uid');
      if($uid>0)
      {
        $user->session->clean($uid); // removes session data only for specific app
        header('Location: ./'); exit;
      } else {
        \my::log('err','You not signed in.');
      }
    }

    public function passwd($uid=0,$pwd='')
    {
      $user=\my::user();
      if($uid=0){$uid=$user->get('uid');}
      if($uid>0 && $pwd!='')
      {
        if($this->valid('pwd',$pwd))
        {
          $db=\my::module('db');
          if($db->connected())
          {
            $sql='UPDATE `users` SET ``'; //? not colpleted !!!
            $stmt=$db->h->prepare($sql);
            ///$stmt->execute(array(':user'=>$username));
            $db->qcount();
          }
        } else {
          \my::log('err','Password is not valid');
        }
      }
    }

    public function valid($type,$value)
    {
      $valid=false;
      $len=strlen($value);
      switch ($type) {
        case 'login':
          if(\my::regex($value,'/^[a-z0-9]+$/') && ($len>=3 && $len<128))
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
            if(\my::regex($value,'/^[a-zA-Z0-9]+$/'))
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
            $db=\my::modules('user');
            if($db->connected())
            {
              $sql='SELECT `uid`,`profile`,`u-user`,`pid`,`email` FROM `users` LEFT OUTER JOIN `hr-people` ON `profile`=`pid` WHERE `u-user` = :user;';
              $stmt=$db->h->prepare($sql);
              $stmt->execute(array(':user'=>$username));
              $db->qcount();
              if($stmt->rowCount()==1)
              {
                $r=$stmt->fetch();
                $email=trim($r['u-email']);
                $uid=(int) $r['u-uid'];
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
                    \my::log('info','Special message has been sent to your E-mail with the appropriate instructions');
                  } else {
                    \my::log('err','Some problems with sending email');
                  }
                } else {
                  \my::log('err','There is no linked email address');
                }
              }
            }
          } else {
            \my::log('err','Username is not valid');
          }
        } else {
          \my::log('err','[CAPTCHA]: Verification code did not match');
        }
      }
      return $ok;
    }

    public function iforgot_clean()
    {
      $db=\my::module('db');
      if($db->connected())
      {
        // clean old iforgot records
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
      $result=false;
      if($link!='' && $this->valid('link',$link))
      {
        $db=\my::module('db');
        if($db->connected())
        {
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
        \my::log('error','Link is not valid');
      }
      return $result;
    }

    public function iforgot_passwd()
    {
      //\my::log('info','passwd');

    }

}
