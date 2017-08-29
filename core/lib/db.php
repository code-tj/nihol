<?php
class db
{
    private $connected = false;
    private $config=array();
    public $h = null; // db handle
    private $queries_counter=0;

    function __construct($config)
    {
        $this->config=$config;
    }

    private function config_ok()
    {
        $check=false;
        if(count($this->config)>0) {
            if(isset($this->config['db_server']) &&
                isset($this->config['db_name']) &&
                isset($this->config['db_user']) &&
                isset($this->config['db_pass']) &&
                isset($this->config['db_charset']))
            {
                $check=true;
            }
        }
        return $check;
    }

    private function connect()
    {
        if($this->config_ok()){
            try {
                $dsn='mysql:host='.$this->config['db_server'].';dbname='.$this->config['db_name'];
                $opt=array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                );
                $this->h = new PDO($dsn,$this->config['db_user'],$this->config['db_pass'],$opt);
                $this->h->query('SET NAMES '.$this->config['db_charset']);
                $this->connected=true;
                //$app->log('debug','[db]: connected');
                $this->config=array();
            } catch(PDOException $e) {
                my::log('err','Something wrong with DB connection');
                my::log('debug','[db]: '.$e->getMessage());
            }
        } else {
            my::log('err','db config error');
        }
    }

    public function connected()
    {
    	if(!$this->connected) $this->connect();
    	return $this->connected;
    }

    public function close()
    {
    	if($this->connected && $this->h!=null)
    	{
	        $this->h=null;
	        $this->connected=false;
          //my::log('debug','[db]: closed');
	    }
    }

    public function get($sql,$opt=array())
    {
        $records=array();
        $sth=$this->h->prepare($sql);
        if(count($opt)>0)
        {
          $sth->execute($opt);
        } else {
          $sth->execute();
        }
        $this->qcount();
        if($sth->rowCount()>0){
            $records=$sth->fetchAll();
        }
        return $records;
    }

    public function qcount()
    {
      $this->queries_counter++;
    }

}
