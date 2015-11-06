<?php
class CORE {

	private static $inst; // instance for singleton

    private $msg_arr=array('error'=>'','info'=>'','debug'=>'');
    // modules: 0 - core; 1 - app;
    private $modules=array('user'=>0,'group'=>0,'acl'=>0,'page'=>0);
    public $dbcon=false;
    // language parameters
    public $lang='';
    public $langs=array('en'=>'English','ru'=>'Русский','tj'=>'Тоҷикӣ');
	public $langfile=false;
	public $lng=array();
    // ajax mode
    public $ajax=false;

    private function __construct() {
        if(isset($_GET['ajax'])) $this->ajax=true;
    }
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
                if(N_DEBUG==0) { return; } else { if(CORE::init()->is_ajax()){ return; } }
            } else {
                if(CORE::init()->is_ajax()) { echo $msg; return; }
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

    public function is_ajax(){ return $this->ajax; }

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

    public function get_acl_db(){
        $acl=array();
        $DB=\DB::init();
        if($DB->connect()){
            //$where=" WHERE `acl-c`=:c";
            $sql="SELECT * FROM `n-acl`;";
            $sth=$DB->dbh->prepare($sql);
            $sth->execute();
            $DB->query_count();
            if($sth->rowCount()>0){
                while($r=$sth->fetch()){
                    $acl[$r['acl-type']]=array(
                        'c'=>$r['acl-c'],
                        'act'=>$r['acl-act'],
                        'xid'=>$r['acl-xid'],
                        'val'=>$r['acl-val'],
                        );
                }
            }
        }
        return $acl;
    }

    public function get_acl_json($path=''){
        if($path=='') $path=PATH_APP.'/acl.json';
        $acl=array();
        if(is_readable($path)){
            $json = file_get_contents($path);
            $acl = json_decode($json,true);
        }
        return $acl;
    }

    public function check_acl($acl,$type,$c,$act,$id){
        $a=false;
        //\CORE::msg('debug','type:'.$type.';c:'.$c.';act:'.$act.';id:'.$id.';');
        if(isset($acl[$type][$c][$act][$id])) {
            if($acl[$type][$c][$act][$id]==1) $a=true;            
        } else {
            if(isset($acl[$type][$c]['*'][$id])) {
                if($acl[$type][$c]['*'][$id]==1) $a=true;
            }
            if(isset($acl[$type]['*']['*'][$id])) {
                if($acl[$type]['*']['*'][$id]==1) $a=true;
            }
            if(isset($acl[$type][$c][$act]['*'])) {
                if($acl[$type][$c][$act]['*']==1) $a=true;
            }
        }
        return $a;
    }

    public function acl($c='',$act=''){
        // I think it needs Refactoring in the future =)
        \CORE::msg('debug','Checking ACL');
        $access=false;

        $USER=\CORE\BC\USER::init();
        $uid=(int) $USER->get('uid');
        $gid=(int) $USER->get('gid');
        $uid=(string) $uid;
        $gid=(string) $gid;

        // dafault acl settings (0-gid type)
        $acl[0]['']['']['*']=1; // default main page
        $acl[0]['*']['*']['1']=1; // for administrators
        $acl[0]['user']['login']['0']=1; // guests can try to login
        if($gid>0){
            $acl[0]['user']['logout']['*']=1;
            $acl[0]['user']['profile']['*']=1;
            $acl[0]['user']['change_password']['*']=1;
            $acl[0]['user']['passwd']['*']=1;
            // bweb2
            $acl[0]['plist']['']['*']=1;
            $acl[1]['es']['events']['135']=1;
            $acl[1]['es']['events']['76']=1;
            $acl[1]['es']['events']['126']=1; // 6; 125;
        }
        // bweb2
        $acl[0]['page']['ciscocall']['*']=1; // page for all
        $acl[0]['es']['']['*']=1; // es for all
        $acl[0]['es']['list']['*']=1;
        $acl[0]['es']['hash']['*']=1;

        // here we can load $acl from db
            // $acl_db=$this->get_acl_db();
        // or load $acl from json file
            // $acl_json=$this->get_acl_json();
        //// but for the moment I will use php array ;)

        // group gid
        if($this->check_acl($acl,0,$c,$act,$gid)) $access=true;
        
        // user uid
        if($this->check_acl($acl,1,$c,$act,$uid)) $access=true;

        if(!$access) \CORE::msg('error','Access denied.');
        return $access;
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

public function table_exists($tbl) {
    $result=false;
    if($this->dbh!=null){
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = $this->dbh->query("SELECT 1 FROM `$tbl` LIMIT 1;");
        } catch (Exception $e) {
            $result=false;
        }
    }
    // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
    return $result !== false;
}

public function isUnique($tbl='',$fld='',$val='',$err_msg='This entry already exists in the database.'){
    $unique=true;
    if($this->dbh!=null){
        $sql = "SELECT * FROM `".$tbl."` WHERE `".$fld."`=:val;";
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array('val'=>$val));
        $this->query_count();
        if($sth->rowCount()>0){
            $unique=false;
            CORE::msg('error',$err_msg);
        }
    }
    return $unique;
}

public function get_records($tbl='',$primary_fld='',$order=''){
    $records=array();
    if($this->connect()){
        $sql="SELECT * FROM `".$tbl."`".$order.";";
        $sth=$this->dbh->prepare($sql);
        $sth->execute();
        $this->query_count();
        if($sth->rowCount()>0){
            while($r=$sth->fetch()){
                $records[$r[$primary_fld]]=$r;
            }
        }
    }
    return $records;
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

public function del_via_key($tbl='',$fld='',$key=''){
    $deleted=false;
        $key=trim($key);
        if($key!=''){
            $DB=\DB::init();
            if($DB->connect()){
                $sql = "DELETE FROM `".$tbl."` WHERE `".$fld."`=:key;";
                $sth = $DB->dbh->prepare($sql);
                $sth->execute(array('key'=>$key));
                $DB->query_count();
                $deleted=true;
            }
        }
    return $deleted;
}

}