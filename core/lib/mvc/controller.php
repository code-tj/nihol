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
    public $model=null;
    public $view=null;
    public $data='';

    public function load($c,$act)
    {
        $this->name=$c;
        $this->action=$act;
    }

    abstract function action();
}