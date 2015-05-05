<?php
namespace CORE\BC;

class APP {

    private static $inst;

    public static function init() {
        if(empty(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    private function __construct() {
        ROUTER::init();
    }

/*
#$REQUEST=REQUEST::init();
#ROUTER::init($REQUEST,$xmods); // check xmods
*/

}