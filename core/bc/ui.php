<?php
namespace CORE\BC;

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
        'user2'=>'',
        'main'=>'',
        );
    private $pages=array('home'=>'home');

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

    public function show($name='main'){
        if(isset($this->pos[$name])){ echo $this->pos[$name]; }
    }

    public function show_template(){
        global $conf,$start;
        $UI=\CORE\BC\UI::init();
        if($UI->tpl()!=''){include($UI->tpl());}
    }

    public function static_page($alias=''){
        if(isset($this->pages[$alias])){
            $path=DIR_APP.'/pages/'.$this->pages[$alias].'.php';
            if(is_readable($path)){
                if(true){ // \SEC::init()->acl('page',$alias)
                    include($path);
                    // \CORE::msg('debug','include page: '.$this->pages[$alias]);
                }
            } else {
                \CORE::msg('error','Page is not found');
            }
        } else {
            \CORE::msg('error','Page is not available');
        }
    }

    public static function modal($id='xModal',$btn=''){
        $result='
    <!-- Modal -->
    <div class="modal fade" id="'.$id.'" tabindex="-1" role="dialog" aria-labelledby="'.$id.'Label" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="'.$id.'Label">Modal title</h4>
          </div>
          <div id="'.$id.'Body" class="modal-body">
            ...
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /Modal -->
    ';
        return $result;
    }

}