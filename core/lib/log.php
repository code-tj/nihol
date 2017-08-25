<?php
class log
{
  private $log=array(
    'err'=>'',
    'debug'=>'',
  );
  public function set($cat,$msg)
  {
    if($cat=='debug' && !DEBUG){return;}
    if(isset($this->log[$cat]))
		{
		    $this->log[$cat].=$msg.PHP_EOL;
		} else {
		    $this->log[$cat]=$msg.PHP_EOL;
		}
  }
  public function get($cat)
  {
		$result='';
		if(isset($this->log[$cat])) $result=$this->log[$cat];
		return $result;
  }
}
