<?php
class user
{
    private $uid=0; // user id
    private $gid=0; // primary group id
    private $gids=array(0); // groups
    private $pid=0; // profile id
    private $name=''; // username
    public $session=null;
    public $profile=null;

    function __construct()
    {
      $this->session = new user_session();
      $this->init();
      //my::log('debug','uid='.$this->get('uid').', gid='.$this->get('gid').', gids='.print_r($this->get('gids'),true));
    }

    private function init()
    {
      if($this->session->started())
      {
        $uid = (int) $this->session->get('uid');
        $gids = $this->session->get('gids');
        $pid = (int) $this->session->get('pid');
        if($uid>0 && count($gids)>0)
        {
            $this->uid=$uid;
            $this->gids=$gids;
            $this->gid=(int) $gids[0];
            $this->name=htmlspecialchars($this->session->get('user'));
            $this->pid=$pid;
        }
        $this->profile = new user_profile($this->pid);
      }
    }

    public function get($key)
    {
      switch ($key) {
        case 'uid':
          return $this->uid;
          break;
        case 'gid':
          return $this->gid;
          break;
        case 'gids':
          return $this->gids;
          break;
        case 'name':
          return $this->name;
          break;
        case 'pid':
          return $this->pid;
          break;
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
      $granted=false;
      
      if($this->isGuest())
      {
        if($c!='user' && $c!='page')
        {
          \mvc\v\user_v::login_form_modal();
        }
      }
      //my::log('debug','access control: c='.htmlspecialchars($c).', act='.htmlspecialchars($act));

      if($this->gid==1) {$granted=true;}
      if($c=='user' && $act=='login'){$granted=true;}
      if($c=='user' && $act=='logout'){$granted=true;}
      if($c=='page'){$granted=true;}
      // check ... should be completed!

      return $granted;
    }

}
