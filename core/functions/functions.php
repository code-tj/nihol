<?php
// count script execution time
function exec_time_start(){
	$start=microtime(true);
	return $start;
}
function exec_time_count($start){
	$time=microtime(true)-$start;
	return $time;
}
// ...

?>