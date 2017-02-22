<?php
class LOG
{
    public static function init()
    {
        static $inst=null;
        if($inst===null) {$inst = new DB();}
        return $inst;
    }

    private function __construct(){}

    public static function msg()
    {

    }

    public static function show()
    {

    }

}