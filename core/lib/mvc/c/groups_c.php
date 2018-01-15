<?php
namespace mvc\c;

class groups_c extends \controller
{

  public function action($act="")
  {
    switch ($act) {
      default:
        $this->model = new \mvc\m\groups_m;
        $this->view = new \mvc\v\groups_v;
        \my::data($this->view->main($this->model));
        break;
    }
  }

}
