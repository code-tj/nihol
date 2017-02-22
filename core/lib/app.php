<?php
class app
{
    private $conf=array(); // confoguration
	public $log = null; // log for debug, errors, info messages
	public $db = null; // database (by default based on PDO)
	public $user = null; // defines current user parameters
	public $ui = null; // user interface (works with html templates, js, etc)

    private function load_config($config)
    {
    	if(is_readable($config)){
			require $config;
			$this->conf=$conf;
			return true;
		} else {
			echo 'configuration not found';
			return false;
		}	
    }

	private function get_config($prefix) // getting specific configuration via prefix
	{
		$cfg=array();
		$len=strlen($prefix);
		foreach ($this->conf as $key => $val) {
			if(substr($key,0,$len)==$prefix) {
				$cfg[$key]=$val;
				$this->conf[$key]='';
			}
		}
		return $cfg;
	}

	public function run($config)
	{
		if($this->load_config($config))
		{
			$this->log = new log();
			$this->db = new db($this->get_config('db_'),$this->log);
			$this->ui = new ui($this->get_config('ui_'),$this->log);
			// session
			$this->user = new user($this->log);
			// проверить сессию, определить пользователя
			// проверить url, проверить правила доступа и подключить нужный модуль рутером
			// еще есть мысль вести лог приложения для возможного дебага, а также лог действий пользователя (опционально)
			// надо позаботиться о переводах интерфейса заблаговременно, даже если язык один
			// ...
			///$this->db->ok();
			///$this->ui->p($this->log->get('debug'));
			//$this->ui->p('<pre>'.print_r(get_included_files(),true).'</pre>');
			$this->ui->render();
		}			
	}



}