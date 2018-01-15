<?php
class user_session
{
  private $started=false;
  private $extend=true; // extended session flag

  function __construct()
  {
    $this->start();
    $this->ext();
  }

  public function started()
  {
    return $this->started;
  }

  public function start()
  {
    if(isset($_COOKIE['PHPSESSID']))
    {
      if(!$this->started)
      {
        session_start();
        $this->started=true;
      }
    } else {
      // ...
    }
  }

  public function set($key,$val)
  {
      $_SESSION[AL.'_'.$key]=$val;
  }

  public function get($key)
  {
      if(isset($_SESSION[AL.'_'.$key]))
      {
          return $_SESSION[AL.'_'.$key];
      } else {
          return '';
      }
  }

  public function unset($key)
  {
      if(isset($_SESSION[AL.'_'.$key])) unset($_SESSION[AL.'_'.$key]);
  }

  public function clean($uid)
  {
    if($this->extend)
    {
      $this->ext_clean($uid);
    }
    $len=strlen(AL);
    foreach($_SESSION as $key=>$val)
    {
        if(substr($key,0,$len)==AL) { unset($_SESSION[$key]); }
    }
  }

  // extended session

  public function str_clean($string)
  {
    $string = str_replace(' ', '-', $string);
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
  }

  public function extend($uid,$ses) // make Session Longer
  {
    if($this->extend)
    {
      $db=my::module('db');
      $es_id=md5(microtime().$uid);
      if($db->connected())
      {
        // remove previous records for this uid
        $stmt=$db->h->prepare('DELETE FROM `user-sessions` WHERE `ses-uid`=:uid;');
        $stmt->execute(array('uid'=>$uid));
        $db->qcount();
        $stmt=$db->h->prepare('INSERT INTO `user-sessions` SET `ses-id`=:id,`ses-uid`=:uid,`ses-pulse`=NOW(),`ses-data`=:data;');
        $stmt->execute(array('id'=>$es_id,'uid'=>$uid,'data'=>json_encode($ses)));
        $db->qcount();
      }
      setcookie(AL.'_es', $es_id, strtotime(date('Y-m-d 23:59:00')), "/"); // until the end of day
    }
  }

  public function ext()
  {
    $uid = (int) $this->get('uid');
    if($uid==0 && $this->extend)
    {
      if(isset($_COOKIE[AL.'_es']))
      {
        $es_id=$this->str_clean($_COOKIE[AL.'_es']);
        $db=my::module('db');
        if($db->connected())
        {
          $stmt=$db->h->prepare('SELECT * FROM `user-sessions` WHERE `ses-id`=:id;');
          $stmt->execute(array('id'=>$es_id));
          $db->qcount();
          if($stmt->rowCount()==1)
          {
            $r=$stmt->fetch();
            $ses=json_decode($r['ses-data'],true);
            $this->start();
            $this->set('uid',$ses['uid']);
            $this->set('gid',$ses['gid']);
            $this->set('gids',$ses['gids']);
            $this->set('user',$ses['user']);
            $this->set('pid',$ses['pid']);

            my::log('debug','[user] session extended');
          } else {
            unset($_COOKIE[AL.'_es']);
            setcookie(AL.'_es', null, -1, '/');
          }
        }
      }
    }
  }

  public function ext_clean($uid,$full=false)
  {
    if($uid>0)
    {
      unset($_COOKIE[AL.'_es']);
      setcookie(AL.'_es', null, -1, '/');
      if($full)
      {
        $db=my::module('db');
        if($db->connected())
        {
          $stmt=$db->h->prepare('DELETE FROM `user-sessions` WHERE `ses-uid`=:uid;');
          $stmt->execute(array('uid'=>$uid));
          $db->qcount();
        }
      }
    }
  }

}
