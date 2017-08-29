<?php
class ui
{
    private $menu_file='./ui/menu.php'; // ...
    private $template_path='';
    private $app=null;

    function __construct($opt=array())
    {
      $this->app = my::app();
      $this->user = $this->app->module('user');
      if(isset($opt['ui_template']))
      {
        if(is_readable($opt['ui_template']))
        {
          $this->template_path=$opt['ui_template'];
        }
      }

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
          if(isset($items[$i]['url']))
          {
            $result.='<li><a href="'.$items[$i]['url'].'">'.$items[$i]['label']."</a></li>".PHP_EOL;
          } else {
            $result.=$items[$i]['label'].PHP_EOL;
          }
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
                $this->app->data('<ul class="nav navbar-nav">'.PHP_EOL,$name);
                $this->app->data($this->menu_items($items),$name);
                $this->app->data('</ul>'.PHP_EOL,$name);
              }
            }
        } else {
          $this->app->log('debug','menu file not found');
        }
    }

    public function messages()
    {
      $log=my::module('log');
      $err=$log->get('err');
      $info=$log->get('info');
      $debug=$log->get('debug');
      if($err!='')
      {
        $err='<div class="alert alert-danger alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span></button>
        '.htmlspecialchars($err).'
        </div>';
        $this->app->data($err,'messages');
      }
      if($info!='')
      {
        $info='<div class="alert alert-info alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span></button>
        '.htmlspecialchars($info).'
        </div>';
        $this->app->data($info,'messages');
      }
      if($debug!='')
      {
        $debug='<pre>'.htmlspecialchars($debug).'</pre>';
        $this->app->data($debug,'messages');
      }
    }

    public static function modal($title='',$body='',$btn='',$modal_id='modal1',$frm_attr='',$btn_text='Update')
    {
return '
<!-- Modal -->
<div class="modal fade" id="'.$modal_id.'" tabindex="-1" role="dialog" aria-labelledby="'.$modal_id.'_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form'.$frm_attr.'>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="'.$modal_id.'_label">'.$title.'</h4>
      </div>
      <div id="'.$modal_id.'_body" class="modal-body">
        '.$body.'
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">'.\app::t('Close').'</button>
        <input type="submit" id="'.$modal_id.'_btn" class="btn btn-primary" value="'.\app::t($btn_text).'">
      </div>
      </form>
    </div>
  </div>
</div>
<!-- /Modal -->
';
    }

    public static function modal_trigger($modal_id='modal1',$text='Show Modal',$attr='')
    {
return '<button id="show_'.$modal_id.'" type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#'.$modal_id.'"'.$attr.'>'.$text.'</button>';
    }

    public function render_breadcrumb()
    {
      $result='';
      $appdata=my::module('appdata');
      $breadcrumb=$appdata->get_breadcrumb();
      $count=count($breadcrumb);
      if($count>0)
      {
        $result.='<ol class="breadcrumb">'."\n";
        $result.='<li><a href="./">Dashboard</a></li>'."\n";
        for ($i=0; $i < $count; $i++) {
          if(($i+1)==$count)
          {
            $result.='<li class="active">'.$breadcrumb[$i]['title'].'</li>'."\n";
          } else {
            $result.='<li><a href="'.$breadcrumb[$i]['link'].'">'.$breadcrumb[$i]['title'].'</a></li>'."\n";
          }
        }
        $result.="</ol>\n";
      }
      $appdata->set($result,'breadcrumb');
    }

    public function render($template_path='')
    {
      $result='';
      $appdata=my::module('appdata');
      if($template_path==''){ $template_path=$this->template_path; }
      if(is_readable($template_path))
      {
        $this->app->data('BWEB','title');
        $this->app->data(BRAND,'brand');
        // render
        $this->load_menu();
        //$app->log('debug',print_r(get_included_files(),true));
        $this->messages();
        $this->render_breadcrumb();
        $blocks=$appdata->get_blocks();
        $result=file_get_contents($template_path);
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

    public function jsFile($src)
    {
      $this->app->data('<script type="text/javascript" src="'.$src.'"></script>','js');
    }

}
