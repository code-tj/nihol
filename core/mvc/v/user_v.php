<?php
namespace CORE\MVC\V;

class USER_V {


public function manage($model){
	$result='';
	$UI=\CORE\BC\UI::init();
	$groups=$model->get_groups();
	$users=$model->get_users();
	$counter=count($users);
	$result.='
<div>
	<h4>Users: <span class="badge">'.$counter.'</span>&nbsp;
	'.$UI::bootstrap_modal_btn('show_NewUser','NewUser','New user').'
	</h4>
</div>
';
	$modal_body_new='
<div class="form-group">
	<label for="new_group">Group</label>
	'.$UI::html_list($groups,'',' id="new_group" class="form-control"',2).'
</div>		
<div class="form-group">
	<label for="new_user">Username</label>
	<input type="text" class="form-control" id="new_user" placeholder="username">
</div>
<div class="form-group">
	<label for="new_pwd">Password</label>
	<input type="password" class="form-control" id="new_pwd" placeholder="password">
</div>
<div class="form-group">
	<input type="button" class="btn btn-default" id="new_pwd_gen" value="Generate password">
	<span id="new_pwd_gen_val" style="margin-left:10px;"></span>
</div>
<div>
  <div class="checkbox">
    <label for="new_status">
      <input type="checkbox" id="new_status" value="1" checked>
      User enabled
    </label>
  </div>
</div>
';
	$result.=$UI::bootstrap_modal('NewUser','New user',' id="frm_NewUser"',$modal_body_new,'addUser','Add');
	$modal_body_edit='
<div class="form-group">
	<label for="edit_group">Group</label>
	'.$UI::html_list($groups,'',' id="edit_group" class="form-control"',2).'
</div>		
<div class="form-group">
	<input type="hidden" id="edit_uid" value="0">
	<label for="edit_user">Username</label>
	<input type="text" class="form-control" id="edit_user" placeholder="username">
</div>
<div>
  <div class="checkbox">
    <label for="edit_pwd_change">
      <input type="checkbox" id="edit_pwd_change" value="1">
      Change password
    </label>
  </div>
</div>
<div id="pwd_box" class="hidden">
	<div class="form-group">
		<label for="edit_pwd">New password</label>
		<input type="password" class="form-control" id="edit_pwd" placeholder="new password">
	</div>
	<div class="form-group">
		<input type="button" class="btn btn-default" id="edit_pwd_gen" value="Generate password">
		<span id="edit_pwd_gen_val" style="margin-left:10px;"></span>
	</div>
</div>
<div>
  <div class="checkbox">
    <label for="edit_status">
      <input type="checkbox" id="edit_status" value="1" checked>
      User enabled
    </label>
  </div>
</div>
';
	$result.=$UI::bootstrap_modal('EditUser','Edit user',' id="frm_EditUser"',$modal_body_edit,'updateUser','Update');
	if($counter>0){
		$result.='
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
			$result.='
<tr>
	<td>'.$cnt.'</td>
	<td>'.$user['user'].'</td>
	<td>'.$user['gid'].'</td>
	<td>'.$user['status'].'</td>
	<td>'.$user['lastlogin'].'</td>
	<td>
		<div id="'.$uid.'" class="btn-group btn-group-xs">
		<button type="button" class="btn btn-default user_edit" data-toggle="modal" data-target="#EditUser">edit</button>
		<button type="button" class="btn btn-default user_del">delete</button>
		</div>
	</td>
</tr>
';
		}
		$result.="</tbody></table>\n";
	} else {
		$result.='<div class="well">'.\CORE::init()->lang('norecdb','No records found in the database.').'</div>';
	}
	$UI->pos['js'].='
<script type="text/javascript">
	$(document).ready(function() {

		function IsJsonString(str) {
			try { JSON.parse(str); } catch(e) { return false; }
			return true;
		}

		function generate_pwd(len) {
		    var length = len,
		        charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
		        retVal = "";
		    for (var i = 0, n = charset.length; i < length; ++i) {
		        retVal += charset.charAt(Math.floor(Math.random() * n));
		    }
		    return retVal;
		}

		$("#new_pwd_gen").click(function(){
			var xpwd=generate_pwd(10);
			$("#new_pwd").val(xpwd);
			$("#new_pwd_gen_val").text(xpwd);			
		});

		// ADD

		$("#NewUser").on("shown.bs.modal", function() {
			$("#new_user").focus();
		});

		$("#frm_NewUser").submit(function(e){
			e.preventDefault();
		});

		$("#addUser").click(function(e){
			var xgid = $("#new_group").val();
			var xuser = $("#new_user").val();
			var xpwd = $("#new_pwd").val();
			var xstatus = 1;
			if(!$("#new_status").prop("checked")){ xstatus=0; }
			$.post("./?c=user&act=manage&do=add&ajax", {user:xuser,password:xpwd,gid:xgid,status:xstatus}, function(data){
				if(data=="New user successfully added."){
					location.reload();
				} else {
					if(IsJsonString(data)){
						var obj = JSON.parse(data);
						alert(obj.errors);
					} else {
						alert("Error. Check JS console log.");
						console.log(data);
					}
				}
			});
		});

		// EDIT

		$("#EditUser").on("shown.bs.modal", function() {
		    $("#edit_user").focus();
		});

		$("#frm_EditUser").submit(function(e){
			e.preventDefault();
		});

		$("#edit_pwd_gen").click(function(){
			var xpwd=generate_pwd(10);
			$("#edit_pwd").val(xpwd);
			$("#edit_pwd_gen_val").text(xpwd);
		});

		$("#edit_pwd_change").click(function(){
			if($(this).prop("checked")){
				$("#pwd_box").removeClass("hidden");
			} else {
				$("#edit_pwd").val("");
				$("#edit_pwd_gen_val").text("");
				$("#pwd_box").addClass("hidden");
			}
		});

		$("button.user_edit").click(function(){
			var xuid = $(this).parent("div").attr("id");
			$.post("./?c=user&act=manage&do=edit&ajax", {uid:xuid}, function(data){
				$("#edit_pwd").val("");
				$("#edit_pwd_gen_val").text("");
				if(IsJsonString(data)){
					var obj = JSON.parse(data);
					$("#edit_uid").val(obj.uid);
					$("#edit_group").val(obj.gid);
					$("#edit_user").val(obj.user);
					if(obj.status==1){
						$("#edit_status").prop("checked",true);
					} else {
						$("#edit_status").prop("checked",false);
					}
				} else {
					alert("Error. Check JS console log.");
					console.log(data);
				}
			});
		});

		// UPDATE

		$("#updateUser").click(function(e){
			var xuid = $("#edit_uid").val();
			var xgid = $("#edit_group").val();
			var xuser = $("#edit_user").val();
			var xchpwd = 0;
			var xpwd = $("#edit_pwd").val();
			var xstatus = 1;
			if($("#edit_pwd_change").prop("checked")){ xchpwd=1; }
			if(!$("#edit_status").prop("checked")){ xstatus=0; }
			$.post("./?c=user&act=manage&do=update&ajax", {uid:xuid,gid:xgid,user:xuser,chpwd:xchpwd,password:xpwd,status:xstatus}, function(data){
				if(data=="User data successfully updated."){
					location.reload();
				} else {
					if(IsJsonString(data)){
						var obj = JSON.parse(data);
						alert(obj.errors);
					} else {
						alert("Error. Check JS console log.");
						console.log(data);
					}
				}
			});
		});

		// DEL

		$("button.user_del").click(function(){
			var xuid = $(this).parent("div").attr("id");
			if(confirm("Delete this user?")){
				$.post("./?c=user&act=manage&do=del&ajax", {uid:xuid}, function(data){
					if(data=="User successfully deleted."){
						location.reload();
					} else {
						alert("Error. Check JS console log.");
						console.log(data);
					}
				});
			}
		});

	});
</script>
';
	return $result;
}

public function profile($model){
	$result='';
	if($model!=null){
		
	}
	return $result;
}

public function change_pwd(){
	$result='';
	$UI=\CORE\BC\UI::init();
	$result.='
<div class="col-md-4">
	<h4>Change password:</h4>
	<br>
	<form id="frm_chpwd">
	<div class="form-group">
		<label for="pwd">New password</label>
		<input type="password" class="form-control" id="pwd" placeholder="password">
	</div>
	<div class="form-group">
		<label for="pwd2">Retype new password</label>
		<input type="password" class="form-control" id="pwd2" placeholder="password">
	</div>
	<div class="form-group">
		<input type="submit" id="chpwd" class="btn btn-danger" value="Change password">
	</div>
	</form>
</div>
';
	$UI->pos['js'].='
<script type="text/javascript">
$(document).ready(function(){

	function check_pwd(pwd){
		var xlen = pwd.length
		if(xlen>=8 && xlen<255){ return true; } else { return false; }
	}

	$("#pwd").focus();

	$("#frm_chpwd").submit(function(e){
		e.preventDefault();
	});

	$("#chpwd").click(function(){
		var xpwd = $("#pwd").val();
		if(check_pwd(xpwd)){
			if(xpwd==$("#pwd2").val()){
				$.post("./?c=user&act=passwd&ajax", {pwd:xpwd}, function(data){
					if(data=="Password successfully changed."){
						alert("Password successfully changed.");
						window.location.replace("./");
						// location.reload();
					} else {
						alert("Error. Check JS console log.");
						console.log(data);
					}
				});
			} else {
				alert("Password does not match the confirm password.");
			}
		} else {
			alert("Password is not valid.");
		}
	});

});
</script>';
	return $result;
}



}
