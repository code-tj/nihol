<?php
class CORE
{
	private static $instance; // for singleton pattern
	private $messages=array();
    private $config=array();
    private $acl=array();
    private $acl_loaded=false;
    private $modules=array(
        'frontpage'=>1,
        'user'=>1,
    ); // registry of modules: 1 -> core module, 2 -> app module;
    
    public static function init($config_path='config.php')
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
            spl_autoload_register('CORE::autoloader');
            self::$instance->get_config($config_path);
            if(self::$instance->config('debug')==1){
                error_reporting(E_ALL);
                ini_set('display_errors',1);
            }
        }
        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public static function autoloader($class) {
        $path='';
        if(preg_match('/^[\\a-zA-Z0-9_]+$/',$class)) {
            $path=strtolower(str_replace('\\', '/', $class));
            if(strpos($path,'/') !== false) {
                $base=strtok($path, '/');
                if($base=='core') {$path=CL.'/'.substr($path,5).'.php';}
                if($base=='app') {$path='app/lib/'.substr($path,4).'.php';}
            } else {
                $path=CL.'/'.$path.'.php';
            }
            if(is_readable($path)) {
                require $path;
            } else {
                //CORE::msg('Can not load class '.$class.' from "'.$path.'"','debug');
                CORE::msg('Can not load class '.$class,'debug');
            }
        }
    }

    private function get_config($config_path)
    {
        if(is_readable($config_path))
        {
            require($config_path);
            $this->config=$conf;
            if($this->config('modules')=='db') $this->get_modules_from_db();
        } else {
            echo 'config not found'; exit;
        }
    }

    public function set_config($key,$value)
    {
        $this->config[$key]=$value;
    }

    public function config($key)
    {
        if(isset($this->config[$key]))
        {
            return $this->config[$key];
        } else {
            return '';
        }
    }

    public static function msg($msg='',$type='debug')
    {
        $CORE=CORE::init();
        if($type=='debug') { if($CORE->config('debug')==0) { return; } }
    	$CORE->messages[$type][]=$msg;
    }

    public static function get_messages()
    {
    	return CORE::init()->messages;
    }

    public function get_acl(){
        return $this->acl;
    }

    public function set_acl($acl){
        $this->acl=$acl;
    }

    public function acl_get_from_db($acl=array()){
        $DB=DB::init();
        if($DB->ok()){
            $sql="SELECT * FROM `n-acl`;";
            $sth=$DB->dbh->prepare($sql);
            $sth->execute();
            $DB->query_count();
            if($sth->rowCount()>0){
                CORE::msg('Loading ACL from DB','debug');
                while($r=$sth->fetch()){
                    $acl[$r['c']][$r['act']][$r['gu']][$r['xid']]=(int) $r['val'];
                }
            }
        }
        return $acl;
    }

    public function acl_get_from_json($acl=array(),$path=''){
        if($path=='') $path='./app/acl.json';
        if(is_readable($path)){
            CORE::msg('Loading ACL from json','debug');
            $json = file_get_contents($path);
            $acl = json_decode($json,true);
        }
        return $acl;
    }

    public function acl_get_from_file($acl=array(),$path=''){
        if($path=='') $path='./app/acl.php';
        if(is_readable($path)){
            CORE::msg('Loading ACL from the file','debug');
            include($path);
        }
        return $acl;
    }

    public function acl_load($load_from='file'){
        if(!$this->acl_loaded){
            $acl=array();
            // default base values
            $acl['frontpage']['']['g']['*']=1; // frontpage for all
            $acl['*']['*']['g']['1']=1; // for admin
            $acl['user']['login']['g']['0']=1; // guests can try to login
            if(USER::init()->get_gid()>0){
                // for authorized users
                $acl['user']['logout']['g']['*']=1;
                //$acl['user']['profile']['g']['*']=1;
                //$acl['user']['passwd']['g']['*']=1;
            }
            // loading from: file | json | db
            if($load_from=='file') {$acl=$this->acl_get_from_file($acl);} else {
               if($load_from=='db') $acl=$this->acl_get_from_db($acl);
                if($load_from=='json') $acl=$this->acl_get_from_json($acl); 
            }            
            //UI::init()->p('<pre>'.print_r($acl,true).'</pre>');
            $this->set_acl($acl);
            $this->acl_loaded=true;
        }
    }

    public function acl_check($c,$act,$gu,$xid){
        //CORE::msg('checking ACL','debug');
        $a=false;
        // acl[c,'','*'][act,'','*']['g'|'u']['xid','*']=1-allow,0-deny
        $acl=CORE::init()->get_acl();
        if(isset($acl[$c][$act][$gu][$xid])) {
            if($acl[$c][$act][$gu][$xid]==1) return true;            
        } else {
            if(isset($acl[$c]['*'][$gu][$xid])) {
                if($acl[$c]['*'][$gu][$xid]==1) return true;
            }
            if(isset($acl['*']['*'][$gu][$xid])) {
                if($acl['*']['*'][$gu][$xid]==1) return true;
            }
            if(isset($acl[$c][$act][$gu]['*'])) {
                if($acl[$c][$act][$gu]['*']==1) return true;
            }
        }        
        return $a;
    }

    public static function acl($c,$act){
        $a=false;
        //CORE::msg('CORE::acl()','debug');
        $CORE=CORE::init();
        if($CORE::module_check($c)>0){
            $CORE->acl_load('file');
            $USER=USER::init();
            $gids=$USER->get_gids();
            $uid=$USER->get_uid();
            $gids_count=count($gids);
            $gu='g';
            for($i=0;$i<$gids_count;$i++){
                $a=$CORE->acl_check($c,$act,$gu,(string) $gids[$i]);
                if($a) {break;}
            }
            if(!$a){
                $gu='u';
                $a=$CORE->acl_check($c,$act,$gu,(string) $uid);
            }
            //if($a) CORE::msg('checking ACL('.$c.','.$act.','.$gu.','.$gids[$i].') - ok','debug');
        } else {
            CORE::msg('Unregistered module.','error');
        }
        return $a;
    }

    public static function module_check($module_name){
        $CORE=CORE::init();
        if(isset($CORE->modules[$module_name])) {return $CORE->modules[$module_name];} else {return 0;}
    }

    public function get_modules_from_db(){
        $DB=DB::init();
        if($DB->ok()){
            CORE::msg('getting list of modules from DB','debug');
            $sql="SELECT * FROM `n-modules`;";
            $sth=$DB->dbh->prepare($sql);
            $sth->execute();
            $DB->query_count();
            if($sth->rowCount()>0){
                while($r=$sth->fetch()){
                    $this->modules[$r['mod-name']]=(int) $r['mod-type'];
                }
            }
        }
    }

    public function reg_app_modules($modules){
        $count_items=count($modules);
        for ($i=0; $i < $count_items; $i++) { 
            $this->modules[$modules[$i]]=2;
        }
    }

    public static function check_regex($str,$regex='/^[a-zA-Z0-9_]+$/'){
        if(preg_match($regex,$str)){ return true; } else {return false;}
    }

}