<?php
namespace Modules\Admin\Logout\Controllers;


use Modules\Module;
use Extensions\Sessions\Session;


class Logout extends Module
{
    protected $redirectTo = '/';


    public function logout()
    {
        Session::destroy();
        redirect($this->redirectTo);
    }
}