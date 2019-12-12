<?php

namespace Extensions\Authorization;


use Core\Exceptions\AuthException;
use Core\Router;
use Core\Settings\Base;
use Core\System\Traits\Singleton;
use Extensions\Sessions\Session;

class Auth
{
    protected static $redirectTo = '/';
    protected $user;
    private static $_instance;

    public static function getInstance(){
        if (self::$_instance instanceof self)
            return self::$_instance;
        return self::$_instance = new self();
    }

    private function __construct()
    {
        if (!Session::statusAuth()) {
            $this->user = null;
            return;
        }
        try {
            $this->user = Base::get('user')::findOrFail(Session::getUser('id'))->first();
            if ($this->user === null){
                throw new AuthException('Пользователь не авторизован ' . __FILE__ . __LINE__, 404);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new AuthException($e->getMessage() . __FILE__ . __LINE__, 404);
        }
    }

    private function __clone()
    {
    }


    public function user()
    {
        return $this->user;
    }

    public function id()
    {
        return $this->user->id;
    }

    public function name()
    {
        return $this->user->name;
    }

    public function email()
    {
        return $this->user->email;
    }

    public function check()
    {
        return Session::statusAuth() and Session::getUser('id');
    }

}