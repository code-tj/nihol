<?php
namespace mvc\c;

class users_c extends \controller

{

public function action($act="")
{
  switch ($act) {
    case 'add':
      # code...
      break;

    default:
      $this->model = new \mvc\m\users_m();
      $this->view = new \mvc\v\users_v();
      $this->view->list($this->model->get_users(true));
      break;
  }
}

}
