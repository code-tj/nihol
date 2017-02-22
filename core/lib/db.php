<?php
class DB
{
	private $connected = false;
	private $conf=array();
	private $h = null; // db handle

	public static function init()
    {
        static $inst=null;
        if($inst===null) {$inst = new DB();}
        return $inst;
    }

    private function __construct(){}

	public function conf($conf)
	{
		$this->conf=$conf;
	}

    public function connect()
    {
    	try {
            $dsn='mysql:host='.$this->conf['db_server'].';dbname='.$this->conf['db_name'];
            $opt=array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            );
            $this->h = new PDO($dsn,$this->conf['db_user'],$this->conf['db_pass'],$opt);
            $this->h->query('SET NAMES '.$this->conf['db_charset']);
            $this->connected=true;
            LOG::msg('debug','[db]: connected to db');
            $this->conf=array();
        } catch(PDOException $e) {
            LOG::msg('err','something wrong with DB connection');
            LOG::msg('debug','[db]: '.$e->getMessage());
        }
    }

    public function ok()
    {
    	if(!$this->connected) {$this->connect();}
    	return $this->connected;
    }

    public function close()
    {
    	if($this->connected && $this->h!=null)
    	{
	        $this->h=null;
	        $this->connected=false;
            LOG::msg('debug','[db]: connection closed');
	    }
    }

    function __destruct()
    {
    	// close connection
    	if($this->connected) $this->close();
    }

}