<?php
class app
{
	private $config=array();
	private $log=array(); // 'debug',err','info','user' /// ?
	public $db=null;
	public $user=null;
	//public $modules=array();

	private function load_config($config_path)
	{
		if(is_readable($config_path)){
			require $config_path;
			$this->config=$cfg; // cfg - config array
		} else {
			echo 'config not found';
			exit;
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
					$this->config[$key]=''; // clean
				}
			}
		}
		return $config;
	}

	private function set_log($cat,$msg)
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

	public function log($cat,$msg)
  {
		$this->set_log($cat,$msg);
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

	private function load_module($c='',$act='')
	{
		if($c=='' && $act=='')
    {
        if(isset($_GET['c']))
        {
          $c=$_GET['c'];
          if($c!='' && isset($_GET['act'])) { $act=$_GET['act']; }
        } else {
        	$c='home'; // default
        }
    }
    if($this::regex($c) && ($this::regex($act) || $act==''))
    {
        $path="\\mvc\\c\\".$c.'_c';
        if(class_exists($path))
        {
            if($this->user->ac($c,$act))
            {
                $controller = new $path();
								/*
								if($controller->get_name()!='')
								{
									$this->modules[$controller->get_name()]=$controller;
								}
								*/
                //$controller->load($c,$act);
                //$controller->action();
            } else {
                $this->log('err','Access denied');
            }
        } else {
      		$this->log('err','Controller not found');
        }
    }
	}

	public function stop()
	{
		if($this->db!=null){$this->db->close();} // close db connection if needed
	}

	public function run($config_path)
	{
		$this->load_config($config_path);
		$this->user = new user();
		$this->load_module();
		$this->stop();
		$this->ui = new ui($this->get_config('ui_'));
		$this->ui->render($this);
	}


// end class: app
}
