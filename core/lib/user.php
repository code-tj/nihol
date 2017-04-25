<?php
class user
{
    private $uid=0;
    private $gid=0; // primary gid
    private $gids=array(0);
    private $name=''; // guest
    //private $sid=''; // session_id
    //private $log=null;

    function __construct()
    {
    	//$this->app=app::init();
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            //$this->sid=session_id();
            //app::log('debug','session started');
            $uid=(int) $this->session_get('uid');
            $gids=$this->session_get('gids');
            if($uid>0 && count($gids)>0)
            {
                $this->uid=$uid;
                $this->gids=$gids;
                $this->gid=(int) $gids[0];
            }
        } else {
          // check special cookie flag if user remembered

        }
        //app::log('debug','uid='.$this->get('uid').', gid='.$this->get('gid').', gids='.print_r($this->get('gids'),true));
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

    public function ac($c,$act)
    {
      // access control
      $granted=true; // by default false
      // check ... should be completed!

      return $granted;
    }

}
