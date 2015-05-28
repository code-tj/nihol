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
        // if isAuth() true
        $value=\SESSION::get('uid');
        if($value!=''){$this->uid=(int) $value;}
        if($this->uid>0){
        	$value=\SESSION::get('gid');
            if($value!=''){$this->gid=(int) $value;}
            $value=\SESSION::get('pid');
            if($value!=''){$this->pid=(int) $value;}
            $value=\SESSION::get('user');
            if($value!=''){$this->username=$value;}
        }
        \CORE::msg('debug','USER (uid:'.$this->uid.'; gid:'.$this->uid.';)');
    }

    public function get($item){
        $result='';
        if(isset($this->$item)) $result=$this->$item;
        return $result;
    }

    public function auth(){
        $result = ($this->uid > 0 ? true : false);
        return $result;
    }

}