<?php
function get_start_time(){
	$start=microtime(true);
	return $start;
}

function get_exec_time($start){
	$time=microtime(true)-$start;
	return $time;
}
?>