<?php
class ui
{
    private $app=null;
    private $template_file='';
    private $menu_file='./ui/menu.php'; // ...

    function __construct($config=array())
    {
        $this->app=app::init();
        if(isset($config['ui_tpl'])) {$this->set_template($config['ui_tpl']);}
    }

    public function set_template($path)
    {
      if(is_readable($path)) $this->template_file=$path;
    }

    private function menu_items($items) // желательно вынести стили в часть описания меню
    {
      $result='';
      $c=count($items);
      for($i=0;$i<$c;$i++){
        if(isset($items[$i]['items']))
        {
          $result.='<li class="dropdown">
<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
'.$items[$i]['label'].' <span class="caret"></span></a>
<ul class="dropdown-menu">'.PHP_EOL;
          $result.=$this->menu_items($items[$i]['items']);
          $result.='</ul>
</li>'.PHP_EOL;
        } else {
          $result.='<li><a href="'.$items[$i]['url'].'">'.$items[$i]['label']."</a></li>".PHP_EOL;
        }
      }
      return $result;
    }

    public function load_menu()
    {
        if(is_readable($this->menu_file))
        {
            require $this->menu_file;
            if(isset($menu))
            {
              foreach ($menu as $name => $items) {
                $this->app->data->set('<ul class="nav navbar-nav">'.PHP_EOL,$name);
                $this->app->data->set($this->menu_items($items),$name);
                $this->app->data->set('</ul>'.PHP_EOL,$name);
              }
            }
        } else {
          $this->app->log->set('debug','menu file not found');
        }
    }

    public function messages()
    {
      $err=$this->app->log->get('err');
      $info=$this->app->log->get('info');
      $debug=$this->app->log->get('debug');
      if($err!='')
      {
        $err='<div class="alert alert-danger alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span></button>
        '.htmlspecialchars($err).'
        </div>';
        $this->app->data->set($err,'messages');
      }
      if($info!='')
      {
        $info='<div class="alert alert-info alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span></button>
        '.htmlspecialchars($info).'
        </div>';
        $this->app->data->set($info,'messages');
      }
      if($debug!='')
      {
        $debug='<pre>'.htmlspecialchars($debug).'</pre>';
        $this->app->data->set($debug,'messages');
      }
    }

    public function render()
    {
      $result='';
      if($this->template_file!='')
      {
        $this->app->data->set('BWEB','title');
        $this->app->data->set('OiT Intranet portal','brand');
        // render
        $this->load_menu();
        //$this->app->log->set('debug',print_r(get_included_files(),true));
        $this->messages();
        $blocks=$this->app->data->get_blocks();
        $result=file_get_contents($this->template_file);
        foreach($blocks as $alias => $content)
        {
            $tag="<!--@$alias-->";
            $result=str_replace($tag,$content,$result);
        }
      } else {
        echo 'Template not found.';
      }
      echo $result;
    }


}
