<?php
namespace mvc\c;

class page_c extends \controller
{

    public function action($act='')
    {
      if($act=='')
      {
        $this->app->data->set($this->homepage());
      } else {
        $this->app->data->set($this->page($act));
      }

    }

    public function homepage()
    {
      $alias='guest';
      if(!$this->app->user->isGuest())
      {
          $alias='user';
      }
      return $this->page($alias);
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
            $this->app->log->set('err','Page not found'); // ... translation
        }
      } else {
        $this->app->log->set('err','Incorrect page alias'); // ... translation
      }
      return $result;
    }

}
