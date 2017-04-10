<?php
namespace mvc;

class controller
{
    protected $name='';
    protected $action='';
    public $model=null;
    public $view=null;

    public function getName()
    {
      return $this->name;
    }

}
