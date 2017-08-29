<?php
// for autoloading (app lib priority)
set_include_path(get_include_path().PATH_SEPARATOR.'./app/lib/');
set_include_path(get_include_path().PATH_SEPARATOR.CORE.'lib/');
spl_autoload_register();

class my
{
  protected static $app=null;

  protected function __construct(){}
  protected function __clone(){}

  public static function app()
  {
      if(!isset(static::$app)) { static::$app = new app(); }
      return static::$app;
  }

  public static function log($type,$msg)
  {
      if(isset(static::$app)) {
        static::$app->log($type,$msg);
      }
  }

  public static function data($content,$block='main')
  {
      if(isset(static::$app)) {
        static::$app->data($content,$block);
      }
  }

  public static function module($name)
  {
      $module=null;
      if(isset(static::$app))
      {
        $module=static::$app->module($name);
      }
      return $module;
  }

  public static function user()
  {
      return my::module('user');
  }

  public static function regex($s,$regex='/^[a-zA-Z0-9_]+$/')
	{
		if(preg_match($regex,$s))
    {
      return true;
    } else {
      return false;
    }
	}

}
