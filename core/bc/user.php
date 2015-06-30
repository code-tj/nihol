<?php
namespace CORE\BC;

class USER {

    private static $inst;

    private $uid=0; // user id
    private $gid=0; // group id
    private $pid=0; // profile id
    private $username=''; // username

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    private function __construct() {
        $uid=\SESSION::get('uid');
        if($uid!=''){
            $this->uid=(int) $uid;
            if($this->uid>0){
                $gid=\SESSION::get('gid');
                if($gid!='') $this->gid=(int) $gid;
                $pid=\SESSION::get('pid');
                if($pid!='') $this->pid=(int) $pid;
                $user=\SESSION::get('user');
                if($user!='') $this->username=$user;
            }
        }
        \CORE::msg('debug','user (uid:'.$this->uid.'; gid:'.$this->gid.';)');
    }

    public function get($item){
        if(isset($this->$item)) {
            return $result=$this->$item;
        } else {
            return '';
        }
    }

    public function auth(){
        $result=($this->uid > 0 ? true : false);
        return $result;
    }

}