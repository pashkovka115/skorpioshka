<?php

namespace Extensions\Sessions;

// <meta http-equiv="refresh" content="5">
use Core\Response;
use Core\Settings\Base;
use Core\System\Traits\Singleton;

class Session
{
    use Singleton;

    public static function setUser($user)
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        if (is_array($user)) {
            $_SESSION['user'] = $user;
        }
    }

    public static function getUser($key = '')
    {
        if ($key == '') {
            return !isset($_SESSION['user']) ? false : $_SESSION['user'];
        }
        if (isset($_SESSION['user'][$key])) {
            return $_SESSION['user'][$key];
        }
        return false;
    }


    public static function statusAuth()
    {
        if (isset($_SESSION['auth'])){
            return $_SESSION['auth'];
        }
        return false;
    }

    public static function setAuth(bool $bool)
    {
        if (isset($_SESSION['auth'])) {
            unset($_SESSION['auth']);
        }
        $_SESSION['auth'] = $bool;
    }


    public static function destroy()
    {
        Response::sessionDestroy();
    }

    public static function setLife($secons)
    {
        if (isset($_SESSION['life'])) {
            unset($_SESSION['life']);
        }
        $_SESSION['life'] = $secons;
    }

    public static function getLife()
    {
        if (isset($_SESSION['life'])) {
            return $_SESSION['life'];
        }
        return 0;
    }

    /**
     * Проверяет время жизни сессии
     */
    public static function checkSession(){
        $b_life = Base::get('cookieLifetime');
        if (!isset($_SESSION['life'])) {
            $_SESSION['life'] = time() + $b_life;
        }elseif ($_SESSION['life'] < time()){
            Response::getInstance()->setCookie(session_name());
//            self::destroy();
            session_regenerate_id(true);
            session_destroy();
        }else{
            $_SESSION['life'] = time() + $b_life;
        }
    }

    public static function getReferrer()
    {
        if (isset($_SESSION['prev_url'])) {
            return $_SESSION['prev_url'];
        }
        return '';
    }

    public static function referrer()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            if (isset($_SESSION['current_url']) and $_SERVER['REQUEST_URI'] != $_SESSION['current_url']) {
                $_SESSION['prev_url'] = $_SESSION['current_url'];
            }
            $_SESSION['current_url'] = $_SERVER['REQUEST_URI'];
        }
    }

    public static function referrer_clear()
    {
        if (isset($_SESSION['prev_url'])){
            unset($_SESSION['prev_url']);
        }
    }


    public static function setError($error)
    {
        if (!isset($_SESSION['error'])) {
            $_SESSION['error'] = [];
        }
        if (is_array($error)){
            foreach ($error as $item){
                $_SESSION['error'][] = $item;
            }
        }else{
            $_SESSION['error'][] = $error;
        }

    }


    public static function getError()
    {
        if (isset($_SESSION['error'])) {
            return $_SESSION['error'];
        }
        return [];
    }


    public static function hasError()
    {
        if (isset($_SESSION['error'])) {
            return 0 < count($_SESSION['error']);
        }
        return false;
    }

    public static function destroyError()
    {
        $_SESSION['error'] = [];
    }


    public static function setInfo($info)
    {
        if (!isset($_SESSION['info'])) {
            $_SESSION['info'] = [];
        }
        if (is_array($info)){
            foreach ($info as $item){
                $_SESSION['info'][] = $item;
            }
        }else{
            $_SESSION['info'][] = $info;
        }
    }


    public static function getInfo()
    {
        if (isset($_SESSION['info'])) {
            return $_SESSION['info'];
        }
        return [];
    }


    public static function hasInfo()
    {
        if (isset($_SESSION['info'])) {
            return 0 < count($_SESSION['info']);
        }
        return false;
    }


    public static function destroyInfo()
    {
        $_SESSION['info'] = [];
    }

    /**
     * @param $data
     * Данные вводимые пользователем в форму
     */
    public static function setInputDate($data)
    {
        if (isset($_SESSION['data_forms'])) {
            unset($_SESSION['data_forms']);
        }
//        var_dump($data);
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $_SESSION['data_forms'][$k] = $v;
            }
        } else {
            $_SESSION['data_forms'] = $data;
        }
    }

    public static function getInputDate($key = false)
    {
//        dump($_SESSION['data_forms']);
        if (!isset($_SESSION['data_forms'])) {
//            $_SESSION['data_forms'] = '';
            return '';
        }
        if ($key and isset($_SESSION['data_forms'][$key]))
            return $_SESSION['data_forms'][$key];

        return $_SESSION['data_forms'];
    }

    public static function destroyInputDate()
    {
        if (isset($_SESSION['data_forms'])) {
            unset($_SESSION['data_forms']);
        }
    }
}






















