<?php

namespace Modules\Register\Controllers;


use Extensions\Sessions\Session;
use Modules\CommonModels\User;
use Modules\Login\Controllers\Login;
use Modules\Module;
use Valitron\Validator;


class Register extends Module
{
    /**
     * @var string
     *  Перенаправление пользователя после регистрации
     */
    private $admin_redirectTo = '/admin';


    public function createAdmin()
    {
        if (Session::statusAuth()) {
            redirect($this->admin_redirectTo, 301);
        }
        return view('admin/auth/register');
    }


    public function storeAdmin()
    {
        $data = $this->reguest()->getAllInputs();
        $v = $this->validator($data);
        $v->rules([
            'min_length' => ['name:3', 'email:6', 'password:6'],
            'max_length' => ['name:32', 'email:50'],
            'email'      => ['email'],
            'equals'     => ['password:password_confirm'],
            'confirmed'  => ['confirm'],
            'required'   => ['name', 'email', 'password', 'password_confirm'],
            'unique'     => ['email']
        ]);

        if ($v->validate()) {

            $register = $this->register();
            $register->setRedirectTo($this->admin_redirectTo);
            set_info('Вы успешно зарегестрированы');
            $register->registration($data);
        } else {
            set_info('Что то пошло не так, попробуйте ещё раз');
            back();
        }
    }
}