<?php
namespace CORE\MVC\V;

class USER_V {

public static function user_menu(){
	$UI=\CORE\BC\UI::init();
	$USER=\CORE\BC\USER::init();
	if($USER->auth()){
		if($USER->get('gid')==1){
			$UI->pos['user2'].='<li class="dropdown">
			  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">
			  Administration 
			  <span class="caret"></span></a>
			  <ul class="dropdown-menu" role="menu">
			    <li><a href="./?c=user&act=manage">Manage users</a></li>
			    <li><a href="./?c=user&act=groups">Manage groups</a></li>
			  </ul>
			</li>
			<!--<li><a href="#about">About</a></li>-->';
		}
	/*
	$UI->pos['user1']='<form class="navbar-form">
		<a href="./?c=user&act=logout" class="btn btn-success">Sign out</a>
	</form>';
	*/
	$UI->pos['user1']='<form class="navbar-form">
		<div class="dropdown">
		  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
		    '.$USER->get('username').'
		    <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
		    <li role="usermenu">
		    	<a role="menuitem" tabindex="-1" href="./?c=user&act=profile">
		    		<small><i class="glyphicon glyphicon-user"></i>&nbsp;</small> <span class="text">Profile</span>
		    	</a>
		    </li>
		    <!--
		    <li role="usermenu">
		    	<a role="menuitem" tabindex="-1" href="#">
		    		<small><i class="glyphicon glyphicon-cog"></i>&nbsp;</small> <span class="text">Settings</span>
		    	</a>
		    </li>
		    -->
		    <li role="usermenu" class="divider"></li>
		    <li role="usermenu">
		    	<a role="menuitem" tabindex="-1" href="./?c=user&act=logout">
		    		<small><i class="glyphicon glyphicon-off"></i>&nbsp;</small> <span class="text">Sign out</span>
		    	</a>
		    </li>
		  </ul>
		</div>
	</form>';
	$UI->pos['user2'].='<li class="dropdown">
	  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">
	  Modules 
	  <span class="caret"></span></a>
	  <ul class="dropdown-menu" role="menu">
	    <li><a href="./?c=tb">Telephone billing</a></li>
	    <li><a href="./?c=es">Events schedule</a></li>
	  </ul>
	</li>';
	} else {
	$UI->pos['user1']='<form action="./?c=user&act=login" method="post" class="navbar-form">
	    <div class="form-group">
	      <input type="text" name="login" placeholder="Login" class="form-control">
	    </div>
	    <div class="form-group">
	      <input type="password" name="password" placeholder="Password" class="form-control">
	    </div>
	    <button type="submit" class="btn btn-success">Sign in</button>
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
		//\CORE::init()->msg('debug','Managing user groups');
		\CORE::init()->test('Groups...');
	}
}

}