<?php
class app
{
	protected static $inst=null;
	private $config=array();
	public $db=null;
	public $log=null;
	public $data=null;
	public $user=null;
	public $modules=array();

	public static function init()
	{
			if(!isset(static::$inst)) { static::$inst = new static; }
			return static::$inst;
	}

	protected function __construct(){}
	protected function __clone(){}

	private function load_config($path)
	{
		if(is_readable($path))
		{
			require $path;
			$this->config=$cfg;
		} else {
			echo 'config not found'; exit;
		}
	}

	public function cfg($prefix='')
	{
		$config=array();
		if($prefix!='')
		{
			$prefix.='_';
			$len=strlen($prefix);
			foreach ($this->config as $key => $val)
			{
				if(substr($key,0,$len)==$prefix)
				{
					$config[$key]=$val;
					$this->config[$key]=''; // clean!
				}
			}
		}
		return $config;
	}

	public static function regex($s,$regex='/^[a-zA-Z0-9_]+$/')
	{
		if(preg_match($regex,$s)){return true;} else {return false;}
	}

	private function load_module()
	{
		$module = new module();
		if($module->name()!=''){$this->modules[$module->name()]=$module;}
	}

	// ...

	public function stop()
	{
		if($this->db!=null){$this->db->close();}
	}

	public function run($config_path)
	{
		// load config
		$this->load_config($config_path);
		// load logger for app messages
		$this->log = new log();
		// app data (store output data)
		$this->data = new data();
		// load user object to define current user
		$this->user = new user();
		// loading app modules
		$this->load_module();
		// ui initialization
		$this->ui = new ui($this->cfg('ui'));
		// rendering results
		$this->ui->render();
		$this->stop();
	}
} // end class: app
