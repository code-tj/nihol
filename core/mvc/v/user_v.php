<?php
namespace CORE\MVC\V;

class USER_V {

public static function user_menu(){
	$UI=\CORE\BC\UI::init();
	$USER=\CORE\BC\USER::init();
	if($USER->auth()){
	/*
	$UI->pos['user1']='<form class="navbar-form">
		<a href="./?c=user&act=logout" class="btn btn-success">'.\CORE::init()->lang('signout','Sign out').'</a>
	</form>';
	*/
	$UI->pos['user1'].='<form class="navbar-form">
		<div class="dropdown">
		  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
		    '.$USER->get('username').'
		    <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
		    <li role="usermenu">
		    	<a role="menuitem" tabindex="-1" href="./?c=user&act=profile">
		    		<small><i class="glyphicon glyphicon-user"></i>&nbsp;</small> <span class="text">'.\CORE::init()->lang('profile','Profile').'</span>
		    	</a>
		    </li>
		    <!--
		    <li role="usermenu">
		    	<a role="menuitem" tabindex="-1" href="#">
		    		<small><i class="glyphicon glyphicon-cog"></i>&nbsp;</small> <span class="text">'.\CORE::init()->lang('settings','Settings').'</span>
		    	</a>
		    </li>
		    -->
		    <li role="usermenu" class="divider"></li>
		    <li role="usermenu">
		    	<a role="menuitem" tabindex="-1" href="./?c=user&act=logout">
		    		<small><i class="glyphicon glyphicon-off"></i>&nbsp;</small> <span class="text">'.\CORE::init()->lang('logout','Logout').'</span>
		    	</a>
		    </li>
		  </ul>
		</div>
	</form>';
	} else {
	$UI->pos['user1'].='<form action="./?c=user&act=login" method="post" class="navbar-form">
		<div class="form-group">
			<div class="dropdown">
			  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-expanded="true">
			    <i></i>&nbsp;<small>Язык</small>
			    <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu2">
					<li role="usermenu">
						<a role="menuitem" tabindex="-1" href="./?lang=ru">
							<i class="langflag langflag-ru"></i>&nbsp;<small>Русский</small>
						</a>
					</li>
					<li role="usermenu">
			    	<a role="menuitem" tabindex="-1" href="./?lang=tj">
			    		<i class="langflag langflag-tj"></i>&nbsp;<small>Тоҷикӣ</small>
			    	</a>
			    </li>
			  </ul>
			</div>
		</div>
	    <div class="form-group">
	      <input type="text" name="login" placeholder="'.\CORE::init()->lang('login','Login').'" class="form-control">
	    </div>
	    <div class="form-group">
	      <input type="password" name="password" placeholder="'.\CORE::init()->lang('password','Password').'" class="form-control">
	    </div>
	    <button type="submit" class="btn btn-success">'.\CORE::init()->lang('login','login').'</button>
	  </form>
	';
	}

}

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
