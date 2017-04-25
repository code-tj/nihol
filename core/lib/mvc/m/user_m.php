<?php
namespace mvc\m;

class user_m extends \model
{

    public function login($login='',$pwd='',$remember=false)
    {
      $this->app->data->set("login: $login, pwd: $pwd, rem: $remember.");
      $login=strtolower(trim($login)); $pwd=trim($pwd);
      if($login!='' && $pwd!='')
      {
        if($this->valid('login',$login) && $this->valid('pwd',$pwd))
        {
          // db ...

        } else {
          $this->app->log->set('err','Login or password is not valid.');
        }
      } else {
        $this->app->log->set('err','Login and password are empty.');
      }
    }

    public function logout()
    {

    }

    public function valid($type,$value)
    {
      $valid=false;
      $len=strlen($value);
      // validation ...
      switch ($type) {
        case 'login':
          if($this->app::regex($value,'/^[a-z0-9]+$/') && ($len>=3 && $len<128))
          {
            $valid=true;
          }
          break;
        case 'pwd':
          if($len>=8 && $len<256)
          {
            $valid=true;
          }
          break;
      }
      return $valid;
    }

}
