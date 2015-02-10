<?php
# main core script
if(!defined('BDIR')){echo '[+_+]'; exit;}

class CORE {

	private static $inst; // instance (singleton)

    private $msg=array('error'=>'','info'=>'','debug'=>'');

    private function __construct() {
        spl_autoload_register('CORE::CLoader'); // autoloader's initialisation
    }

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
            // additional part or init
            CORE::MSG('debug','CORE init');
            CORE::check_mode(XMODE);
            SESSION::init();
        }
        return self::$inst; // singleton pattern
    }

    public static function check_mode($mode=0) {
    	switch($mode){
			case 1: // Maintenance mode
				echo '<h2 style="text-align:center;">Maintenance... We will be back soon.</h2>';
				exit;
			break;
		}
        CORE::MSG('debug','Checking mode');
    }

    public static function CLoader($ClassName) { // AutoLoader method

        if(CORE::isValid($ClassName) && strlen($ClassName)<256) {
            $path=CDIR.'/cls/'.strtolower($ClassName).'.php';
            if(is_readable($path)) { 
                require $path;
                CORE::MSG('debug','Require: '.$path.' (class '.$ClassName.')');
            }
        } else {
            if(CORE::isValid($ClassName,'/^[a-zA-Z0-9\\\_]+$/')) {
                if(strpos($ClassName,'CORE')===0) {
                    $ClassName=substr($ClassName,5);
                    $path=strtolower(str_replace("\\", "/", $ClassName));
                    $path=CDIR.'/'.$path.'.php';
                } else {
                    if(strpos($ClassName,'APP')===0) {
                        $ClassName=substr($ClassName,4);
                        $path=strtolower(str_replace("\\", "/", $ClassName));
                        $path=APPDIR.'/'.$path.'.php';                        
                    }
                }
                if(is_readable($path)) {
                    require $path;
                    CORE::MSG('debug','Require: '.$path.' ('.$ClassName.')');
                }
            }

        }

    }

    public static function isValid($str='',$pattern='/^[a-zA-Z0-9_]+$/'){ // ClassName validation method
        if(preg_match($pattern,$str)==1){return true;} else {return false;}
    }

    public static function MSG($type='debug',$txt='') {
        $txt=trim($txt);
        if(XDEBUG==0 && $type=='debug') { return; }
        if( $txt!='' && isset(CORE::init()->msg[$type]) ) {
            CORE::init()->msg[$type].=htmlspecialchars($txt)."<br>\n";
        }
    }

    public static function show($type='') {
        $result='';
        if($type!='') {
            if(isset(CORE::init()->msg[$type])) {
                $result.=CORE::init()->msg[$type];
            }
        }
        if($result!='') {
            $style=''; $type_txt='';
            switch ($type) {
                case 'info': $style=' class="alert alert-info alert-dismissable"'; $type_txt=$type; break;
                case 'error': $style=' class="alert alert-danger alert-dismissable"'; $type_txt=$type; break;
                case 'debug': $style=' class="alert alert-warning alert-dismissable"'; $type_txt=$type; break;
            }
            $type_txt=strtoupper($type_txt);
$s1='<div'.$style.' role="alert">
<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
<strong>'.$type_txt.':</strong><br>
';
            $s2="</div>\n";
            $result=$s1.$result.$s2;
        }
        echo $result;
    }

}

class SEC {

    private static $inst; // instance (singleton)

    private $acl=array( /// ??
        '1'=>'1',
        );

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst; // singleton pattern
    }

    private function __construct() {
        CORE::MSG('debug','SEC init');
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
    CORE::MSG('debug','Session start'); //session_id()
    //CORE::MSG('debug',session_save_path());
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
    if(isset($_SESSION[APPREFIX.$key])){$result=$_SESSION[APPREFIX.$key];}
  }
  return $result;
}
public static function get_all(){
  $len=strlen(APPREFIX);
  foreach($_SESSION as $key=>$val){if(substr($key,0,$len)==APPREFIX){$session_params[$key]=$val;}}
  if(isset($session_params)) {return $session_params;} else {return array();}
}
public static function set($key,$val){
  if(SESSION::isValidKey($key)){
  $_SESSION[APPREFIX.$key]=$val;
  }
}
public static function remove($key){
  if(SESSION::isValidKey($key)){
  unset($_SESSION[APPREFIX.$key]);
  }
}
public static function remove_all(){
  $len=strlen(APPREFIX);
  foreach($_SESSION as $key=>$val){if(substr($key,0,$len)==APPREFIX){unset($_SESSION[$key]);}}
}
public static function isValidKey($key){
  if($key!="" && preg_match('/^[A-Za-z0-9_]+$/',$key)){
    return true;
  } else {return flase;}
}

}

class REQUEST {

    private static $inst; // instance (singleton)

    private $param=array(
        'c'=>'', // controller
        'act'=>'', // action
        'do'=>'',
        );

    function __construct(){
        if(isset($_GET['c']) && $this->valid($_GET['c'])){
            $this->param['c']=trim($_GET['c']);
        }
        if(isset($_GET['act']) && $this->valid($_GET['act'])){
            $this->param['act']=trim($_GET['act']);
        }
        if(isset($_GET['do']) && $this->valid($_GET['do'],'/^[a-zA-Z0-9_]+$/')){
            $this->param['do']=trim($_GET['do']);
        }
    }

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst; // singleton pattern
    }

    public function valid($str='',$pattern='/^[a-z]+$/'){
        if(preg_match($pattern,$str)==1 && strlen($str)<256){return true;} else {return false;}
    }

    public function get($key=''){
        $result='';
        if(isset($this->param[$key])){$result=$this->param[$key];}
        return $result;
    }

}

class ROUTER {
    private static $inst; // instance (singleton)

    private function __construct($REQUEST,$xmods) {
        CORE::MSG('debug','ROUTER init');
        if($REQUEST!==null){
            UI::init()->pos['title']=strtoupper(APPNAME);
            $modname=$REQUEST->get('c');
            if($modname!=''){
                if(USER::init()->acl()){
                    // before include and create controller -> check ACL for c && act
                    // include here only controllers
                    if(isset($xmods[$modname])){

                        $model=null;
                        $view=null;
                        $controller=null;

                        $MODNAME['n']=strtoupper($modname); // just current uppercase name
                        $MODNAME['m']=$MODNAME['n'].'_M';
                        $MODNAME['v']=$MODNAME['n'].'_V';
                        $MODNAME['c']=$MODNAME['n'].'_C';

                        $nspace['m']='\\MVC\\M\\'.$MODNAME['m'];
                        $nspace['v']='\\MVC\\V\\'.$MODNAME['v'];
                        $nspace['c']='\\MVC\\C\\'.$MODNAME['c'];

                        if($xmods[$modname]==1){
                            $nspace['m']='CORE'.$nspace['m'];
                            $nspace['v']='CORE'.$nspace['v'];
                            $nspace['c']='CORE'.$nspace['c'];
                        } else {
                            $nspace['m']='APP'.$nspace['m'];
                            $nspace['v']='APP'.$nspace['v'];
                            $nspace['c']='APP'.$nspace['c'];
                        }

                        // here automatically tries to include class-file  
                        if(class_exists($nspace['m'])) {}
                        if(class_exists($nspace['v'])) {}
                        if(class_exists($nspace['c'])) {}

                        if(class_exists($MODNAME['m'])){
                            $model = new $MODNAME['m'](); /// NO namespace  ))
                            //CORE::MSG('debug','Object '.$MODNAME['m'].' created');
                        } else { CORE::MSG('debug','Class '.$MODNAME['m'].' not exist'); }

                        if(class_exists($MODNAME['v'])){
                            $view = new $MODNAME['v']();
                            //CORE::MSG('debug','Object '.$MODNAME['v'].' created');
                        } else { CORE::MSG('debug','Class '.$MODNAME['v'].' not exist'); }

                        if(class_exists($MODNAME['c'])){
                            $controller = new $MODNAME['c']($model,$view);
                            //CORE::MSG('debug','Object '.$MODNAME['c'].' created');
                        } else { CORE::MSG('debug','Class '.$MODNAME['c'].' not exist'); }

                    } else {
                        CORE::MSG('error','Such module is not registered');
                    }
                }
            } else {
                
                if($REQUEST->get('page')!=''){
                    // put here or inside UI class ACL checking for pages
                    UI::init()->spage($REQUEST->get('page'));
                } else {
                    // UI default front page content
                    // specific page for each type of user
                    if(USER::init()->get('uid')==0) {
                        UI::init()->spage('guest');
                    } else {                        
                        if(USER::init()->get('gid')==1){
                            UI::init()->spage('admin');
                        } else {
                            UI::init()->spage('user');
                        }
                    }
                    //print_r(get_defined_constants(true));
                }

            }
        }
    }

    public static function init($REQUEST=null,$xmods=array()) {
        if(empty(self::$inst)) {
            self::$inst = new self($REQUEST,$xmods);
        }
        return self::$inst; // singleton pattern
    }
}

// core initialization

CORE::init();
$USER=USER::init();
$UI=UI::init();
$REQUEST=REQUEST::init();
ROUTER::init($REQUEST,$xmods); // check xmods
if(isset($conf['dbcon'])){DB::init()->close();}
if($UI->tpl()!=''){include($UI->tpl());} // template
// echo '<pre>'; print_r(get_included_files()); echo '</pre>';