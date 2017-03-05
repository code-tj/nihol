<?php
namespace mvc\c;
class p extends \mvc\controller
{

    public function action()
    {
        $this->app->ui->set($this->static_page($this->action));
    }

    public function static_page($alias)
    {
        $result='';
        if($alias=='')
        {
            if($this->app->user->isGuest())
            {
                $alias='guest';
            } else {
                $alias='user';
            }
        }
        // defining file path
        $path='./app/pages/'.$alias.'.html';
        if(is_readable($path))
        {
            $result=file_get_contents($path);
        } else {
            $this->app::log('err','Page not found'); // needs translation
        }
        return $result;
    }

}