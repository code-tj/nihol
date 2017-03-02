<?php
namespace mvc\c;
class home
{
    private $app=null;
    private $act='';

    function __construct($act='home')
    {
        $this->app=\app::init();
        $this->act=$act;
        if($this->app->user->isGuest())
        {

        } else {
        	
        }

    }
}