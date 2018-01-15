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

    public static function html_select($array,$opt=array()){
      $result='';
      if(!isset($opt['key'])) $opt['key']='';
      if(!isset($opt['attr'])) $opt['attr']='';
      if(!isset($opt['select'])) $opt['select']=0;
      if(!isset($opt['zero_text'])) $opt['zero_text']='';
      if(count($array)>0)
      {
          $result.='<select'.$opt['attr'].'>'.PHP_EOL;
          if($opt['zero_text']!='')
          {
              if($opt['select']<=0)
              {
                  $result.='<option value="0" selected="selected">'.$opt['zero_text'].'</option>'.PHP_EOL;
              } else {
                  $result.='<option value="0">'.$opt['zero_text'].'</option>'.PHP_EOL;
              }
          }
          if($opt['key']=='')
          {
              foreach ($array as $k => $v)
              {
                  if($opt['select']===$k){$s=' selected="selected"';} else {$s='';}
                  $result.='<option value="'.$k.'"'.$s.'>'.htmlspecialchars($v).'</option>'.PHP_EOL;
              }
          } else {
              foreach ($array as $k => $v)
              {
                  if($opt['select']===$k){$s=' selected="selected"';} else {$s='';}
                  $result.='<option value="'.$k.'"'.$s.'>'.htmlspecialchars($v[$opt['key']]).'</option>'.PHP_EOL;
              }
          }
          $result.='</select>'.PHP_EOL;
      }
      return $result;
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

    public static function modal($opt)
    {
      if(!isset($opt['id'])) $opt['id']='modal1';
      if(!isset($opt['title'])) $opt['title']='Modal title';
      if(!isset($opt['body'])) $opt['body']='...';
      if(!isset($opt['frm_attr'])) $opt['frm_attr']='';
      if(!isset($opt['default_btn_text'])) $opt['default_btn_text']='Default';
      return '
<!-- Modal -->
<div class="modal fade" id="'.$opt['id'].'" tabindex="-1" role="dialog" aria-labelledby="'.$opt['id'].'_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form'.$opt['frm_attr'].'>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="'.$opt['id'].'_label">'.$opt['title'].'</h4>
      </div>
      <div id="'.$opt['id'].'_body" class="modal-body">
        '.$opt['body'].'
      </div>
      <div class="modal-footer">
        <!--<button type="button" class="btn btn-default" data-dismiss="modal">'.\app::t('Close').'</button>-->
        <input type="submit" id="'.$opt['id'].'_submit" class="btn btn-primary" value="'.\app::t($opt['default_btn_text']).'">
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
        $result.='<li><a href="./"><i class="glyphicon glyphicon-home"></i></a></li>'."\n";
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
        $this->app->data(BRAND,'title');
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

    public static function boolMarker($bool,$color=false)
  	{
  		$class='';
  		if($bool)
  		{
  			if($color) $class=' text-success';
  			return '<span class="glyphicon glyphicon-ok'.$class.'" aria-hidden="true"></span>';
  		} else {
  			if($color) $class=' text-danger';
  			return '<span class="glyphicon glyphicon-remove'.$class.'" aria-hidden="true"></span>';
  		}
  	}

    public static function YesNo($bool)
  	{
  		if($bool)
  		{
  			return 'Yes';
  		} else {
  			return 'No';
  		}
  	}

    public static function norecords()
    {
      return '<div class="well" style="margin-top:20px;margin-bottom:20px;">No records found in the database.</div>';
    }

}
