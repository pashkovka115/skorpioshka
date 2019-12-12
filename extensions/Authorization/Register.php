<?php


namespace Extensions\Authorization;


use Core\Exceptions\AuthException;
use Core\Settings\Base;
use Core\System\Traits\Singleton;
use Extensions\Sessions\Session;

class Register
{
    private $redirectTo = '/';

    use Singleton;


    public function registration(array $data, $remember = false)
    {
        try {
            $user_class = Base::get('user');
            $login_field = Base::get('user_login');
            $password_field = Base::get('user_password');

            if (!isset($data[$password_field]) or !isset($data[$login_field])){
                throw new AuthException('Переданы не корректные поля формы' . __FILE__ . __LINE__, 404);
            }

            $user = $user_class::where($login_field, '=', $data[$login_field])->first();

            $data_password = $data[$password_field];
            $data[$password_field] = password_hash($data[$password_field], PASSWORD_DEFAULT);
            $data['remember_token'] = str_random(30);

            if (is_null($user) or $user === false){
                $u = $user_class::create($data);
                set_info('Вы зарегестрированы');
                if (isset($u->$login_field) and isset($u->$password_field)){
                    $loginer = Loginer::getInstance();
                    $loginer->setRedirectTo($this->redirectTo);
                    $loginer->login($data[$login_field], $data_password, $remember);
                }

            }else{
                set_error('Опсс... этот email уже занят');
                back();
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new AuthException($e->getMessage() . __FILE__ . __LINE__, 404);
        }
    }

    /**
     * @param string $redirectTo
     */
    public function setRedirectTo(string $redirectTo): void
    {
        $this->redirectTo = $redirectTo;
    }
}