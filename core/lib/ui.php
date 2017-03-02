<?php
class ui
{
    private $config=array();
    private $blocks=array(
        'meta'=>'',
        'link'=>'',
        'title'=>'',
        'js'=>'',
        'main'=>''
        );

    function __construct($config)
    {
        $this->config=$config;
    }

    public function menu()
    {
        if(is_readable('./ui/menu/menu.php'))
        {
            require './ui/menu/menu.php';
            $this->load_menu($menu);
        }
    }

    public function load_menu($menu)
    {
        foreach ($menu as $menu_name => $items) {
            $c=count($items);
            for($i=0;$i<$c;$i++){
                $this->set('<li><a href="'.$items[$i]['url'].'">'.$items[$i]['label']."</a></li>\n",$menu_name);
            }
        }
    }

    public function set($content,$alias='main'){
        if(isset($this->blocks[$alias])){
            $this->blocks[$alias].=$content;
        } else {
            $this->blocks[$alias]=$content;
        }
    }

    public function render()
    {
        $output='';
        $app=app::init();
        if(isset($this->config['ui_tpl']) && is_readable($this->config['ui_tpl']))
        {
            $this->menu();
            // system log messages
            $err=$app->get_log('err');
            $info=$app->get_log('info');
            $debug=$app->get_log('debug');

            if($err!='') 
            {
                $err='<div class="alert alert-danger alert-dismissible fade in" role="alert">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">×</span></button>
'.htmlspecialchars($err).'
</div>';
                $this->set($err,'msg');
            }
            if($info!='') 
            {
                $info='<div class="alert alert-info alert-dismissible fade in" role="alert">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">×</span></button>
'.htmlspecialchars($info).'
</div>';
                $this->set($info,'msg');
            }
            if($debug!='') 
            {
                $debug='<pre>'.htmlspecialchars($debug).'</pre>';
                $this->set($debug,'msg');
            }
            // rendering to template
            $output = file_get_contents($this->config['ui_tpl']);
            $this->set('<pre>'.print_r(get_included_files(),true).'</pre>');
            foreach($this->blocks as $alias => $content)
            {
                $tag="<!--@$alias-->";
                $output=str_replace($tag,$content,$output);
            }
        } else {
            echo 'template not found';
        }
        echo $output;
    }

}