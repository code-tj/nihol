<?php
namespace mvc;

abstract class model
{
    
}

abstract class view
{
    
}

abstract class controller
{
    protected $name='';
    protected $action='';
    protected $app=null;
    public $model=null;
    public $view=null;
    public $data='';

    public function initialize($c,$act)
    {
        $this->app=\app::init();
        $this->name=$c;
        $this->action=$act;
    }

    abstract function action();
}