<?php
namespace CORE\MVC\V;

class USER_V {

public function profile($model){
	if($model!=null){
		//\CORE::init()->msg('debug','Viewing user profile');
		//$UI->pos['main'].='';

	}
}

public function manage_users($model){
	if($model!=null){
		$users=$model->get_users();
		$users_count=count($users);
		$UI=\CORE\BC\UI::init();
		$UI->pos['main'].='
<div>
	<h4>Users: <span class="badge">'.$users_count.'</span>&nbsp;
	<button id="new_user" type="button" class="btn btn-success btn-xs"
	data-toggle="modal" data-target="#newUserForm">New user</button>
	</h4>
</div>
		';
		if($users_count>0){
			$UI->pos['main'].='
<table class="table table-bordered table-hover" style="width:auto;">
<thead>
<tr>
	<th>#</th>
	<th>USER</th>
	<th>GROUP</th>
	<th>STATUS</th>
	<th>LOGIN TIME</th>
	<th class="text-center">ACTION</th>
</tr>
</thead>
<tbody>
';
			$cnt=0;
		foreach ($users as $uid => $user) {
			$cnt++;
			$UI->pos['main'].='
<tr>
<td>'.$cnt.'</td>
<td>'.$user['username'].'</td>
<td>'.$user['gid'].'</td>
<td>'.$user['status'].'</td>
<td>'.$user['lastlogin'].'</td>
<td>
<div id="'.$uid.'" class="btn-group btn-group-xs">
	<button type="button" class="btn btn-default">edit</button>
	<button type="button" class="btn btn-default">delete</button>
</div>
</td>
</tr>
';
		}
		$UI->pos['main'].='</tbody>
</table>';
		} else {
			$UI->pos['main'].='
			<div class="well">'.\CORE::init()->lang('norecdb','No records found in the database').'</div>
			';
		}
	}
}

public function manage_groups($model){
	if($model!=null){
		$groups=$model->get_groups();
		$groups_count=count($groups);
		$UI=\CORE\BC\UI::init();
		$UI->pos['main'].='
		<div>
			<h4>Groups: <span class="badge">'.$groups_count.'</span>&nbsp;
			'.$UI::bootstrap_modal_btn('show_newGroupForm','NewGroup','New group').'
			</h4>
		</div>
		';
		$modal_body1='
		<div class="form-group">
		<label for="newgroup">Group name</label>
		<input type="text" class="form-control" id="newgroup" placeholder="">
		</div>
		';
		$modal_body2='
		<div class="form-group">
		<label for="editgroup">Group name
		<input type="hidden" id="ugid" value="0">
		</label>
		<input type="text" class="form-control" id="editgroup" placeholder="">
		</div>
		';
		$UI->pos['main'].=$UI::bootstrap_modal('NewGroup','New group',' id="frm_NewGroup"',$modal_body1,'addGroup','Add');
		$UI->pos['main'].=$UI::bootstrap_modal('EditGroup','Edit group',' id="frm_EditGroup"',$modal_body2,'updateGroup','Update');
		if($groups_count>0){
			$UI->pos['main'].='
		<table class="table table-bordered table-hover" style="width:auto;">
		<thead>
		<tr>
			<th>#</th>
			<th>GROUP</th>
			<th class="text-center">ACTION</th>
		</tr>
		</thead>
		<tbody>
		';
		$cnt=0;
		foreach ($groups as $gid => $group) {
			$cnt++;
			$UI->pos['main'].='
		<tr>
		<td>'.$cnt.'</td>
		<td>'.$group['groupname'].'</td>
		<td>
		<div id="'.$gid.'" class="btn-group btn-group-xs">
			<button type="button" class="btn btn-default edit" data-toggle="modal" data-target="#EditGroup">edit</button>
			<button type="button" class="btn btn-default delete">delete</button>
		</div>
		</td>
		</tr>
		';
		}
		$UI->pos['main'].='</tbody>
		</table>';
		} else {
			$UI->pos['main'].='
			<div class="well">'.\CORE::init()->lang('norecdb','No records found in the database').'</div>
			';
		}
	}
$UI->pos['js'].='<!-- groups js -->
<script type="text/javascript">

	/* ADD */

	$("#NewGroup").on("shown.bs.modal", function() {
	    $("#newgroup").focus();
	});
	$("#frm_NewGroup").submit(function(e){
		e.preventDefault();
	});
	$("#addGroup").click(function(){
		var xnewgroup = $("#newgroup").val();
		$.post("./?c=user&act=groups&ajax=add", {newgroup:xnewgroup}, function(data){
			if(data=="Group successfully added."){
				location.reload();
			} else {
				alert("Error. Check JS console log.");
				console.log(data);
			}
		});
	});

	/* EDIT */

	function IsJsonString(str) {
	    try {
	        JSON.parse(str);
	    } catch (e) {
	        return false;
	    }
	    return true;
	}
	$("#EditGroup").on("shown.bs.modal", function() {
	    $("#editgroup").focus();
	});
	$("#frm_EditGroup").submit(function(e){
		e.preventDefault();
	});
	$("button.edit").click(function(){
		var xgid = $(this).parent("div").attr("id");
		$.post("./?c=user&act=groups&ajax=edit", {gid:xgid}, function(data){
			if(IsJsonString(data)){
				var obj = JSON.parse(data);
				$("#editgroup").val(obj.group);
				$("#ugid").val(xgid);
			} else {
				alert("Error. Check JS console log.");
				console.log(data);
			}
		});
	});
	$("#updateGroup").click(function(){
		var xugid = $("#ugid").val();
		var xeditgroup = $("#editgroup").val();
		$.post("./?c=user&act=groups&ajax=update", {gid:xugid,editgroup:xeditgroup}, function(data){
			if(data=="Group successfully updated."){
				location.reload();
			} else {
				alert("The operation failed. Check JS console log.");
				console.log(data);
			}
		});
	});

	/* DELETE */

	$("button.delete").click(function(){
		var xgid = $(this).parent("div").attr("id");
		if(confirm("Delete this group?")){
			$.post("./?c=user&act=groups&ajax=del", {gid:xgid}, function(data){
				if(data=="Group successfully deleted."){
					location.reload();
				} else {
					alert("Error. Check JS console log.");
					console.log(data);
				}
			});
		}
	});

</script>';
}

}
