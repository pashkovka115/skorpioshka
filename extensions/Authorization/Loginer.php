<?php

namespace Extensions\Authorization;


use Core\Exceptions\LoginException;
use Core\Request;
use Core\Response;
use Core\Settings\Base;
use Core\System\Traits\Singleton;
use Extensions\Sessions\Session;

class Loginer
{
    private $redirectTo = '/';

    use Singleton;


    /**
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @throws LoginException
     * кука "u" - данные пользователя
     * Авторизует пользователя. (записывает в сессию id пользователя и статус auth = true)
     * если есть такой пользователь
     */
    public function login(string $email, string $password, bool $remember = false):void
    {
        if ($email == '' or $password == '') {
            throw new LoginException('Переданы не все данные для авторизации пользователя', 403);
        }

        $user_class = Base::get('user');
        $login_field = Base::get('user_login');
        $password_field = Base::get('user_password');

        $user = $user_class::where($login_field, '=', $email)->first()->getAttributes();

        if (!is_null($user) and password_verify($password, $user[$password_field])){
            if ($remember) {
                $a = [
                    'id'       => $user['id'],
                    'remember' => Base::get('remember'),
                    'agent' => md5($_SERVER['HTTP_USER_AGENT'])
                ];
                Response::getInstance()->setCookie('u', serialize($a), Base::get('remember') + time());
            }

            Session::setUser(['id' => $user['id']]);
            Session::setAuth(true);
            redirect($this->redirectTo);
        }else{
            set_info('Неправильная пара логин/пароль');
            back();
        }
    }

    /**
     * @throws LoginException
     * Востанавливает информацию о пользователе если пришла соответствующая кука от него
     * и авторизует его
     */
    public function refresh_memory():void
    {
        $request = Request::getInstance();

        if ($request->hasCookie('u')){
            $cookie = @unserialize($request->getCookie('u'), ['allowed_classes' => false]);
            if ($cookie and
                isset($cookie['id']) and
                isset($cookie['remember']) and
                isset($cookie['agent']) and
                $cookie['remember'] < time() and
                $cookie['agent'] == md5($_SERVER['HTTP_USER_AGENT'])
            ){
                $user_class = Base::get('user');
                $login_field = Base::get('user_login');
                $password_field = Base::get('user_password');

                $user = $user_class::where('id', '=', $cookie['id'])->first([$login_field, $password_field]);
                if (!is_null($user) and isset($user->$login_field) and isset($user->$password_field)){
                    $this->login($user->$login_field, $user->$password_field);
                }
            }
        }
    }

    /**
     * Забыть пользователя. (при новом посещении сайта пользователю необходимо будет авторизоваться)
     * Удаляет авторизационную куку из браузера пользователя.
     */
    public function forget()
    {
        Response::getInstance()->setCookie('u');
    }

    /**
     * @param string $redirectTo
     * Установить перенаправление после авторизации.
     * Возможно перенаправление в зависимости: известный пользователь или нет.
     * $this->loginer()->setRedirectTo('admin/page')
     * $this->loginer()->refresh_memory()
     * $this->loginer()->setRedirectTo('houme/page')
     * $this->loginer()->login($email, $password)
     */
    public function setRedirectTo(string $redirectTo): void
    {
        $this->redirectTo = $redirectTo;
    }
}