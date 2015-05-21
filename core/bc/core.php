<?php
class CORE {

	private static $inst; // instance for singleton
    private $messages=array('error'=>'','info'=>'','debug'=>'');
    private $modules=array('user'=>0);
    public $dbcon=false;

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
                    //CORE::msg('debug','Required path: '.$path);
                }
        } else {
            CORE::msg('debug','Not valid class required: '.$class);
        }
    }

    public static function isValid($str,$regex='/^[a-zA-Z0-9_]+$/'){
        if(preg_match($regex,$str)){ return true; } else {return false;}
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
$s1='<div'.$style.' style="margin-top:18px;" role="alert">
<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
<strong>'.$title.':</strong><br>
';
            $s2="</div>\n";
            $result=$s1.$result.$s2;
        }
        echo $result;
    }

    public function get_modules(){
        return $this->modules;
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
        //CORE::init()->includes();
        if(CORE::init()->dbcon){DB::init()->close();}
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
        CORE::msg('debug','starting session'); //session_id()
        // session_id(uniqid());
        // \CORE::msg('debug',session_save_path());
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
      if(CORE::isValid($key)){
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
      if(CORE::isValid($key)){
      $_SESSION[PREFX.$key]=$val;
      }
    }
    public static function remove($key){
      if(CORE::isValid($key)){
      unset($_SESSION[PREFX.$key]);
      }
    }
    public static function remove_all(){
      $len=strlen(PREFX);
      foreach($_SESSION as $key=>$val){if(substr($key,0,$len)==PREFX){unset($_SESSION[$key]);}}
    }
    public static function info(){
        if(isset($_SESSION)){
            \CORE::msg('debug','session_id: '.session_id());
            foreach($_SESSION as $k => $v){
                \CORE::msg('debug','$_SESSION["'.$k.'"] = '.$v);
            }
        }
    }
}

class SEC {
    private static $inst;

    private function __construct() {
        
    }

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    public static function check($REQUEST){
        return true; // temp
    }

}

class DB {

private static $inst;

private $connected=false;
private $queries=0;
public $dbh=null;

public static function init() {
    if(empty(self::$inst)) {
        self::$inst = new self();
    }
    return self::$inst;
}

private function __construct(){
    $this->connect();
}

private function connect(){
    global $conf;
    try {
        $dsn='mysql:host='.$conf['db_server'].';dbname='.$conf['db_name'];
        // .';charset='.$conf['db_charset']

        // PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        $opt=array(         
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        $this->dbh = new PDO($dsn,$conf['db_user'],$conf['db_pass'],$opt);
        ///$this->dbh->query('SET character_set_connection = '.$conf['db_charset'].';');
        ///$this->dbh->query('SET character_set_client = '.$conf['db_charset'].';');
        ///$this->dbh->query('SET character_set_results = '.$conf['db_charset'].';');
        $this->dbh->query('SET NAMES '.$conf['db_charset']);
        $this->connected=true;
        CORE::init()->dbcon=true;
        CORE::msg('debug','Connecting to database');
    } catch(PDOException $e) {
        CORE::msg('error','Some problems with db connection (check configuration)');
        CORE::msg('debug',$e->getMessage());
    }
    // after initializing DB cleaning db configuration parameters ( sec. reasons =)
    $conf['db_server']=''; $conf['db_name']=''; $conf['db_user']=''; $conf['db_pass']='';
}

public function connected(){ return $this->connected; }

public function query_count($q=0){ if($q==0){$this->queries++;} else {$this->queries+=(int) $q;} }

public function close(){
    if($this->dbh!=null){
        $this->dbh=null;
        $this->connected=false;
        CORE::init()->dbcon=false;
        CORE::msg('debug','Closing db connection (queries: '.$this->queries.')');
    }
}

}