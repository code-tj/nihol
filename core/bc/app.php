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
		    		if(\CORE::isValid($act,'/^[a-zA-Z0-9_]+$/')){
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
    		UI::init()->static_page('home');
    	} else {
			if(isset($modules[$REQUEST->get('c')])){
	    		// \CORE::msg('debug','Controller: '.$REQUEST->get('c'));
		    		$model=null; $view=null; $controller=null;
		    		$p2=strtoupper($REQUEST->get('c'));
		    		$path=array('m'=>'','v'=>'','c'=>'');
				if($modules[$REQUEST->get('c')]==0){
					$p1='CORE\\MVC\\';
					$path['m']=$p1.'M\\'.$p2.'_M';
					$path['v']=$p1.'V\\'.$p2.'_V';
					$path['c']=$p1.'C\\'.$p2.'_C';
				} else {
					$p1='APP\\MVC\\';
					$path['m']=$p1.'M\\'.$p2.'_M';
					$path['v']=$p1.'V\\'.$p2.'_V';
					$path['c']=$p1.'C\\'.$p2.'_C';
				}
                    if(class_exists($path['c'])){
                        if(\SEC::init()->acl($REQUEST->get('c'),$REQUEST->get('act'))){ // access control ($USER)
                            if(class_exists($path['m'])){ $model = new $path['m'](); }
                            if(class_exists($path['v'])){ $view = new $path['v'](); }
                            $controller = new $path['c']($REQUEST,$model,$view);
                        }
                    } else {
                        \CORE::msg('error','Module not loaded');
                    }
	    	} else {
	   			\CORE::msg('error','Unregistered module');
	   			UI::init()->static_page('home');
	    	}
    	}
    }

}

class APP {

    private static $inst;

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
            \CORE::msg('debug','app initialization');
            if(is_readable(DIR_APP.'/appmain.php')){
                include(DIR_APP.'/appmain.php');
            } else {
                \CORE::init()->msg('debug','application main script not found');
            }
            $modules=\CORE::init()->get_modules();
            $REQUEST = new REQUEST();
            ROUTER::init($REQUEST,$modules); // check modules
        }
        return self::$inst;
    }

    private function __construct() {

    }

}