<?php
namespace mvc\c;

class user_c extends \controller
{

    public function action($act='')
    {
      switch ($act) {
        case 'login':
          if(isset($_POST['n_username']) && isset($_POST['n_password']))
          {
            $remember=false;
            if(isset($_POST['remember'])) {$remember=true;}
            $this->model = new \mvc\m\user_m();
            $this->model->login($_POST['n_username'],$_POST['n_password'],$remember);
          } else {
            $this->view = new \mvc\v\user_v();
            $this->app->data->set($this->view->loginForm());
          }
          break;

        case 'logout':
          $this->model = new \mvc\m\user_m();
          $this->model->logout();
          break;

        default:
          # code...
          break;
      }
    }

    public function login()
    {

    }

    public function logout()
    {

    }



}
