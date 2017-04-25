<?php
// для ускорения автоладинга используем следующий способ
set_include_path(get_include_path().PATH_SEPARATOR.'./app/lib/'); // в данном случае приоритет для классов приложения
set_include_path(get_include_path().PATH_SEPARATOR.CORE.'lib/');
spl_autoload_register();
// ниже можно определить наиболее часто используемые классы (микрофреймворка)
class log
{
  private $log=array(
    'err'=>'',
    'debug'=>'',
  );

  public function set($cat,$msg)
  {
    if(isset($this->log[$cat]))
		{
		    $this->log[$cat].=$msg.PHP_EOL;
		} else {
		    $this->log[$cat]=$msg.PHP_EOL;
		}
  }

  public function get($cat)
  {
		$result='';
		if(isset($this->log[$cat])) $result=$this->log[$cat];
		return $result;
  }

}

abstract class controller
{
  protected $app=null;
  protected $model=null;
  protected $view=null;
  function __construct(){$this->app=app::init();}
  abstract public function action($act='');
}

abstract class model
{
  protected $app=null;
  function __construct(){$this->app=app::init();}
}

class module
{
    protected $name='';
    protected $action='';
    public $controller=null;

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
            $this->name=$c;
            $this->action=$act;
          } else {
        		$app->log->set('err','Module not found');
          }
        } else {
          $app->log->set('err','Access denied');
        }
      } else {
        $app->log->set('err','Wrong URL parameters');
      }
    }

    public function name(){return $this->name;}

}
// app data (for output)
class data
{
  private $blocks=array(
    'meta'=>'',
    'link'=>'',
    'title'=>'',
    'js'=>'',
    'main'=>''
  );
  public function set($content='',$block='main')
  {
    if(isset($this->blocks[$block])) {
      $this->blocks[$block].=$content;
    } else {
      $this->blocks[$block]=$content;
    }
  }
  public function get($block='main')
  {
    $content='';
    if(isset($this->blocks[$block])) $content=$this->blocks[$block];
    return $content;
  }
  public function get_blocks()
  {
    return $this->blocks;
  }

}
