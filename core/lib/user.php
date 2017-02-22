<?php
class user
{
    private $uid=0;
    private $gid=0;
    private $name='guest';
    private $log=null;

    function __construct($log)
    {
        $this->log=$log;
    }
    
}