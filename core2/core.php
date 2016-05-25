<?php

class CORE
{
	private static $instance; // singleton pattern
	private $messages=array();
    
    public static function init()
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
            spl_autoload_register('CORE::autoloader');
        }
        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

	public static function autoloader($class)
	{
        $path=''; echo $path;
        if(preg_match('/^[\\a-zA-Z0-9_]+$/',$class))
        {
            $path=strtolower(str_replace('\\', '/', $class));

            if(strpos($path,'/') !== false)
            {
            	$base=strtok($path, '/'); // get substr before "/"
            	if($base=='core') $path=DIR_CORE.'/lib/'.substr($path,5).'.php';
            	if($base=='app') $path='app/lib/'.substr($path,4).'.php';
            } else {
            	$path=DIR_CORE.'/lib/'.$path.'.php';
            }

        }
        if($path!='' && is_readable($path))
        {
            require $path;
        } else {
            CORE::msg('Can not load class '.htmlspecialchars($class).' '.$path,'debug');
        }
    }

    public static function msg($message='',$type='')
    {
    	if($type=='') $type='msg';
    	if(isset(CORE::init()->messages[$type]))
    	{
    		CORE::init()->messages[$type].=htmlspecialchars($message)."<br>\n";
    	} else {
    		CORE::init()->messages[$type]=htmlspecialchars($message)."<br>\n";
    	}
    }

    public static function get_messages($type='')
    {
    	$result='';
    	$messages=CORE::init()->messages;
    	if($type!='')
    	{
    		if(isset($messages[$type])) { $result=$messages[$type]; }
    	} else {
    		foreach ($messages as $key => $msg) {
    			$result.=$msg;
    		}
    	}
    	return $result;
    }

    public static function APP()
    {
    	CORE::init();
    	return APP::init();
    }

}