<?php
class UI
{
    private $conf=array();
    private $p=array(
        'meta'=>'',
        'link'=>'',
        'title'=>'',
        'js'=>'',
        'main'=>''
        );

    public static function init()
    {
        static $inst=null;
        if($inst===null) {$inst = new UI();}
        return $inst;
    }

    private function __construct(){}	

    public function conf($conf)
    {
        $this->conf=$conf;
    }

    public function p($content,$block='main'){
        if(isset($this->p[$block])){
            $this->p[$block].=$content;
        } else {
            // $this->p[$block]=$content;
        }
    }

    public function render()
    {
        $output='';
        if(is_readable($this->conf['ui_tpl']))
        {
            $output = file_get_contents($this->conf['ui_tpl']);
            foreach($this->p as $key => $content)
            {
                $tag="<!--@$key-->";
                $output=str_replace($tag,$content,$output);
            }
        } else {
            echo 'template not found';
        }
        echo $output;
    }

}