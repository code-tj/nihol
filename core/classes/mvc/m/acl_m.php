<?php
namespace CORE\MVC\M;

class ACL_M {

private $modules=array();

function __construct(){
	$this->modules=\CORE::init()->get_modules();
}

public function get_acl_db($all=false){
	$acl=array();
	$DB=\DB::init();
	if($DB->connect()){
		//$where=" WHERE `acl-c`=:c";
		$sql="SELECT * FROM `n-acl`;";
		$sth=$DB->dbh->prepare($sql);
		$sth->execute();
		$DB->query_count();
		if($sth->rowCount()>0){
			while($r=$sth->fetch()){
				$acl[$r['acl-type']]=array(
					'c'=>$r['acl-c'],
					'act'=>$r['acl-act'],
					'xid'=>$r['acl-xid'],
					'val'=>$r['acl-val'],
					);
			}
		}
	}
	return $acl;
}

public function get_acl_json($path=''){
	if($path=='') $path=PATH_APP.'/acl.json';
	$acl=array();
	if(is_readable($path)){
		$json = file_get_contents($path);
		$acl = json_decode($json,true);
	}
	return $acl;
}


}