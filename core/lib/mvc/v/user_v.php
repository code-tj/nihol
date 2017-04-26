<?php
namespace mvc\v;

class user_v
{

    public function loginForm()
    {
      ///\app::init()->data->set('<script type="text/javascript" src="./ui/js/user.js"></script>','js');
        return '
<div class="col-md-4"></div>

<div class="col-md-3">
<form id="LoginForm" action="./?c=user&act=login" method="post">
  <div class="input-group" style="margin-top:20px;margin-bottom:20px;">
      <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i>
      </span>
      <input type="text" class="form-control" id="n_username" name="n_username" value="" placeholder="username" style="font-size:120%;">
  </div>
  <div class="input-group" style="margin-bottom:10px;">
      <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i>
      </span>
      <input type="password" class="form-control" id="n_password" name="n_password" value="" placeholder="password" style="font-size:120%;">
  </div>
  <div class="input-group" style="margin-bottom:10px;">
    <div class="checkbox">
      <label class="text-muted">
        <input type="checkbox" id="remember" name="remember" value="1">
        Remember
      </label>
    </div>
  </div>
  <div class="input-group" style="margin-bottom:10px;">
    <small class="text-muted">If forgot your password
    <a id="iforgot" href="./?c=user&act=restore">click here</a>.</small>
  </div>
  <div class="text-right">
    <input id="btn_login" type="submit" class="btn btn-success" style="font-size:120%;" value=" Login ">
  </div>
</form>
</div>

<div class="col-md-4"></div>
';
    }

}
