<?php
class app
{
	private $modules = array();

	public function load($module_name,$options=[])
	{
		if(!isset($this->modules[$module_name]))
		{
			$this->modules[$module_name] = new $module_name($options);
		}
	}

	public function module($module_name)
	{
		if(isset($this->modules[$module_name]))
		{
			return $this->modules[$module_name];
		} else {
			return null;
		}
	}

	public function log($log_type,$msg)
	{
		$this->modules['log']->set($log_type,$msg);
	}

	public function data($content,$block_name='main')
	{
		$this->modules['appdata']->set($content,$block_name);
	}

	public function t($s) // translation
	{
		return $s;
	}

	private function stop()
	{
		//print_r(get_included_files());
		///if(!is_null($this->db)){$this->db->close();}
	}

/*

	public function json($output=array())
	{
		header("Content-Type: application/json; charset=UTF-8");
		echo json_encode($output);
		exit;
	}

*/

	public function run($config_file='config.php')
	{
		$this->load('config',['config_file'=>$config_file]);
		$this->load('log');
		$this->load('db',$this->module('config')->options('db',true));
		$this->load('appdata');
		$this->load('user');
		$this->load('ui',$this->module('config')->options('ui'));
		$this->load('mvc');
		$this->module('ui')->render();
		$this->stop();
	}
} // end class: app
