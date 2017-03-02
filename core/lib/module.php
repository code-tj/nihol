<?php
class module
{
    private $opt=array('c'=>'','act'=>'');
    public $c=null;

    public static function valid($str,$regex='/^[a-zA-Z0-9_]+$/'){
        if(preg_match($regex,$str)){ return true; } else {return false;}
    }

    function __construct($opt=array())
    {
        $c=''; $act='';
        // GET parameters
        if(isset($_GET['c'])){
            if($this->valid($_GET['c'])) { $c=$_GET['c']; }
            if($c!='' && isset($_GET['act']))
            {
                if($this->valid($_GET['act'])) { $act=$_GET['act']; }
            }
        }
        // constructor parameters
        if(count($opt)>0)
        {
            if(isset($opt['c'])){
                if($this->valid($opt['c'])) { $c=$opt['c']; }
                if($c!='' && isset($opt['act']))
                {
                    if($this->valid($opt['act'])) { $act=$opt['act']; }
                }
            }
        }
        // check existing modules
        // ...
        // check acl
        // ...
        if($c=='' && $act=='') { $c='home'; }
        // load files and classes
        $c_path="\\mvc\\c\\".$c;
        if(class_exists($c_path))
        {
            $this->c = new $c_path($act);
        }
    }

    public function get($k)
    {
        if($k=='name') return $this->opt['c'];
    }
}