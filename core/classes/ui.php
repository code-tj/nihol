<?php
namespace CORE;

class UI {

    private static $inst;

    private $tpl='';
    public $pos=array(
        'meta'=>'',
        'link'=>'',
        'title'=>'',
        'js'=>'',
        'mainmenu'=>'',
        'user1'=>'',
        'main'=>'',
        );
    private $pages=array(); // assoc

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
            // init
            \CORE::init()->msg('debug','ui initialization');
        }
        return self::$inst;
    }

    private function __construct() {
        global $conf;
        if(isset($conf['ui_tpl']) && is_readable($conf['ui_tpl'].'/tpl.php')){
            $this->tpl=$conf['ui_tpl'].'/tpl.php';
        } else {
            if(is_readable(PATH_UI.'/tpl/default/tpl.php')){
                $this->tpl=PATH_UI.'/tpl/default/tpl.php';
            } else {
                echo 'Template not found';
            }
        }
    }

    public static function core_msg_show($type,$style,$messages) {
        return '<div class="alert alert-'.$style.' alert-dismissable" role="alert">
                    <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                    </button>
                    <strong>'.strtoupper($type).':</strong><br>
                    '.$messages.'
                </div>
                ';
    }

    public static function core_msg($type='') {
        $msg_array=\CORE::init()->get_msg_arr();
        $styles=array(
            'error' => 'danger',
            'info' => 'info',
            'debug' => 'warning'
            );
        if($type!=''){
            if(isset($msg_array[$type])){
                if($msg_array[$type]!='') {
                    echo UI::init()->core_msg_show($type,$styles[$type],$msg_array[$type]);
                }
            }
        } else {
            foreach ($msg_array as $type => $msg) {
                if($msg!='') {
                    echo UI::init()->core_msg_show($type,$styles[$type],$msg);
                }
            }
        }
    }

    public function tpl(){ return $this->tpl; }
    public function get_pages(){ return $this->pages; }
    public function set_pages($pages){ $this->pages=$pages; }

    public function show($name='main'){
        if(isset($this->pos[$name])){ echo $this->pos[$name]; }
    }

    public function render(){
        global $conf; // start to count exec time, in index.php
        $UI=\CORE\UI::init();
        if($UI->tpl()!=''){include($UI->tpl());}
    }

    public function static_page($alias='',$single=false){
        if(isset($this->pages[$alias])){
            if(\CORE::init()->lang!='' && !$single) {$lang='_'.\CORE::init()->lang;} else {$lang='';}
            $path=DIR_APP.'/pages/'.$this->pages[$alias].$lang.'.php';
            if(is_readable($path)){
                include($path);
                // \CORE::msg('debug','include page: '.$this->pages[$alias]);
            } else {
                \CORE::msg('error','Page is not found');
            }
        } else {
            \CORE::msg('error','Page is not available');
            print_r($this->pages);
        }
    }

    public static function bootstrap_modal_btn($id='show_myModal',$target='myModal',$text='ShowModal'){
        $result='<button id="'.$id.'" type="button" class="btn btn-success btn-xs"
        data-toggle="modal" data-target="#'.$target.'">'.$text.'</button>
        ';
        return $result;
    }

    public static function bootstrap_modal($id='myModal',$title='Modal title',$frm='',$body='...',$btn_id='defaultAction',$btn_txt='Save changes'){
        $result='
    <!-- Modal -> '.$id.' -->
    <div class="modal fade" id="'.$id.'" tabindex="-1" role="dialog" 
    aria-labelledby="'.$id.'Label" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
        <form'.$frm.'>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="'.$id.'Label">'.$title.'</h4>
          </div>
          <div id="'.$id.'Body" class="modal-body">
            '.$body.'
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">'.\CORE::t('close','Close').'</button>
            <input type="submit" id="'.$btn_id.'" class="btn btn-primary" value="'.$btn_txt.'">
          </div>
          </form>
        </div>
      </div>
    </div>
    <!-- /Modal -> '.$id.' -->
    ';
        return $result;
    }

    public static function html_list($ls=array(),$key='',$attr=' id="select"',$sel=0,$zero=''){
        $list=''; $s='';
            if(count($ls)>0){
                $list.="<select".$attr.">\n";
                if($zero!='') {
                    if($sel<=0) {
                        $list.='<option value="0" selected="selected">'.$zero."</option>\n";
                    } else {
                        $list.='<option value="0">'.$zero."</option>\n";
                    }
                }
                if($key==''){
                    foreach ($ls as $k => $val) {
                        if($sel===$k){$s=' selected="selected"';} else {$s='';}
                        $list.='<option value="'.$k.'"'.$s.'>'.htmlspecialchars($val)."</option>\n";
                    }
                } else {
                    foreach ($ls as $k => $val) {
                        if($sel===$k){$s=' selected="selected"';} else {$s='';}
                        $list.='<option value="'.$k.'"'.$s.'>'.htmlspecialchars($val[$key])."</option>\n";
                    }
                }
                $list.="</select>\n";
            }
        return $list;
    }

}