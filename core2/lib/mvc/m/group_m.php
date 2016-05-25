<?php
namespace CORE\MVC\M;

class GROUP_M {


public function get_groups(){
	$groups=array();
	$DB=\DB::init();
	$recs=$DB->get_records('n-groups','gp-gid',' ORDER BY `gp-sort`');
	foreach($recs as $key => $val){
		$groups[$key]=$val['gp-group'];
	}
	return $groups;
}

public function add($group='',$sort=0){
	$group=trim($group);
	if($group=='' && isset($_POST['newgroup'])){
		$group=trim($_POST['newgroup']);
	}
	if($sort==0 && isset($_POST['sort'])){
		$sort=(int) $_POST['sort'];
	}
	if($group!=''){
		$DB=\DB::init();
		if($DB->connect()){
			if($DB->isUnique('n-groups','gp-group',$group)){
				$sql = "INSERT INTO `n-groups` SET `gp-group`=:group, `gp-sort`=:sort;";
				$sth = $DB->dbh->prepare($sql);
				$sth->execute(array('group'=>$group,'sort'=>$sort));
				$DB->query_count();
				\CORE::msg('info','Group successfully added.');
			}
		}
	} else {
		\CORE::msg('error','Incorrect user data.');
	}
}

public function edit($gid=0){
	if($gid==0 && isset($_POST['gid'])){
		$gid=(int) $_POST['gid'];
	} else {
		$gid=(int) $gid;
	}	
	if($gid>0){
		$DB=\DB::init();
		if($DB->connect()){
			$sql = "SELECT * FROM `n-groups` WHERE `gp-gid`=:gid;";
			$sth = $DB->dbh->prepare($sql);
			$sth->execute(array('gid'=>$gid));
			$DB->query_count();
			if($sth->rowCount()==1){
				$r=$sth->fetch();
				echo json_encode(array('group'=>htmlspecialchars($r['gp-group'])));
			}
		}
	} else {
		\CORE::msg('error','Incorrect ID.');
	}
}

public function update($gid=0,$group='',$sort=-1){
	if($gid==0 && isset($_POST['gid'])){
		$gid=(int) $_POST['gid'];
	}
	if($group=='' && isset($_POST['group'])){
		$group=trim($_POST['group']);
	}
	if($sort==-1 && isset($_POST['sort'])){
		$sort=(int) $_POST['sort'];
	}
	if($gid>0 && $group!=''){
		$DB=\DB::init();
		if($DB->connect()){
			if($DB->isUnique('n-groups','gp-group',$group)){
				if($sort>0) {
					$sql = "UPDATE `n-groups` SET `gp-group`=:group, `gp-sort`=:sort WHERE `gp-gid`=:gid;";
					$sth = $DB->dbh->prepare($sql);
					$sth->execute(array('group'=>$group,'sort'=>$sort,'gid'=>$gid));
				} else {
					$sql = "UPDATE `n-groups` SET `gp-group`=:group WHERE `gp-gid`=:gid;";
					$sth = $DB->dbh->prepare($sql);
					$sth->execute(array('group'=>$group,'gid'=>$gid));
				}
				$DB->query_count();
				\CORE::msg('info','Group successfully updated.');
			}
		}
	} else {
		\CORE::msg('error','Incorrect user data.');
	}
}

public function del($gid=0){
	if($gid==0 && isset($_POST['gid'])){
		$gid=(int) $_POST['gid'];
	} else {
		$gid=(int) $gid;
	}
	if($gid>0){
		$DB=\DB::init();
		if($DB->connect()){
			if($DB->del('n-groups','gp-gid',$gid)){
				\CORE::msg('info','Group successfully deleted.');
			} else {
				\CORE::msg('error','Group was not deleted.');
			}
		}
	} else {
		\CORE::msg('error','Incorrect ID.');
	}
}

}