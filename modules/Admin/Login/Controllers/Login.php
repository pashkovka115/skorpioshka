<?php

namespace Modules\Admin\Login\Controllers;


use Core\Exceptions\LoginException;
use Core\Settings\Base;
use Modules\CommonModels\User;
use Modules\Module;
use Extensions\Sessions\Session;


class Login extends Module
{
    protected $admin_redirectTo = 'admin';
    protected $user_redirectTo = '';


    public function createAdmin()
    {
        if (Session::statusAuth()){
            redirect($this->admin_redirectTo);
        }
        return view('admin/auth/login');
    }

    public function createUser()
    {
        return '<h1>Опсс...</h1>';
    }


    public function checkAdmin()
    {
        $data = $this->reguest()->getAllInputs();
        $v = $this->validator($data);
        $v->rules([
            'required' => ['email', 'password'],
            'auth'     => ['email:password']
        ]);

        if ($v->validate()) {
            if (isset($data['remember']) and $data['remember'] == 'on'){
                $remember = true;
            }else{
                $remember = false;
            }

            $login = $this->loginer();
            $login->setRedirectTo($this->admin_redirectTo);
            $login->refresh_memory();
            $login->login($data['email'], $data['password'], $remember);


        } else {
            Session::setError($v->getErrors());
            back(true);
        }

    }
}




















