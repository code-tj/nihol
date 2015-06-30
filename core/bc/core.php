<?php
class CORE {

	private static $inst; // instance for singleton

    private $msg_arr=array('error'=>'','info'=>'','debug'=>'');
    // modules: 0 - core; 1 - app;
    private $modules=array('user'=>0,'page'=>0);
    public $dbcon=false;
    // language parameters
    public $lang='en';
    public $langs=array('en'=>'English','ru'=>'Русский','tj'=>'Тоҷикӣ');
	public $langfile=false;
	public $lng=array();

    private function __construct() {
        spl_autoload_register('CORE::AutoLoader');
    }

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
            // additional
            CORE::check_mode();
            CORE::msg('debug','core initialization');
            SESSION::init();
            CORE::check_lang();
        }
        return self::$inst; // singleton pattern
    }

    public static function check_mode() {
        switch(N_MODE){
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
                $path=DIR_CORE.'/'.substr($class,5).'.php';
            } elseif(substr($class,0,4)=='app/') {
                $path=DIR_APP.'/'.substr($class,4).'.php';
            }
                if(is_readable($path)) {
                require $path;
                //CORE::msg('debug','AutoLoader: '.$class);
                } else {
                    CORE::msg('debug','Can not find required class: '.$class);
                    //CORE::msg('debug','Required path: '.$path);
                }
        } else {
            CORE::msg('debug','Not valid class name required: '.$class);
        }
    }

    public static function isValid($str,$regex='/^[a-zA-Z0-9_]+$/'){
        if(preg_match($regex,$str)){ return true; } else {return false;}
    }

    public static function msg($type='debug',$msg='') {
        if($msg!=''){
            if($type=='debug' && N_DEBUG==0) { return; }
            if(isset(CORE::init()->msg_arr[$type])){
                CORE::init()->msg_arr[$type].=htmlspecialchars($msg)."<br>\n";
            }
        }
    }

    public static function get_msg_arr(){ return CORE::init()->msg_arr; }

    public static function check_lang(){
        global $conf;
        if(isset($conf['lang'])){
            $langs=CORE::init()->langs;
            // set language
            $ln=SESSION::get('lang');
            if(isset($_GET['lang'])){ $ln=trim($_GET['lang']); }
            if(isset($langs[$ln])) { CORE::init()->lang=$ln; } else {CORE::init()->lang=$conf['lang'];}
            CORE::msg('debug','language: '.CORE::init()->lang);
        }
    }

	public static function lang($alias,$default=''){
		$lang=CORE::init()->lang;
		if(!CORE::init()->langfile){
			if(is_readable(DIR_CORE.'/lang/'.$lang.'.php')){
				include(DIR_CORE.'/lang/'.$lang.'.php');
				CORE::init()->lng=$lng;
				CORE::init()->langfile=true;
				//CORE::msg('debug','core language file loaded');
			} else { CORE::msg('debug','core language file not loaded'); }
		}
		if(isset(CORE::init()->lng[$alias])){
			return CORE::init()->lng[$alias];
		} else {
			return $default;
		}
	}

    public function get_modules(){ return $this->modules; }

    public function includes(){
        $inc=get_included_files();
        if(count($inc)>1){
            $this::msg('debug','included:');
            foreach ($inc as $key => $val) {
                $this::msg('debug',($key+1). ') '.$val);
            }
        }
    }

    public static function unload(){
        //CORE::init()->includes();
        if(CORE::init()->dbcon){DB::init()->close();}
    }

    public function test($str=''){
        \CORE\BC\UI::init()->pos['main'].=(htmlspecialchars($str)."<br>");
    }

}

class SESSION {
public static function init($mode=0){
  if($mode==1){
    SESSION::start();
  } else {
    // if(isset($_COOKIE[PREFX.'st'])){SESSION::start();}
    if(isset($_COOKIE['PHPSESSID'])){SESSION::start();}
  }
}
    public static function start(){
        if(session_id()==''){
        session_start();
        // CORE::msg('debug','starting session: '.session_id());
        CORE::msg('debug','starting session');
        // uniqid(),session_save_path(),sys_get_temp_dir(),session_id()
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

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    public function acl($c='',$act=''){
        $result=false;
        $name=$c.','.$act;
        \CORE::msg('debug','Checking acl: '.$name);
        $USER=\CORE\BC\USER::init();
        $uid=$USER->get('uid');
        $gid=$USER->get('gid');
        //dafault acl settings
            $group_acl[',']=1;
            if($gid==0) {$group_acl['user,login']=1;} else {
                $group_acl['user,logout']=1;
                $group_acl['user,profile']=1;
            }
            if($gid==1) {$group_acl['*,*']=1;}
        // loading acl
            // ...
        // allow
            // user
            if(isset($user_acl[$name])){
                if($user_acl[$name]==1){
                    $result=true;
                }
            }
            // group
            if(isset($group_acl['*,*'])){if($group_acl['*,*']==1){$result=true;}}
            if(isset($group_acl[$name])){
                if($group_acl[$name]==1){
                    $result=true;
                }
            }
        // deny
            // ...
        if(!$result) \CORE::msg('error','Access denied');
        return $result;
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
