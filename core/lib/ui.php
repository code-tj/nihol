<?php
class ui
{
    private $config=array();
    private $log=null;
    private $p=array(
        'meta'=>'',
        'link'=>'',
        'title'=>'',
        'js'=>'',
        'main'=>''
        );

    function __construct($config=array(),$log)
    {
        $this->config=$config;
        $this->log=$log;
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
        if(isset($this->config['ui_tpl']) && is_readable($this->config['ui_tpl']))
        {
            $output = file_get_contents($this->config['ui_tpl']);
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