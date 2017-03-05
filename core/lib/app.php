<?php
class app
{
	protected static $inst=null;

	private $cfg=array(); // configuration
	private $log=array(); // 'debug',err','info','user'
	public $db = null;
	public $user=null;	
	public $ui=null;	
	//public $modules=array();
    
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

	public function set_module($c='',$act='')
	{
		if($c=='' && $act=='')
        {
            if(isset($_GET['c']))
            {
                $c=$_GET['c'];
                if($c!='' && isset($_GET['act'])) { $act=$_GET['act']; }
            } else {
            	$c='p'; // for some static pages
            }
        }
        if(\app::regex($c) && (\app::regex($act)) || $act=='')
        {
            $cpath="\\mvc\\c\\".$c;
            if(class_exists($cpath))
            {
                $controller = new $cpath();
                $controller->initialize($c,$act);
                $controller->action();
                //$this->modules[$c]=$controller;
            } else {
            	app::log('err','Module not found');
            }
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
			$this->ui = new ui($this->get_config('ui_'));
			$this->user = new user();
			$this->set_module();
			//...
			$this->stop();			
			$this->ui->render();
		}			
	}



}