<?php
namespace mvc\c;

class user_c extends \controller
{

    public function action($act='')
    {
      $app=\app::init();
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
            $app->data($this->view->loginForm(['title'=>BRAND,'remember'=>true,'forgot'=>true]));
          }
          break;

        case 'logout':
          $this->model = new \mvc\m\user_m();
          $this->model->logout();
          break;

        case 'profile':
          if(!$app->user->isGuest())
          {
            $this->model = new \mvc\m\profile_m();
            $this->view = new \mvc\v\profile_v();
            $app->data($this->view->show($this->model));
          } else {
            $app->log('err','You are not authorized');
          }
          break;

        case 'forgot':
          $this->model = new \mvc\m\user_m();
          $this->view = new \mvc\v\user_v();
          $app->data($this->view->iforgot_form($this->model));
          break;

        default:
          # code...
          break;
      }
    }

}
