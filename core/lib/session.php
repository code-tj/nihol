<?php
class session
{
    private $id='';
    private $started=false;

    function __construct()
    {
        if(isset($_COOKIE['PHPSESSID'])){
            session_start();            
            $this->id=session_id();
            $this->started=true;
            //app::log('debug','session started');
        }
    }
    public function started()
    {
        return $this->started;
    }
    public function id()
    {
        return $this->id;
    }
    public function get($key)
    {
        // is Valid
        if(isset($_SESSION[AL.'_'.$key]))
        {
            return $_SESSION[AL.'_'.$key];
        } else {
            return '';
        }
    }
    public function set($key,$val){
        // is Valid
        $_SESSION[AL.'_'.$key]=$val;
    }
    public function remove($key)
    {
        if(isset($_SESSION[AL.'_'.$key]))
        {
            unset($_SESSION[AL.'_'.$key]);
        }
    }
    public function destroy()
    {
        
    }
}