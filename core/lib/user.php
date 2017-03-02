<?php
class user
{
    private $uid=0;
    private $gids=array(0);
    private $name=''; // guest
    public $app=null;

    function __construct()
    {
    	$this->app=app::init();
        $this->app->session = new session();
        $this->initialize();
    }

    private function initialize()
    {
        if($this->app->session->started())
        {
            $ses_uid=(int) $this->app->session->get('uid');
            $ses_gids=$this->app->session->get('gids');
            if($ses_uid>0 && count($ses_gids)>0)
            {
                $this->uid=$ses_uid;
                $this->gids=$ses_gids;
            }
        }
        //app::log('debug','uid='.$this->uid().', gid='.$this->gid().', gids='.print_r($this->gids(),true));
    }

    public function uid()
    {
        return $this->uid;
    }

    public function gid()
    {
        return $this->gids[0];
    }

    public function gids()
    {
        return $this->gids;
    }

    public function name()
    {
        return $this->name;
    }

    public function isGuest()
    {
        return $this->uid > 0 ? true : false;
    }
    
}