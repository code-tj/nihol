<?php
namespace CORE\MVC\V;

class GROUP_V {


public function main($model){
	$result='';
	$groups=$model->get_groups();
	$counter=count($groups);
	$UI=\CORE\BC\UI::init();
	$result.='<div>
	<h4>Groups: <span class="badge">'.$counter.'</span>&nbsp;
	'.$UI::bootstrap_modal_btn('ShowNewGroup','NewGroup','New group').'
	</h4>
</div>
';
	$modal_body_new='<div class="form-group">
	<label for="new_group">Group name</label>
	<input type="text" class="form-control" id="new_group" placeholder="">
</div>
';
	$result.=$UI::bootstrap_modal('NewGroup','New group',' id="frm_NewGroup"',$modal_body_new,'addGroup','Add');
	$modal_body_edit='<div class="form-group">
	<input type="hidden" id="gid" value="0">
	<label for="edit_group">Group name</label>
	<input type="text" class="form-control" id="edit_group" placeholder="">
</div>
';
	$result.=$UI::bootstrap_modal('EditGroup','Edit group',' id="frm_EditGroup"',$modal_body_edit,'updateGroup','Update');
	if($counter>0){
$result.='
<table class="table table-bordered table-hover" style="width:auto;">
<thead>
<tr>
<th>#</th>
<th>Group</th>
<th class="text-center">ACTION</th>
</tr>
</thead>
<tbody>
';
		$cnt=0;
		foreach ($groups as $gid => $group) {
			$cnt++;
$result.='
<tr>
	<td>'.$cnt.'</td>
	<td>'.$group.'</td>
	<td>
	<div id="'.$gid.'" class="btn-group btn-group-xs">
		<button type="button" class="btn btn-default group_edit" data-toggle="modal" data-target="#EditGroup">edit</button>
		<button type="button" class="btn btn-default group_del">delete</button>
	</div>
	</td>
</tr>
';
		}
$result.='</tbody></table>';
	} else {
		$result.='<div class="well">'.\CORE::init()->lang('norecdb','No records found in the database.').'</div>';
	}
	$UI->pos['js'].='
<script type="text/javascript">
	$(document).ready(function(){

		// ADD

		function IsJsonString(str) {
	    	try { JSON.parse(str); } catch(e) { return false; }
	    	return true;
		}

		$("#NewGroup").on("shown.bs.modal", function() {
		    $("#new_group").focus();
		});

		$("#frm_NewGroup").submit(function(e){
			e.preventDefault();
		});

		$("#addGroup").click(function(){
			var new_group = $("#new_group").val();
			$.post("./?c=group&act=add&ajax=add", {newgroup:new_group}, function(data){
				if(data=="Group successfully added."){
					location.reload();
				} else {
					alert("Error. Check JS console log.");
					console.log(data);
				}
			});
		});

		// EDIT
		
		$("#EditGroup").on("shown.bs.modal", function() {
		    $("#edit_group").focus();
		});

		$("#frm_EditGroup").submit(function(e){
			e.preventDefault();
		});

		$("button.group_edit").click(function(){
			var xgid = $(this).parent("div").attr("id");
			$.post("./?c=group&act=edit&ajax", {gid:xgid}, function(data){
				if(IsJsonString(data)){
					var obj = JSON.parse(data);
					$("#gid").val(xgid);
					$("#edit_group").val(obj.group);					
				} else {
					alert("Error. Check JS console log.");
					console.log(data);
				}
			});
		});

		// UPDATE

		$("#updateGroup").click(function(){
			var edit_gid = $("#gid").val();
			var edit_group = $("#edit_group").val();
			$.post("./?c=group&act=update&ajax", {gid:edit_gid,group:edit_group}, function(data){
				if(data=="Group successfully updated."){
					location.reload();
				} else {
					alert("The operation failed. Check JS console log.");
					console.log(data);
				}
			});
		});

		// DEL

		$("button.group_del").click(function(){
			var del_gid = $(this).parent("div").attr("id");
			if(confirm("Delete this group?")){
				$.post("./?c=group&act=del&ajax", {gid:del_gid}, function(data){
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
</script>	
';
	return $result;
}

}
