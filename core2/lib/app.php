<?php

class APP {

    private static $instance; // singleton pattern
    private $conf=array(); // configuration

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

    private function load_conf($config_file)
    {
        if(is_readable($config_file))
        {
            require($config_file);
            $this->conf=$conf;
        } else {
            echo 'Configuration file not found';
            exit;
        }
    }

    public function get_conf($key)
    {
        if(isset($this->conf[$key]))
        {
            return $this->conf[$key];
        } else {
            return '';
        }
    }

    public function run($config_file='conf.php')
    {
        $this->load_conf($config_file);
        CORE::msg('Running application...');
        /*
        what if debug -> enable ERR_ALL etc and set messages
        if not debug - destroy all debug messages in msg method
        translation - needed by def 1 lang
        
        */
    }

}