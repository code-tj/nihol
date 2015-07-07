<?php
class CORE {

	private static $inst; // instance for singleton

    private $msg_arr=array('error'=>'','info'=>'','debug'=>'');
    // modules: 0 - core; 1 - app;
    private $modules=array('user'=>0,'page'=>0);
    public $dbcon=false;
    // language parameters
    public $lang='';
    public $langs=array('en'=>'English','ru'=>'Русский','tj'=>'Тоҷикӣ');
	public $langfile=false;
	public $lng=array();

    //private function __construct() {}
    //private function __clone() {}
    //private function __wakeup() {}

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
            // initialization
            CORE::check_mode();
            spl_autoload_register('CORE::AutoLoader');
            CORE::msg('debug','core initialization');
            SESSION::init();
            CORE::check_lang();
        }
        return self::$inst; // singleton pattern
    }

    public static function check_mode() {
        switch(N_MODE){
            case 1:
                echo 'Currently down for maintenance.'; exit;
            break;
        }
    }

    public static function AutoLoader($class) {
        if(CORE::isValid($class,'/^[\\a-zA-Z0-9]+$/')){
            $cls=strtolower(str_replace('\\', '/', $class));
            $path='';
            if(substr($cls,0,5)=='core/'){
                $path=DIR_CORE.'/'.substr($cls,5).'.php';
            } elseif(substr($cls,0,4)=='app/') {
                $path=DIR_APP.'/'.substr($cls,4).'.php';
            }
            if(is_readable($path)) {
                require $path;
                //CORE::msg('debug','autoloader: '.$class); 
            } else {
                CORE::msg('debug','Can not find required class: '.$class);
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
            if($type=='debug') {
                if(N_DEBUG==0) { return; } else { if(isset($_GET['ajax'])){ return; } }
            } else {
                if(isset($_GET['ajax'])) { echo $msg; return; }
            }            
            if(isset(CORE::init()->msg_arr[$type])){
                CORE::init()->msg_arr[$type].=htmlspecialchars($msg)."<br>\n";
            }
        }
    }

    public static function get_msg_arr(){ return CORE::init()->msg_arr; }

    public static function check_lang(){
        global $conf;
        if(isset($conf['lang'])){
            $lang=$conf['lang'];
            $langs=CORE::init()->langs;
            $ln=COOKIE::get('lang');
            if($ln!='') $lang=$ln;
            if(isset($_GET['lang'])){
                $ln=trim($_GET['lang']);
                if(isset($langs[$ln])) {
                    COOKIE::set('lang',$ln);
                    $lang=$ln;
                }
            }
            if(isset($langs[$lang])) { CORE::init()->lang=$lang; }
            CORE::msg('debug','language: '.CORE::init()->lang);
        }
    }

	public function lang($alias,$default=''){
        $result=$default;
        if($this->lang!=''){
    		if(!$this->langfile){
    			if(is_readable(DIR_CORE.'/lang/'.$this->lang.'.php')){
    				include(DIR_CORE.'/lang/'.$this->lang.'.php');
    				$this->lng=$lng;
    				$this->langfile=true;
    				//CORE::msg('debug','core language file loaded');
    			} else { CORE::msg('debug','core language file is not loaded'); }
    		}
            //CORE::msg('debug','lng: '.$alias);
    		if(isset($this->lng[$alias])){
    			$result=$this->lng[$alias];
    		}
        }
        return $result;
	}

    public function get_modules(){ return $this->modules; }
    public function set_modules($new_modules,$redefine=false){
        foreach($new_modules as $key => $value){
            if(!isset($this->modules[$key]) || $redefine){
                $this->modules[$key]=$value;
            }
        }
    }

    public function includes(){
        $inc=get_included_files();
        if(count($inc)>1){
            $this::msg('debug','included:');
            foreach ($inc as $key => $val) {
                $this::msg('debug',($key+1). ') '.$val);
            }
        }
    }

    public function unload(){
        // what if php exit()... maybe needs some register_shutdown_function()
        //$this->includes();
        if($this->dbcon){DB::init()->close();}
    }

}

class COOKIE {
    public static function get($key=""){
      $result='';
      if(CORE::isValid($key)){
        if(isset($_COOKIE[PREFX.$key])){$result=$_COOKIE[PREFX.$key];}
      }
      return $result;
    }
    public static function set($key,$val,$time=0){
      if(CORE::isValid($key)){
        if($time==0) {$time=time()+86400;} // 1 day
        setcookie(PREFX.$key,$val,$time);
      }
    }
    public static function remove($key){
      if(CORE::isValid($key)){
        if(isset($_COOKIE[PREFX.$key])) {
            unset($_COOKIE[PREFX.$key]);
            setcookie($_COOKIE[PREFX.$key],null,-1);
        }
      }
    }
}

class SESSION {
    public static function init(){
        // if(isset($_COOKIE[PREFX.'st'])){SESSION::start();}
        if(isset($_COOKIE['PHPSESSID'])){SESSION::start();}
    }
    public static function start(){
        if(session_id()==''){
        session_start();
        CORE::msg('debug','starting session');
        }
    }
    public static function get($key=""){
      $result='';
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
            // session_id(),session_save_path(),sys_get_temp_dir(),uniqid()
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
        // ! this method is not completed !
        $result=false;
        $name=$c.','.$act;
        \CORE::msg('debug','Checking acl: '.$name);
        $USER=\CORE\BC\USER::init();
        $uid=$USER->get('uid');
        $gid=$USER->get('gid');
        //dafault acl settings
            $group_acl[',']=1;
            $group_acl['page,ciscocall']=1;
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

public function connect(){
    if(!$this->connected){
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
    return $this->connected;
}

public function connected(){ return $this->connected; }

public function query_count($q=0){ if($q==0){$this->queries++;} else {$this->queries+=(int) $q;} }

public function close(){
    if($this->dbh!=null && $this->connected){
        $this->dbh=null;
        $this->connected=false;
        CORE::init()->dbcon=false;
        CORE::msg('debug','Closing db connection (queries: '.$this->queries.')');
    }
}

public function isUnique($tbl='',$fld='',$val='',$err_msg='This entry already exists in the database.'){
    $unique=true;
    if($this->dbh!=null){
        $sql = "SELECT * FROM `".$tbl."` WHERE `".$fld."`=:val;";
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array('val'=>$val));
        if($sth->rowCount()>0){
            $unique=false;
            CORE::msg('error',$err_msg);
        }
    }
    return $unique;
}

public function del($tbl='',$fld='',$id=0){
    $deleted=false;
        $id=(int) $id;
        if($id>0){
            $DB=\DB::init();
            if($DB->connect()){
                $sql = "DELETE FROM `".$tbl."` WHERE `".$fld."`=:id;";
                $sth = $DB->dbh->prepare($sql);
                $sth->execute(array('id'=>$id));
                $DB->query_count();
                $deleted=true;
            }
        }
    return $deleted;
}

}