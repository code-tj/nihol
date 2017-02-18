<?php
class UI {

    private static $inst;
    public $template='';
    //private $languages=array('en'=>'English','ru'=>'Русский','tj'=>'Тоҷикӣ');
    //private $multilang=false;
    //public $lang='en'; // should switch via $_GET['lang'] and $languages;

    public $p=array(
        'meta'=>'',
        'link'=>'',
        'title'=>'',
        'js'=>'',
        'main'=>''
        );

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
            self::$inst->check_language();
            self::$inst->set_template(CORE::init()->config('tpl'));
        }
        return self::$inst;
    }

    public function p($data,$position='main'){
        if(isset($this->p[$position])){
            $this->p[$position].=$data;
        } else {
            $this->p[$position]=$data;
            // debug - show name of new 'p' item =)
        }
    }

    public function show($position='main'){
        if(isset($this->p[$position])){ echo $this->p[$position]; }
    }

    public function set_template($template_file){
        if(is_readable($template_file)){
            $this->template=$template_file;
        } else {
            echo 'template not found';
        }
    }

    public function get_system_messages(){
        $result=''; $s='warning';
        $style=array('error'=>'danger','info'=>'info','ok'=>'success','debug'=>'warning');
        $messages = CORE::init()->get_messages();
        // check is debug on - then show debug messages
        foreach ($messages as $type => $msgs) {
            if(isset($style[$type])) {$s=$style[$type];}
            $result.='<div class="alert alert-'.$s.' alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span></button>'."\n";
            foreach ($msgs as $index => $msg) {
                $result.=$msg."<br>\n";
            }
            $result.="</div>\n";
        }
        return $result;
    }

    public function render_tpl($path,$content){
        $output='';
        if(is_readable($path) && count($content)>0){
            $output = file_get_contents($path);
            foreach($content as $key => $value) {
                $tagToReplace = "<!--@$key-->";
                $output = str_replace($tagToReplace, $value, $output);
            }
        }
        return $output;
    }

    public function render(){
        $this->p($this->get_system_messages(),'msg');
        echo $this->render_tpl($this->template,$this->p);
    }

    public function check_language(){
        //CORE::msg('checking language','debug');
        
    }

    public function menu(){
        // load menus from DB
        $menu = CORE\WIDGETS\MENU::get_menu_from_db(); // ? load from DB?
        // put each menu to the specific template position
        foreach ($menu as $menu_name => $items) {
            foreach ($items as $index => $item) {
                $this->p($item,'menu_'.$menu_name);
            }
        }
    }



}