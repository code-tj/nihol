<?php
namespace CORE\BC;

class REQUEST {

	private $c='';
	private $act='';

    public function __construct() {
    	if(isset($_GET['c'])){
    		$c=trim($_GET['c']);
    		if(\CORE::isValid($c,'/^[a-z]+$/')){
    			$this->c=$c; // controller
    			if(isset($_GET['act'])){
		    		$act=trim($_GET['act']);
		    		if(\CORE::isValid($act,'/^[a-z]+$/')){
		    			$this->act=$act; // action	    			
		    		} else {\CORE::msg('error','Unregistered action');}
		    	}
    		} else {\CORE::msg('error','Incorrect module name');}
    	}
    }

    public function get($param=''){
    	if(isset($this->$param)){return $this->$param;} else {return '';}
    }

}


class ROUTER {

    private static $inst;

    public static function init($REQUEST,$modules) {
        if(empty(self::$inst)) {
            self::$inst = new self($REQUEST,$modules);
        }
        return self::$inst;
    }

    private function __construct($REQUEST,$modules) {
    	if($REQUEST->get('c')==''){
    		\CORE::msg('debug','Here try to include frontpage');
    	} else {
			if(isset($modules[$REQUEST->get('c')])){
	    		\CORE::msg('debug','Controller: '.$REQUEST->get('c'));
	    		
	    	} else {
	   			\CORE::msg('error','Unregistered module');
	   			\CORE::msg('debug','Here try to include frontpage');
	    	}
    	}
    }

}

class APP {

    private static $inst;
    private $modules=array('test'=>1);

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    private function __construct() {
    	$modules=array_merge(\CORE::init()->get_modules(), $this->modules);
    	$REQUEST = new REQUEST();
        ROUTER::init($REQUEST,$modules); // check modules
    }

}