<?php
class MODULE {

    private $c='';
    private $act='';
    private $model=null;
    private $view=null;
    private $controller=null;

    function __construct($c='',$act=''){

        //CORE::msg('new module','debug');

        if($c=='' && isset($_GET['c'])){
            $c=trim($_GET['c']);
            if($act=='' && isset($_GET['act'])){
                $act=trim($_GET['act']);
            }
        }

        if($c!=''){
            if(MODULE::check_regex($c,'/^[a-z]+$/')){
                $this->c=$c; // controller
                if($act!=''){
                    if(MODULE::check_regex($act,'/^[a-zA-Z0-9_]+$/')){
                        $this->act=$act; // action
                    } else {CORE::msg('Incorrect action name','error');}
                }
            } else {CORE::msg('Incorrect controller name','error');}
        } else {
            $c='frontpage';
            $this->c=$c;
        }

        //CORE::msg('c='.$this->c.'; act='.$this->act.';','debug');
        $this->router();
    }

    public static function check_regex($str,$regex='/^[a-zA-Z0-9_]+$/'){
        if(preg_match($regex,$str)){ return true; } else {return false;}
    }

    public function c(){return $this->c;}

    public function act(){return $this->act;}

    private function clear(){
        $this->c='';
        $this->act='';
    }

    private function router(){
        if(CORE::acl($this->c,$this->act)){
            // load mvc
            $class_name=strtoupper($this->c);
            if(CORE::module_check($this->c)==1){
                $prefix='CORE\\MVC\\';
            } else {
                $prefix='APP\\MVC\\';
            }
            if($this->c!='frontpage') { // tmp
                $p=$prefix.'M\\'.$class_name.'_M'; // model
                if(class_exists($p)){ $this->model = new $p; }
                $p=$prefix.'V\\'.$class_name.'_V'; // view
                if(class_exists($p)){ $this->view = new $p; }
            }
            $p=$prefix.'C\\'.$class_name.'_C'; // controller
            if(class_exists($p)){ $this->controller = new $p($this); } else {
                CORE::msg("Can not find \"".$this->c."\"",'error');
            }
        } else {
            CORE::msg('Access is denied.','error');
        }
    }

}