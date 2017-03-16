<?php
class app
{
	protected static $inst=null; // singlton

	private $config=array();
	private $log=array(); // 'debug',err','info','user'
	public $db=null;
	public $user=null;	
	public $ui=null;	
	public $controllers=array();
    
    protected function __construct(){}
    protected function __clone(){}

    public static function init()
    {   // реализация патерна синглтон (чтобы вызывать единственный объект из разных частей кода)
        if(!isset(static::$inst)) { static::$inst = new static; }
        return static::$inst;
    }

    private function set_config($config)
    {
    	if(is_readable($config)){
			require $config;
			$this->config=$cfg; // $cfg - массив конфигураций
			return true;
		} else {
			echo 'config not found';
			return false;
		}	
    }

	private function get_config($prefix='')
	{
		$config=array();
        if($prefix!='')
        {
            $len=strlen($prefix);
            foreach ($this->config as $key => $val)
            {
                if(substr($key,0,$len)==$prefix)
                {
                    $config[$key]=$val;
                    $this->config[$key]=''; // очищаем после получения
                }
            }
        } else {

        }		
		return $config;
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

	public function load_controller($c='',$act='')
	{
		if($c=='' && $act=='')
        {
            if(isset($_GET['c']))
            {
                $c=$_GET['c'];
                if($c!='' && isset($_GET['act'])) { $act=$_GET['act']; }
            } else {
            	$c='p'; // default controller name
            }
        }
        if(\app::regex($c) && (\app::regex($act) || $act==''))
        {
            $c_path="\\mvc\\c\\".$c;
            if(class_exists($c_path))
            {
                if($this->user->ac($c,$act))
                {
                    $controller = new $c_path();
                    $controller->load($c,$act);
                    $controller->action();
                    $this->controllers[$c]=$controller;
                } else {
                    app::log('err','Access denied');
                }                
            } else {
            	app::log('err','Controller not found');
            }
        }
	}

	public function get_controller($name)
	{
		if(isset($this->controllers[$name])){return $this->controllers[$name];} else {return null;}
	}

	public function stop()
	{
		if($this->db!=null){$this->db->close();} // close db connection if needed
	}

	public function run($config)
	{
		if($this->set_config($config))
		{			
			$this->ui = new ui($this->get_config('ui_'));
			$this->user = new user();
			$this->load_controller();
			$this->stop();
			$this->ui->render();
		}			
	}



}