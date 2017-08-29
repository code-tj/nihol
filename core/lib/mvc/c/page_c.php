<?php
namespace mvc\c;

class page_c extends \controller
{

    public function action($act='')
    {
      if($act=='')
      {
        $this->homepage();
      } else {
        \my::data($this->page($act));
      }

    }

    public function homepage()
    {
      $user=\my::user();
      if(!$user->isGuest())
      {
        $path="./app/pages/user.php";
        if($user->isAdmin()){$path="./app/pages/admin.php";}
        if(is_readable($path)){
          include($path);
        } else {
          \my::log('err','Page not found.');
        }
      } else {
        \my::data($this->page('guest'));
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
            \my::log('err','Page not found'); // ... translation
        }
      } else {
        \my::log('err','Incorrect page alias'); // ... translation
      }
      return $result;
    }

}
