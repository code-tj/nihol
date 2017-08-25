<?php
// for autoloading (app lib priority)
set_include_path(get_include_path().PATH_SEPARATOR.'./app/lib/');
set_include_path(get_include_path().PATH_SEPARATOR.CORE.'lib/');
spl_autoload_register();

class config{

  private $config=array();

  public function load($file_path)
  {
    if(is_readable($file_path))
		{
			require $file_path;
			$this->config=$cfg;
		} else {
			echo 'config not found';
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

  public function get_array($prefix='',$clean=true)
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

abstract class model
{

}

abstract class view
{

}

abstract class controller
{
  protected $model=null;
  protected $view=null;
}

class module
{
    public $controller=null;
    private $c='';
    private $act='';
    function __construct($c='',$act='')
    {
      // GET parameters
      if($c=='' && isset($_GET['c']))
      {
        $c=$_GET['c'];
        if($c!='' && isset($_GET['act'])){$act=$_GET['act'];}
      }
      // default value
      if($c==''){$c='page';}
      // check parameters
      $app=app::init();
      if($app::regex($c) && ($app::regex($act) || $act==''))
      {
        // checking user access
        if($app->user->ac($c,$act))
    		{
          // try to load controller
          $path="\\mvc\\c\\".$c.'_c';
          if(class_exists($path))
          {
            $this->controller = new $path();
            $this->controller->action($act);
            $this->c=$c;
            $this->act=$act;
          } else {
        		$app->log('err','Module not found');
          }

        } else {
          $app->log('err','Access denied');
        }
      } else {
        $app->log('err','Wrong URL parameters');
      }
    }
    public function name(){return $this->c;}
    public function action(){return $this->act;}
}
