<?php
namespace mvc\v;

class user_v extends \view
{

    public static function loginForm($opt=[])
    {
      if(isset($_COOKIE[AL.'_lu']))
      {
        $last_username=htmlspecialchars(base64_decode(strrev(base64_decode($_COOKIE[AL.'_lu']))));
        $user_autofocus='';
        $pwd_autofocus=' autofocus';
      } else {
        $last_username='';
        $user_autofocus=' autofocus';
        $pwd_autofocus='';
      }
      $frm_title='Please login';
      if(isset($opt['title']))$frm_title=$opt['title'];
        $result='
<div class="col-md-4"></div>
  <div class="col-md-4" style="padding:10px 20px;">
  <div style="width:320px;margin:auto;">
  <h3 class="form-signin-heading">'.$frm_title.'</h3>
  <form id="LoginForm" action="./?c=user&act=login" method="post">
  <div class="input-group" style="margin-top:20px;margin-bottom:14px;">
      <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i>
      </span>
      <input type="text" class="form-control" id="n_username" name="n_username" value="'.$last_username.'" placeholder="username"'.$user_autofocus.' style="font-size:120%;">
  </div>
  <div class="input-group" style="margin-bottom:7px;">
      <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i>
      </span>
      <input type="password" class="form-control" id="n_password" name="n_password" value="" placeholder="password"'.$pwd_autofocus.' style="font-size:120%;">
  </div>';
  if(isset($opt['forgot']) && $opt['forgot'])
  {
  $result.='
  <div class="col-md-6" style="padding-left:0px;margin-top:14px;">
    <small><a href="./?c=user&act=forgot">Forgot your password?</a></small>
  </div>';
} else {
  $result.='
  <div class="col-md-6" style="padding-left:0px;margin-top:14px;"></div>';
}
  $result.='
  <div class="col-md-6" style="padding-right:0px;text-align:right;margin-top:14px;">
      <input id="btn_login" type="submit" class="btn btn-primary" style="font-size:120%;" value="Login">
  </div>
</form>
</div>
</div>
';
    return $result;
    }

  public function iforgot_form($model,$cp_alias='cp3')
  {
    $result='';
    $show_form=true;
    if(isset($_GET['link']))
    {
      $link=trim($_GET['link']);
      if($model->iforgot_link($link))
      {
        // show reset form with hash
        $show_form=false;
        $reseted=false;
        if(isset($_POST['pwd']))
        {
          $reseted=$model->iforgot_passwd();
        }
          if(!$reseted) $result.=$this->pwd_reset_form($link);
      }
    }
    $user=\my::user();
    $user->session_start();
    if(isset($_POST['f_user']) && isset($_POST['f_vcode']))
    {
      $test=$model->iforgot($_POST['f_user'],$_POST['f_vcode'],$cp_alias);
    }
    if($show_form)
    {
      $captcha_path=APP."/lib/ext/captcha/simple-php-captcha.php";
      if(is_readable($captcha_path)) {
      	include($captcha_path);
      	$_SESSION = array();
        $_SESSION[$cp_alias] = simple_php_captcha();
      } else {
      	$app->log('error','Some problems with external libs');
      }
      $result.='
<div class="row">
<div class="col-md-4"></div>
<div class="col-md-4 text-center">
<h3>Reset your password:</h3><br>';
      $result.='<form action="./?c=user&act=forgot" method="POST">
  <div style="width:320px;margin:auto;">
  <div class="input-group" style="margin-bottom:20px;">
    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
    <input type="text" class="form-control" id="f_user" name="f_user" value="" placeholder="username" style="font-size:120%;" autofocus>
  </div>
  <div class="text-center" style="margin-bottom:20px;">
    <img src="'.$_SESSION[$cp_alias]['image_src'].'" alt="verification code">
  </div>
  <div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-arrow-right"></i></span>
    <input type="text" class="form-control" id="f_vcode" name="f_vcode" value="" placeholder="verification code" style="font-size:120%;">
  </div>
  <div style="margin-top:20px;text-align:center;">
    <input id="btn_forgot" type="submit" class="btn btn-primary" value=" Email me ">
  </div>
  </div>
</form>
</div>
<div class="col-md-4"></div>
</div>';
    }
    return $result;
  }

  public function pwd_reset_form($hash='')
  {
    $result='
<div class="row">
  <div class="col-md-4"></div>
  <div class="col-md-4">
    <div style="padding:0px 40px;">
    	<h3 class="text-center">Change your password:</h3>
    	<br>
    	<form id="frm_passwd" action="#reset" method="post">
    	<div class="form-group">
    		<label for="pwd">New password</label>
    		<input type="password" class="form-control" id="pwd" name="pwd" placeholder="new password" autofocus>
    	</div>
    	<div class="form-group">
    		<label for="pwd2">Retype new password</label>
    		<input type="password" class="form-control" id="pwd2" name="pwd2" placeholder="confirm new password">
    	</div>
    	<div class="form-group text-center">';
      if($hash!='') $result.='<input type="hidden" name="link" id="link" value="'.$hash.'">';
      $result.='
        <br>
    		<input type="submit" id="passwd" class="btn btn-danger" value="Change password">
    	</div>
    	</form>
    </div>
  </div>
  <div class="col-md-4"></div>
</div>
';
return $result;
  }

}
