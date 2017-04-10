<?php
namespace mvc\c;
class home_c extends \mvc\controller
{

    public function action()
    {
        //\app::init()->ui->set($this->static_page($this->action));
    }

    public function static_page($alias)
    {
        $result='';
        if($alias=='')
        {
            if(\app::init()->user->isGuest())
            {
                $alias='guest';
            } else {
                $alias='user';
            }
        }
        // defining file path
        $path=APP.'pages/'.$alias.'.php';
        if(is_readable($path))
        {
            $result=file_get_contents($path);
        } else {
            \app::log('err','Page not found'); // needs translation
        }
        return $result;
    }

}
