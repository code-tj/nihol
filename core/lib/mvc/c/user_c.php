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
            \my::data($this->view->loginForm(['title'=>BRAND,'remember'=>true,'forgot'=>false]));
          }
          break;

        case 'logout':
          $this->model = new \mvc\m\user_m();
          $this->model->logout();
          break;

        case 'profile':
          if(!\my::user()->isGuest())
          {
            $this->model = new \mvc\m\profiles_m();
            $this->view = new \mvc\v\profiles_v();
            \my::data($this->view->profile($this->model->read(\my::user()->get('pid'))));
          }
          break;

        case 'forgot': // -> assistance
          $this->model = new \mvc\m\user_m();
          $this->view = new \mvc\v\user_v();
          \my::data($this->view->iforgot_form($this->model));
          break;

        default:
          # code...
          break;
      }
    }

}
