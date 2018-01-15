<?php
class mvc
{
	//public $modules=array();
	private $c='';
	private $act='';

	function __construct()
	{
		$this->load();
	}

	public function load($c='',$act='')
	{
		if($c=='' && isset($_GET['c'])) // controller
		{
			$c=$_GET['c'];
			if($c!='' && isset($_GET['act'])){$act=$_GET['act'];} // action
		}
		if($c==''){$c='page';} // default controller name
		if(my::regex($c) && (my::regex($act) || $act==''))
		{
			// checking user access
			if(my::user()->ac($c,$act))
			{
				$path="\\mvc\\c\\".$c.'_c';
				if(class_exists($path))
				{
					$this->controller = new $path();
					$this->controller->action($act);
					$this->c=$c;
					$this->act=$act;
				} else {
					my::log('err','Module not found');
				}

			} else {
				my::log('err','Access denied');
			}
		} else {
			my::log('err','Wrong query parameters');
		}
	}


}

class controller
{

	protected function init()
	{

	}

	function __construct()
	{
		$this->init();
	}

}

class model
{

	protected $db=null;

	protected function init()
	{

	}

	function __construct()
	{
		$this->init();
	}

}

class view
{

	protected function init()
	{

	}

	function __construct()
	{
		$this->init();
	}

}
