<?php
namespace mvc\c;

class page_c extends \controller

{

    public function action($act='')
    {
      $app=\app::init();
      if($act=='')
      {
        $this->homepage();
      } else {
        $app->data($this->page($act));
      }

    }

    public function homepage()
    {
      $app=\app::init();
      $user=$app->user;
      if(!$user->isGuest())
      {
        $path="./app/pages/user.php";
        if($user->isAdmin()){$path="./app/pages/admin.php";}
        if(is_readable($path)){
          include($path);
        } else {
          $app->log('err','Page not found.');
        }
      } else {
        $app->data($this->page('guest'));
      }
    }

    public function page($alias)
    {
      $result=''; $path='';
      if(preg_match('/^[a-zA-Z0-9_]+$/',$alias))
      {
        $path=APP.'pages/'.$alias.'.php';
        if(is_readable($path))
        {
            $result=file_get_contents($path);
        } else {
            $app=\app::init();
            $app->log('err','Page not found'); // ... translation
        }
      } else {
        $app=\app::init();
        $app->log('err','Incorrect page alias'); // ... translation
      }
      return $result;
    }

}
