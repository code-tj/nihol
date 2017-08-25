<?php
class user
{
    private $uid=0;
    private $gid=0; // primary group id
    private $gids=array(0);
    private $name=''; // guest
    private $session_started=false;

    function __construct()
    {
      $this->init();
      $this->es_check(); // check - is session needs to be extended
      //app::init()->log('debug','uid='.$this->get('uid').', gid='.$this->get('gid').', gids='.print_r($this->get('gids'),true));
    }

    public function session_start()
    {
      if(!$this->session_started){
        session_start();
        $this->session_started=true;
      }
    }

    private function init()
    {
      if(isset($_COOKIE['PHPSESSID'])){
        $this->session_start();
        $uid=(int) $this->session_get('uid');
        $gids=$this->session_get('gids');
        if($uid>0 && count($gids)>0)
        {
            $this->uid=$uid;
            $this->gids=$gids;
            $this->gid=(int) $gids[0];
            $this->name=htmlspecialchars($this->session_get('user'));
        }
      }
    }

    public function get($key)
    {
        if($key=='uid') return $this->uid;
        if($key=='gid') return $this->gid;
        if($key=='gids') return $this->gids;
        if($key=='name') return $this->name;
    }

    public function session_set($key,$val)
    {
        $_SESSION[AL.'_'.$key]=$val;
    }

    public function session_get($key)
    {
        if(isset($_SESSION[AL.'_'.$key]))
        {
            return $_SESSION[AL.'_'.$key];
        } else {
            return '';
        }
    }

    public function session_remove($key)
    {
        if(isset($_SESSION[AL.'_'.$key])) unset($_SESSION[AL.'_'.$key]);
    }

    public function session_remove_all()
    {
        $len=strlen(AL);
        foreach($_SESSION as $key=>$val)
        {
            if(substr($key,0,$len)==AL) { unset($_SESSION[$key]); }
        }
    }

    public function isGuest()
    {
        return $this->uid > 0 ? false : true;
    }

    public function isAdmin()
    {
        return $this->gid==1 ? true : false;
    }

    public function ac($c,$act)
    {
      // access control
      $granted=true; // by default false
      // check ... should be completed!
    //  if($c=='page') $granted=true;
    //  if($c=='user' && $act=='login') $granted=true;
    //  if($c=='user' && $act=='logout' && !$this->isGuest()) $granted=true;
    //  if($this->isAdmin()) $granted=true;

      return $granted;
    }

    public function es_make($ses) // make Session Longer
    {
      $app=\app::init();
      $id=md5(microtime().$ses['uid']);
      if($app->db())
      {
        // remove previous records for this uid
        $db=$app->db;
        $stmt=$db->h->prepare('DELETE FROM `user-sessions` WHERE `ses-uid`=:uid;');
        $stmt->execute(array('uid'=>$ses['uid']));
        $db->qcount();
        $stmt=$db->h->prepare('INSERT INTO `user-sessions` SET `ses-id`=:id,`ses-uid`=:uid,`ses-pulse`=NOW(),`ses-data`=:data;');
        $stmt->execute(array('id'=>$id,'uid'=>$ses['uid'],'data'=>json_encode($ses)));
        $db->qcount();
      }
      setcookie(AL.'_es', $id, strtotime(date('Y-m-d 23:59:00')), "/"); // until the end of day
    }

    public function es_check()
    {
      if($this->uid==0 && isset($_COOKIE[AL.'_es']))
      {
        $es_id=$this->clean_str($_COOKIE[AL.'_es']);
        $app=app::init();
        if($app->db())
        {
          $db=$app->db;
          $stmt=$db->h->prepare('SELECT * FROM `user-sessions` WHERE `ses-id`=:id;');
          $stmt->execute(array('id'=>$es_id));
          $db->qcount();
          if($stmt->rowCount()==1)
          {
            $r=$stmt->fetch();
            $ses=json_decode($r['ses-data'],true);
            $this->session_start();
            $this->session_set('uid',$ses['uid']);
            $this->session_set('gid',$ses['gid']);
            $this->session_set('gids',$ses['gids']);
            $this->session_set('user',$ses['user']);
            $this->init();
            $app->log('debug','[user] session restored');
          } else {
            unset($_COOKIE[AL.'_es']);
            setcookie(AL.'_es', null, -1, '/');
          }
        }
      }
    }

    public function clean_str($string)
    {
      $string = str_replace(' ', '-', $string);
      return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

    public function es_clean($full=false)
    {
      if($this->uid > 0)
      {
        unset($_COOKIE[AL.'_es']);
        setcookie(AL.'_es', null, -1, '/');
        if($full)
        {
          $app=app::init();
          if($app->db())
          {
            $db=$app->db;
            $stmt=$db->h->prepare('DELETE FROM `user-sessions` WHERE `ses-uid`=:uid;');
            $stmt->execute(array('uid'=>$this->uid));
            $db->qcount();
          }
        }
      }
    }

}
