<?php
class singleton
{
    protected static $inst=null;
    
    protected function __construct(){}
    protected function __clone(){}

    public static function init()
    {
        if(!isset(static::$inst)) { static::$inst = new static; }
        return static::$inst;
    }

}