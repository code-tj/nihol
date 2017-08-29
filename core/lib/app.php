<?php
class app
{
	private $modules = array();

	public function load_module($name,$opt=[])
	{
		if(!isset($this->modules[$name]))
		{
			$this->modules[$name] = new $name($opt);
		}
	}

	public function module($name)
	{
		if(isset($this->modules[$name]))
		{
			return $this->modules[$name];
		} else {
			return null;
		}
	}

	public function log($type,$msg)
	{
		$this->modules['log']->set($type,$msg);
	}

	public function data($content,$block='main')
	{
		$this->modules['appdata']->set($content,$block);
	}

	public function t($str) // it's for translation (for the future)
	{
		return $str;
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

	public function run($config_path)
	{
		$this->load_module('cfg',['path'=>$config_path]);
		$this->load_module('log');
		$this->load_module('db',$this->module('cfg')->gets('db',true));
		$this->load_module('appdata');
		$this->load_module('user');
		$this->load_module('ui',$this->module('cfg')->gets('ui'));
		$this->load_module('mvc');
		$this->module('ui')->render();
		$this->stop();
	}
} // end class: app
