<?php
class cfg
{

	private $config=array();

	function __construct($opt=array())
	{
		if(isset($opt['path']))
		{
			if(is_readable($opt['path']))
			{
				require $opt['path'];
				$this->config=$cfg;
			} else {
				echo 'config not found';
				exit;
			}
		} else {
			echo 'please specify config path';
			exit;
		}
	}

	public function set($key,$value)
	{
		$this->config[$key]=$value;
	}

	public function get($key)
	{
		if(isset($this->config[$key]))
		{
			return $this->config[$key];
		} else {
			return '';
		}
	}

	public function gets($prefix='',$clean=false)
	{
		$result=array();
		if($prefix!='')
		{
			$prefix.='_';
			$len=strlen($prefix);
			foreach ($this->config as $key => $value)
			{
				if(substr($key,0,$len)==$prefix)
				{
					$result[$key]=$value;
					if($clean) $this->config[$key]='';
				}
			}
		}
		return $result;
	}

}
