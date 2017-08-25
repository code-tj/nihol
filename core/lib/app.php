<?php
class app
{
	protected static $inst=null;
	private $config = null;
	public $log = null;
	public $data = null;
	public $db = null;
	public $user = null;
	public $ui = null;
	public $module=null;

	public static function init()
	{
			if(!isset(static::$inst))
			{
				static::$inst = new static;
			}
			return static::$inst;
	}

	protected function __construct(){}
	protected function __clone(){}

	public function log($type,$msg)
	{
		$this->log->set($type,$msg);
	}

	public function data($content='',$block='main')
	{
			$this->data->set($content,$block);
	}

	public static function regex($s,$regex='/^[a-zA-Z0-9_]+$/')
	{
		if(preg_match($regex,$s)){return true;} else {return false;}
	}

	public function db()
	{
		$connected=false;
		if(is_null($this->db)){
			$this->db = new db($this->config->get_array('db'));
			$connected=$this->db->connected();
		} else {
			$connected=$this->db->connected();
		}
		return $connected;
	}

	public static function t($text)
	{
		return $text;
	}

	public function json($output=array())
	{
		header("Content-Type: application/json; charset=UTF-8");
		echo json_encode($output);
		exit;
	}

	public function stop()
	{
		if(!is_null($this->db)){$this->db->close();}
	}

	public function run($config_path)
	{
		$this->config = new config();
		$this->config->load($config_path);
		$this->log = new log();
		$this->data = new appdata();
		$this->user = new user();
		$this->ui = new ui();
		$this->module = new module();
		$this->ui->render($this->config->get('ui_tpl'));
		$this->stop();
	}
} // end class: app
