<?php
class user
{
    private $uid=0;
    private $gids=array(0);
    private $name=''; // guest
    private $sid=''; // session_id
    public $app=null;

    function __construct()
    {
    	$this->app=app::init();
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();            
            $this->sid=session_id();
            //app::log('debug','session started');
            $ses_uid=(int) $this->session_get('uid');
            $ses_gids=$this->session_get('gids');
            if($ses_uid>0 && count($ses_gids)>0)
            {
                $this->uid=$ses_uid;
                $this->gids=$ses_gids;
            }
        }
        //app::log('debug','uid='.$this->get('uid').', gid='.$this->get('gid').', gids='.print_r($this->get('gids'),true));
    }

    public function get($key)
    {
        if($key=='uid') return $this->uid;
        if($key=='gid') return $this->gids[0];
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
        $granted=true;
        // access control check
        // ...
        return $granted;
    }
    
}