<?php
class db
{
    private $connected = false;
    private $config=array();
    private $log=null;
    private $h = null; // db handle

    function __construct($config=array(),$log)
    {
        $this->config=$config;
        $this->log=$log;
    }

    private function config_ok()
    {
        if(count($this->config)>0) {
            // here we need put more to check configuration parameters
            return true;
        } else {
            return false;
        }
    }

    private function connect($log)
    {
        if($this->config_ok()){
            try {
                $dsn='mysql:host='.$this->config['db_server'].';dbname='.$this->config['db_name'];
                $opt=array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                );
                $this->h = new PDO($dsn,$this->config['db_user'],$this->config['db_pass'],$opt);
                $this->h->query('SET NAMES '.$this->config['db_charset']);
                $this->connected=true;
                //$log->msg('debug','[db]: connected to db');
                $this->config=array();
            } catch(PDOException $e) {
                $log->msg('err','something wrong with DB connection');
                $log->msg('debug','[db]: '.$e->getMessage());
            }
        } else {
            $log->msg('err','db config error');
        }
    }

    public function ok()
    {
    	if(!$this->connected) $this->connect($this->log);
    	return $this->connected;
    }

    private function close()
    {
    	if($this->connected && $this->h!=null)
    	{
	        $this->h=null;
	        $this->connected=false;
	    }
    }

    function __destruct()
    {
    	if($this->connected) $this->close();
    }

}