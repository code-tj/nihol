<?php
namespace CORE\MVC\V;

class USER_V {

public function __construct(){
	
}

public static function user_menu(){
	$UI=\CORE\BC\UI::init();
	$USER=\CORE\BC\USER::init();
	if($USER->auth()){
	/*
	$UI->pos['user1']='<form class="navbar-form">
		<a href="./?c=user&act=logout" class="btn btn-warning">Sign out</a>
	  </form>';
	*/
	$UI->pos['user1']='<form class="navbar-form">
		<div class="dropdown">
		  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
		    Username
		    <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
		    <li role="usermenu">
		    	<a role="menuitem" tabindex="-1" href="#">
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

}