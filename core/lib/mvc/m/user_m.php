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
          if($this->app->db())
          {
            $db=$this->app->db;
            $stmt=$db->h->prepare('SELECT * FROM users WHERE username = ?;');
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
                  //$this->app->log->set('debug','[user] authorization...');
                  $uid=(int) $r['uid'];
                  $gids=array();
  								$gids_tmp=explode(",",$r['gids']);
                  foreach ($gids_tmp as $index => $gid_str) {
                    $gids[]=(int) $gid_str;
                  }
                  $this->app->user->session_set('uid',$uid);
                  $this->app->user->session_set('gid',$gids[0]);
                  $this->app->user->session_set('gids',$gids);
                  // username?
                  // cookies, remember... todo!

                  ///$this->app->log->set('debug',print_r($gids,true));
                  header('Location: ./'); exit;
                } else {
                  $this->app->log->set('err','Account is currently locked.');
                }
              } else {
                $this->app->log->set('err','Incorrect username or password.');
              }
            } else {
              $this->app->log->set('err','Such user does not exist.');
            }
          }
        } else {
          $this->app->log->set('err','Login or password is not valid.');
        }
      } else {
        $this->app->log->set('err','Login and password are empty.');
      }
    }

    public function logout()
    {
      if($this->app->user->session_get('uid')!='')
      {
        $this->app->user->session_remove_all(); // removes session data only for specific app
        header('Location: ./'); exit;
      } else {
        $this->app->log->set('err','You not signed in.');
      }
    }

    public function valid($type,$value)
    {
      $valid=false;
      $len=strlen($value);
      // validation ...
      switch ($type) {
        case 'login':
          if($this->app::regex($value,'/^[a-z0-9]+$/') && ($len>=3 && $len<128))
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
      }
      return $valid;
    }

}
