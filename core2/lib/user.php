<?php
class USER {

    private static $inst;

    private $uid=0;
    private $gid=0;
    private $gids=array(0);
    private $name='guest';
    private $profile_id=0;
    //private $profile=null;
    public $session=null;

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    public function __construct(){
        $this->session=SESSION::init();
        if($this->session->start()){
            $uid=(int) $this->session->get('uid');
            $this->uid=$uid;
            $gid=(int) $this->session->get('gid'); // gid defines when user signing in (first init)
            $gids_tmp=$this->session->get('gids');
            if($gids_tmp!=''){
                $gids=explode(";",$gids_tmp);
                $gids_count=count($gids);
                for ($i=0; $i<$gids_count; $i++) {
                    $this->gids[]=(int) $gids[$i];
                }
            }
        }
        CORE::msg('uid='.$this->uid.'; gid='.$this->gid.';','debug');
    }

    public function get_uid(){
        return $this->uid;
    }

    public static function uid(){
        return USER::init()->get_uid();
    }

    public function get_gid(){
        return $this->gid;
    }

    public static function gid(){
        return USER::init()->get_gid();
    }

    public function get_gids(){
        return $this->gids;
    }

    public static function gids(){
        return USER::init()->get_gids();
    }

    public function get_name(){
        return $this->name;
    }

    public function profile_id(){
        return $this->profile_id;
    }

}

class SESSION {
    private static $instance;

    public static function init(){
        if(empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public function start(){
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();
            CORE::msg('session_start','debug');
            //CORE::msg('session_id: '.session_id(),'debug');
            return true;
        } else {
            return false;
        }
    }

    public function get($key=""){
        $result='';
        if(isset($_SESSION[PREFIX.$key])){
            $result=$_SESSION[PREFIX.$key];
        }
        return $result;
    }

    public function set($key,$val){
        if(CORE::check_regex($key)){
            $_SESSION[PREFIX.$key]=$val;
        }
    }

    public function remove($key){
        if(CORE::check_regex($key)){
            unset($_SESSION[PREFIX.$key]);
        }
    }

    public function remove_all(){
        $len=strlen(PREFIX);
        foreach($_SESSION as $key=>$val){
            if(substr($key,0,$len)==PREFIX){
                unset($_SESSION[$key]);
            }
        }
    }

}

class COOKIE {
    public static function get($key=""){
      $result='';
      if(CORE::check_regex($key)){
        if(isset($_COOKIE[PREFIX.$key])){$result=$_COOKIE[PREFIX.$key];}
      }
      return $result;
    }
    public static function set($key,$val,$time=0){
      if(CORE::check_regex($key)){
        if($time==0) {$time=time()+86400;} // 1 day - default time for app
        setcookie(PREFIX.$key,$val,$time);
      }
    }
    public static function remove($key){
      if(CORE::check_regex($key)){
        if(isset($_COOKIE[PREFIX.$key])) {
            unset($_COOKIE[PREFIX.$key]);
            setcookie($_COOKIE[PREFIX.$key],null,-1);
        }
      }
    }
}