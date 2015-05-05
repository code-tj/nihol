<?php
class CORE {

	private static $inst; // instance for singleton
    private $messages=array('error'=>'','info'=>'','debug'=>'');

    private function __construct() {
        spl_autoload_register('CORE::AutoLoader');

    }

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
            // additional
            CORE::CheckMode(NL_MODE);
            SESSION::init();
        }
        return self::$inst; // singleton pattern
    }

    public static function CheckMode($mode=0) {
    	switch($mode){
			case 1: // Maintenance mode
				echo 'Maintenance... We\'ll be back soon.';
				exit;
			break;
		}
    }

    public static function AutoLoader($class) {
        if(CORE::isValid($class,'/^[\\a-zA-Z0-9]+$/')){
            $class=strtolower(str_replace('\\', '/', $class));
            $path='';
            if(substr($class,0,5)=='core/'){
                $path=CDIR.'/'.substr($class,5).'.php';
            } elseif(substr($class,0,4)=='app/') {
                $path=ADIR.'/'.substr($class,4).'.php';
            }
                if(is_readable($path)) { 
                require $path;
                //CORE::msg('debug','AutoLoader: '.$class);
                } else {
                    CORE::msg('debug','Can not find required class: '.$class);
                    ///echo $path.'<br>';
                }
        } else {
            CORE::msg('debug','Not valid class required: '.$class);
        }
    }

    public static function isValid($str='',$pattern='/^[a-zA-Z0-9_]+$/'){ // ClassName validation method
        if(preg_match($pattern,$str)==1){return true;} else {return false;}
    }

    public static function msg($type='debug',$txt='') {
        $txt=trim($txt);
        if(NL_DEBUG==0 && $type=='debug') { return; }
        if( $txt!='' && isset(CORE::init()->messages[$type]) ) {
            CORE::init()->messages[$type].=htmlspecialchars($txt)."<br>\n";
        }
    }

    public static function show($type='') {
        $result='';
        if($type!='') {
            if(isset(CORE::init()->messages[$type])) {
                $result.=CORE::init()->messages[$type];
            }
        }
        if($result!='') {
            $style=''; $title='';
            switch ($type) {
                case 'info': $style=' class="alert alert-info alert-dismissable"'; $title=$type; break;
                case 'error': $style=' class="alert alert-danger alert-dismissable"'; $title=$type; break;
                case 'debug': $style=' class="alert alert-warning alert-dismissable"'; $title=$type; break;
            }
            $title=strtoupper($title);
$s1='<div'.$style.' role="alert">
<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
<strong>'.$title.':</strong><br>
';
            $s2="</div>\n";
            $result=$s1.$result.$s2;
        }
        echo $result;
    }

    public function includes(){
        $inc=get_included_files();
        if(count($inc)>1){
            $this::msg('debug','Included:');
            foreach ($inc as $key => $val) {
                $this::msg('debug',($key+1). ') '.$val);
            }
        }
    }

    public static function unload(){
        //if($conf['db_con']){DB::init()->close();}
    }

}

class SESSION {
public static function init($mode=0){
  if($mode==1){
    SESSION::start();
  } else {
    if(isset($_COOKIE['PHPSESSID'])){SESSION::start();}
  }
}
public static function start(){
  if(session_id()==''){
    session_start();
    CORE::msg('debug','session start'); //session_id()
    //CORE::msg('debug',session_save_path());
    // session_id()
    // $_COOKIE["PHPSESSID"]
    // session_save_path(), sys_get_temp_dir()
    // session_destroy();
    // unset($_COOKIE['PHPSESSID']);
    // setcookie("PHPSESSID", "", 1);

    // or this would remove all the variables in the session, but not the session itself
    // session_unset();

    // this would destroy the session variables
    // session_destroy();

  }
}
public static function get($key=""){
  $result="";
  if(SESSION::isValidKey($key)){
    if(isset($_SESSION[PREFX.$key])){$result=$_SESSION[PREFX.$key];}
  }
  return $result;
}
public static function get_all(){
  $len=strlen(PREFX);
  foreach($_SESSION as $key=>$val){if(substr($key,0,$len)==PREFX){$session_params[$key]=$val;}}
  if(isset($session_params)) {return $session_params;} else {return array();}
}
public static function set($key,$val){
  if(SESSION::isValidKey($key)){
  $_SESSION[PREFX.$key]=$val;
  }
}
public static function remove($key){
  if(SESSION::isValidKey($key)){
  unset($_SESSION[PREFX.$key]);
  }
}
public static function remove_all(){
  $len=strlen(PREFX);
  foreach($_SESSION as $key=>$val){if(substr($key,0,$len)==PREFX){unset($_SESSION[$key]);}}
}
public static function isValidKey($key){
  if($key!="" && preg_match('/^[A-Za-z0-9_]+$/',$key)){
    return true;
  } else {return flase;}
}

}