<?php
class APP
{
	private $conf=array();
	public $db = null;
	public $user = null;
	public $ui = null;

	private function conf($prefix)
	{
		$cnf=array();
		$len=strlen($prefix);
		foreach ($this->conf as $key => $val) {
			if(substr($key,0,$len)==$prefix) {
				$cnf[$key]=$val;
				$this->conf[$key]='';
			}
		}
		return $cnf;
	}

	public function run($config='conf.php'){
		if(is_readable($config)){
			require $config;
			$this->conf=$conf; // configuration
			// database
			$this->db = DB::init();
			$this->db->conf($this->conf('db_'));
			// user
			$this->user = new USER();
			// user interface
			$this->ui = UI::init();
			$this->ui->conf($this->conf('ui_'));
			// проверить сессию, определить пользователя
			// проверить url, проверить правила доступа и подключить нужный модуль рутером
			// еще есть мысль вести лог приложения для возможного дебага, а также лог действий пользователя
			// надо позаботиться о переводах интерфейса заблаговременно, даже если язык пока один
			$this->db->ok();
			// ...
			LOG::show();
			//$this->ui->p('<pre>'.print_r(get_included_files(),true).'</pre>');
			$this->ui->render();
		} else {
			echo 'configuration not found';
			exit;
		}		
	}

}