<?php
class app
{
	protected static $inst=null;

	private $cfg=array(); // configuration
	private $log=array(); // 'debug',err','info','user'
	public $db = null;
	public $user=null;
	public $session=null;
	public $ui=null;
	private $modules=array();
    
    protected function __construct(){}
    protected function __clone(){}

    public static function init()
    {
        if(!isset(static::$inst)) { static::$inst = new static; }
        return static::$inst;
    }

    private function set_config($config)
    {
    	if(is_readable($config)){
			require $config;
			$this->cfg=$cfg;
			return true;
		} else {
			echo 'config not found';
			return false;
		}	
    }

	private function get_config($prefix)
	{
		$cfg=array();
		$len=strlen($prefix);
		foreach ($this->cfg as $k => $v) {
			if(substr($k,0,$len)==$prefix) {
				$cfg[$k]=$v;
				$this->cfg[$k]='';
			}
		}
		return $cfg;
	}

	public function set_log($cat,$msg)
    {
        if(isset($this->log[$cat]))
        {
            $this->log[$cat].=$msg.PHP_EOL;
        } else {
            $this->log[$cat]=$msg.PHP_EOL;
        }
    }

    public function get_log($cat)
    {
        $log='';
        if(isset($this->log[$cat])) $log=$this->log[$cat];
        return $log;
    }

	public static function log($cat,$msg)
    {
    	app::init()->set_log($cat,$msg);
    }

	public static function regex($s,$regex='/^[a-zA-Z0-9_]+$/')
	{
        if(preg_match($regex,$s)){return true;} else {return false;}
    }

    public function db()
    {
    	if($this->db===null){$this->db = new db($this->get_config('db_'));}
   		return $this->db->ok();
    }

	public function set_module()
	{
		// ??
		$module = new module();
		$module_name=$module->get('name');
		if($module_name!='')
		{
			//if(!isset($this->modules[$module_name])) $this->modules[$module_name]=$module;
			$this->modules[$module_name]=$module; // overwrite existing
		}
	}

	public function get_module($name)
	{
		if(isset($this->modules[$name])){return $this->modules[$name];} else {return null;}
	}

	public function stop()
	{
		if($this->db!=null){$this->db->close();}
	}

	public function run($config)
	{
		if($this->set_config($config))
		{
			$this->user = new user();
			$this->set_module();
			$this->set_log('debug','а может впихнуть работу с сессией в класс user?');
			$this->set_log('debug','сократить модуль, сделать проще и для home применить file_get_contents (но динамики не будет)?');
			//...
			$this->stop();
			$this->ui = new ui($this->get_config('ui_'));
			$this->ui->render();
		}			
	}



}