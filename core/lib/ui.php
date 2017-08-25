<?php
class ui
{
    private $menu_file='./ui/menu.php'; // ...

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
      $app=app::init();
        if(is_readable($this->menu_file))
        {
            require $this->menu_file;
            if(isset($menu))
            {
              foreach ($menu as $name => $items) {
                $app->data('<ul class="nav navbar-nav">'.PHP_EOL,$name);
                $app->data($this->menu_items($items),$name);
                $app->data('</ul>'.PHP_EOL,$name);
              }
            }
        } else {
          $app->log('debug','menu file not found');
        }
    }

    public function messages()
    {
      $app=app::init();
      $err=$app->log->get('err');
      $info=$app->log->get('info');
      $debug=$app->log->get('debug');
      if($err!='')
      {
        $err='<div class="alert alert-danger alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span></button>
        '.htmlspecialchars($err).'
        </div>';
        $app->data($err,'messages');
      }
      if($info!='')
      {
        $info='<div class="alert alert-info alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span></button>
        '.htmlspecialchars($info).'
        </div>';
        $app->data($info,'messages');
      }
      if($debug!='')
      {
        $debug='<pre>'.htmlspecialchars($debug).'</pre>';
        $app->data($debug,'messages');
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
      $app=app::init();
      $breadcrumb=$app->data->get_breadcrumb();
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
      $app->data($result,'breadcrumb');
    }

    public function render($template_file)
    {
      $app=app::init();
      $result='';
      if(is_readable($template_file))
      {
        $app->data('BWEB','title');
        $app->data(BRAND,'brand');
        // render
        $this->load_menu();
        //$app->log('debug',print_r(get_included_files(),true));
        $this->messages();
        $this->render_breadcrumb();
        $blocks=$app->data->get_blocks();
        $result=file_get_contents($template_file);
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
      \app::init()->data('<script type="text/javascript" src="'.$src.'"></script>','js');
    }

}
