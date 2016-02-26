<?php
namespace CORE;

class APP {

    private static $inst;

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    private function __construct() { }

    public function run() {
        \CORE::msg('debug','app->run');
        if(is_readable(DIR_APP.'/run.php')){
            include(DIR_APP.'/run.php');
        } else {
            \CORE::msg('debug','app/run not found');
        }
        // mvc router
        \CORE::ROUTER();
        \CORE::msg('debug','router');
    }

    public function stop() {
        \CORE::init()->unload();
    }

}