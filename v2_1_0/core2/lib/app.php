<?php
class APP {

    private static $instance; // singleton pattern

    public static function init()
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public static function run()
    {
        $USER=USER::init(); // checking session when user init (inside user class)
        $UI=UI::init(); // language, translations
        $APP=APP::init();
        $APP->check_mode();
        if(is_readable('./app/run.php')){ include('./app/run.php'); }       
        $APP->module = new MODULE();
        // load module(s)
        /*
        1. core init
        2. app run
            - load config (db)
            - check app mode
        3. UI init
            - check language
            - load translation
            - load menu
            - (load widgets)
            - set template (init)
        4. USER init
        5. module init
            - define route
            - check access (acl, check mode 2 for admin only)
            - load module (mvc)
        */
        $UI->menu();
        $APP->stop();
        $UI->render();
    }

    public function check_mode(){
        if(CORE::init()->config('mode')==0){
            echo 'currently down for maintenance...'; exit;
        }
    }

    public function load_module(){
        //CORE::msg('load module','debug');
    }

    public function stop(){
        //CORE::msg('app stop','debug');
        DB::init()->close(); // it will load DB class (class file)!
        //if(CORE::init()->config('debug')==1) UI::init()->p('<pre>'.print_r(get_included_files(),true).'</pre>');
        ///if(CORE::init()->config('debug')==1) CORE::msg('<pre>'.print_r(get_included_files(),true).'</pre>');
    }

}