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
		//\CORE::init()->msg('debug','Managing users accounts');

	}
}

public function manage_groups($model){
	if($model!=null){
		$UI=\CORE\BC\UI::init();
		$groups=$model->get_groups();
		$count=count($groups);
		// test
		$UI->pos['main'].='<div class="btn-group" role="group" aria-label="...">
		  <button id="new_group" type="button" class="btn btn-default"
		  data-toggle="modal" data-target="#myModal">New</button>
		</div>'.$UI::modal();
		if($count>0){
			//... show table
		} else {
			$UI->pos['main'].='<h4 class="text-danger">No records found in the database</h4>';
		}
	}
}

}
