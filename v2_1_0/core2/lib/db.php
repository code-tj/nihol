<?php
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

private function __construct() {}
private function __clone() {}
private function __wakeup() {}

public function ok(){
    if(!$this->connected){
        $CORE=CORE::init();
        try {
            $dsn='mysql:host='.$CORE->config('db_server').';dbname='.$CORE->config('db_name');
            // .';charset='.$CORE->config('db_charset')

            // PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            $opt=array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            );
            $this->dbh = new PDO($dsn,$CORE->config('db_user'),$CORE->config('db_pass'),$opt);
            ///$this->dbh->query('SET character_set_connection = '.$CORE->config('db_charset').';');
            ///$this->dbh->query('SET character_set_client = '.$CORE->config('db_charset').';');
            ///$this->dbh->query('SET character_set_results = '.$CORE->config('db_charset').';');
            $this->dbh->query('SET NAMES '.$CORE->config('db_charset'));
            $this->connected=true;
            CORE::msg('Connecting to DB','debug');
        } catch(PDOException $e) {
            CORE::msg('Some problems with DB connection (check configuration)','error');
            CORE::msg($e->getMessage(),'debug');
        }
        // aclear DB parameters in config
        $CORE->set_config('db_server','');
        $CORE->set_config('db_name','');
        $CORE->set_config('db_user','');
        $CORE->set_config('db_pass','');
    }
    return $this->connected;
}

public function connected(){ return $this->connected; }

public function query_count($q=0){ if($q==0){$this->queries++;} else {$this->queries+=(int) $q;} }

public function close(){
    if($this->dbh!=null && $this->connected){
        $this->dbh=null;
        $this->connected=false;
        CORE::msg('Closing DB connection (query_count: '.$this->queries.')','debug');
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
            CORE::msg($err_msg,'error');
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