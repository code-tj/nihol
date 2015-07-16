<?php
namespace CORE\MVC\V;

class USER_V {

public function profile($model){
	if($model!=null){
		//\CORE::init()->msg('debug','Viewing user profile');
		//$UI->pos['main'].='';

	}
}

public function passwd($model){
	if($model!=null){
		$UI=\CORE\BC\UI::init();
		$UI->pos['main'].='
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
	$UI->pos['js'].='<!-- change pwd js -->
	<script type="text/javascript">
	$(document).ready(function(){
		function CheckPwd(pwd){
			var xlen = pwd.length
			if(xlen>=8 && xlen<255){ return true; } else { return false; }
		}

		$("#pwd").focus();

		$("#frm_chpwd").submit(function(e){
			e.preventDefault();
		});

		$("#chpwd").click(function(){
			var xpwd = $("#pwd").val();
			if(CheckPwd(xpwd)){
				if(xpwd==$("#pwd2").val()){
					$.post("./?c=user&act=change_password&ajax=passwd", {pwd:xpwd}, function(data){
						if(data=="Password successfully changed."){
							alert("Password successfully changed.");
							window.location.replace("./");
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
	}
}

public function manage_users($model){
	if($model!=null){
		$groups=$model->get_groups();
		$users=$model->get_users();
		$users_count=count($users);
		$UI=\CORE\BC\UI::init();
		$UI->pos['main'].='
		<div>
			<h4>Users: <span class="badge">'.$users_count.'</span>&nbsp;
			'.$UI::bootstrap_modal_btn('show_newUserForm','NewUser','New user').'
			</h4>
		</div>
		';
		$modal_body1='
		<div class="form-group">
		<label for="newugroup">Group</label>
		'.$UI::html_list($groups,'groupname',' id="sgroup" class="form-control"',$sel=2).'
		</div>		
		<div class="form-group">
		<label for="newuser">Username</label>
		<input type="text" class="form-control" id="newuser" placeholder="username">
		</div>
		<div class="form-group">
		<label for="pwd">Password</label>
		<input type="password" class="form-control" id="pwd" placeholder="password">
		</div>
		<div class="form-group">
		<input type="button" class="btn btn-default" id="pwdgen" value="Generate password">
		<span id="pwdgenval" style="margin-left:10px;"></span>
		</div>
		<div>
		  <div class="checkbox">
		    <label>
		      <input type="checkbox" id="userstatus" value="1" checked>
		      User enabled
		    </label>
		  </div>
		</div>
		';
		$modal_body2='
		<div class="form-group">
		<label for="eugroup">Group</label>
		'.$UI::html_list($groups,'groupname',' id="esgroup" class="form-control"',$sel=2).'
		</div>		
		<div class="form-group">
		<input type="hidden" id="euid" value="0">
		<label for="euser">Username</label>
		<input type="text" class="form-control" id="euser" placeholder="username">
		</div>
		<div>
		  <div class="checkbox">
		    <label>
		      <input type="checkbox" id="echpwd" value="1">
		      Change password
		    </label>
		  </div>
		</div>
		<div id="pwdbox" class="hidden">
			<div class="form-group">
			<label for="epwd">New password</label>
			<input type="password" class="form-control" id="epwd" placeholder="new password">
			</div>
			<div class="form-group">
			<input type="button" class="btn btn-default" id="epwdgen" value="Generate password">
			<span id="epwdgenval" style="margin-left:10px;"></span>
			</div>
		</div>
		<div>
		  <div class="checkbox">
		    <label>
		      <input type="checkbox" id="euserstatus" value="1" checked>
		      User enabled
		    </label>
		  </div>
		</div>
		';
		$UI->pos['main'].=$UI::bootstrap_modal('NewUser','New user',' id="frm_NewUser"',$modal_body1,'addUser','Add');
		$UI->pos['main'].=$UI::bootstrap_modal('EditUser','Edit user',' id="frm_EditUser"',$modal_body2,'updateUser','Update');
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
				<button type="button" class="btn btn-default uedit" data-toggle="modal" data-target="#EditUser">edit</button>
				<button type="button" class="btn btn-default udel">delete</button>
			</div>
			</td>
			</tr>
			';
		}
		$UI->pos['main'].='</tbody>
		</table>';
		} else {
			$UI->pos['main'].='
			<div class="well">'.\CORE::init()->lang('norecdb','No records found in the database.').'</div>
			';
		}
	$UI->pos['js'].='<!-- users js -->
	<script type="text/javascript">
	$(document).ready(function() {

	/* GENERATOR */

	function passGen(len) {
	    var length = len,
	        charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
	        retVal = "";
	    for (var i = 0, n = charset.length; i < length; ++i) {
	        retVal += charset.charAt(Math.floor(Math.random() * n));
	    }
	    return retVal;
	}
	$("#pwdgen").click(function(){
		var xpwdg=passGen(10);
		$("#pwd").val(xpwdg);
		$("#pwdgenval").text(xpwdg);			
	});

	/* ADD */
	function IsJsonString(str) {
    	try { JSON.parse(str); } catch(e) { return false; }
    	return true;
	}
	$("#NewUser").on("shown.bs.modal", function() {
    	$("#newuser").focus();
	});
	$("#frm_NewUser").submit(function(e){
		e.preventDefault();
	});
	$("#addUser").click(function(e){
		var xgid = $("#sgroup").val();
		var xuser = $("#newuser").val();
		var xpwd = $("#pwd").val();
		var xstatus = 1;
		if(!$("#userstatus").prop("checked")){xstatus = 0;}
		// console.log("user:"+xuser+";password:"+xpwd+";gid:"+xgid+";status:"+xstatus+";");
		$.post("./?c=user&act=manage&ajax=add", {user:xuser,password:xpwd,gid:xgid,status:xstatus}, function(data){
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

	/* EDIT */

	$("#EditUser").on("shown.bs.modal", function() {
	    $("#euser").focus();
	});
	$("#frm_EditUser").submit(function(e){
		e.preventDefault();
	});
	$("#epwdgen").click(function(){
		var xepwdg=passGen(10);
		$("#epwd").val(xepwdg);
		$("#epwdgenval").text(xepwdg);
	});
	$("#echpwd").click(function(){
		if($(this).prop("checked")){
			$("#pwdbox").removeClass("hidden");
		} else {
			$("#epwd").val("");
			$("#epwdgenval").text("");
			$("#pwdbox").addClass("hidden");
		}
	});
	$("button.uedit").click(function(){
		var xuid = $(this).parent("div").attr("id");
		$.post("./?c=user&act=manage&ajax=edit", {uid:xuid}, function(data){
			$("#epwd").val("");
			$("#epwdgenval").text("");
			if(IsJsonString(data)){
				var obj = JSON.parse(data);
				$("#euid").val(obj.uid);
				$("#esgroup").val(obj.gid);
				$("#euser").val(obj.user);
				if(obj.status==1){
					$("#euserstatus").prop("checked",true);
				} else {
					$("#euserstatus").prop("checked",false);
				}
			} else {
				alert("Error. Check JS console log.");
				console.log(data);
			}
		});
	});
	$("#updateUser").click(function(e){
		var xuid = $("#euid").val();
		var xgid = $("#esgroup").val();
		var xuser = $("#euser").val();
		var xchpwd = 0;
		var xpwd = $("#epwd").val();
		var xstatus = 1;
		if($("#echpwd").prop("checked")){ xchpwd=1; }
		if(!$("#euserstatus").prop("checked")){ xstatus=0; }
		// console.log("uid:"+xuid+";user:"+xuser+";gid:"+xgid+"chpwd:"+xchpwd+";password:"+xpwd+";status:"+xstatus+";");
		$.post("./?c=user&act=manage&ajax=update", {uid:xuid,gid:xgid,user:xuser,chpwd:xchpwd,password:xpwd,status:xstatus}, function(data){
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

	/* DELETE */

	$("button.udel").click(function(){
		var xuid = $(this).parent("div").attr("id");
		if(confirm("Delete this user?")){
			$.post("./?c=user&act=manage&ajax=del", {uid:xuid}, function(data){
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
			<button type="button" class="btn btn-default gedit" data-toggle="modal" data-target="#EditGroup">edit</button>
			<button type="button" class="btn btn-default gdel">delete</button>
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
$(document).ready(function(){
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
	$("button.gedit").click(function(){
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

	$("button.gdel").click(function(){
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

});
</script>';
}

}
