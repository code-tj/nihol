<?php
class log
{
    private $logs=array(); // 'debug',err','info','user'

    public function msg($type,$msg)
    {
        if(isset($this->logs[$type]))
        {
            $this->logs[$type].=$msg;
        } else {
            $this->logs[$type]=$msg;
        }
    }

    public function get($type)
    {
        $data='';
        if(isset($this->logs[$type])) $data=$this->logs[$type];
        return $data;
    }

}