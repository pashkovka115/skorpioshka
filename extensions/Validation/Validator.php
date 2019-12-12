<?php


namespace Extensions\Validation;


use Core\Exceptions\ValidateException;
use Core\Settings\Base;
use Illuminate\Database\Capsule\Manager as Capsule;
use Modules\CommonModels\User;


class Validator
{
    protected $validUrlPrefixes = ['http://', 'https://', 'ftp://', 'ftps://'];
    protected $lang;
    protected $errors           = [];
    protected $rules            = [];
    protected $data             = [];
    protected $flag             = true;
    protected $prefix           = 'Поле ';


    public function __construct(array $data)
    {
        $this->lang = require LANG_PATH . '/' . LANG . '/validator.php';
        $this->data = $data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }


    public function rule($rule, array $fields)
    {
        $this->rules[$rule] = $fields;
    }

    public function rules(array $rules)
    {
        foreach ($rules as $rule => $fields) {
            $this->rules[$rule] = $fields;
        }
    }

    /**
     * @return bool
     * @throws ValidateException
     * Валидируем входные данные при помощи правил
     */
    public function validate()
    {
        foreach ($this->rules as $rule => $fields) {
            if (!is_array($fields)) {
                throw new ValidateException('Список полей должен быть массивом', 404);
            }
            foreach ($fields as $field) {
                if (method_exists($this, $rule)) {
                    $this->$rule($rule, $field);
                } else {
                    throw new ValidateException('Не известное правило - ' . $rule, 404);
                }
            }
        }
        return $this->flag;
    }

    /**
     * @param $rule
     * @param $field
     * @param bool $params
     * @return string
     * @throws ValidateException
     * Возвращает перевод сообщений об ошибках
     */
    private function lang($rule, $field, $params = false)
    {
        if (isset($this->lang['rules'][$rule])) {
            if (isset($this->lang['fields'][$field])) {
                $str = $this->prefix . $this->lang['fields'][$field] . ' - ' . sprintf($this->lang['rules'][$rule], $params);
            } else {
                $str = $this->prefix . $field . ' - ' . sprintf($this->lang['rules'][$rule], $params);
            }
        } else {
            throw new ValidateException('Не описан перевод для ' . $rule, 404);
        }
        return $str;
    }


    /**
     * @param $rule
     * @param $field
     * @throws ValidateException
     * Проверяет зарегестрирован ли пользователь
     * Зарегестрирован - true
     * Не зарегестрирован - false
     * 'auth' => ['email:password']
     */
    private function auth($rule, $field)
    {
        $sp = explode(':', $field);
        $login_field = Base::get('user_login');
        $password_field = Base::get('user_password');

        if (count($sp) < 2 or
            !isset($this->data[$login_field]) or
            !isset($this->data[$password_field]) or
            in_array('', $sp)
        ) {
            throw new ValidateException('Не понятно какие поля проверять "' . $field . '". Проверьте настройки', 404);
        }
        $login = $this->data[$login_field];
        $pass = $this->data[$password_field];

        $class = Base::get('user');
        $user = $class::where($login_field, '=', $login)->first([$login_field, $password_field]);

        if (!isset($user->$password_field) or !password_verify($pass, $user->$password_field)) {
            $this->errors[] = $this->lang($rule, $login_field);
            $this->errors[] = $this->lang($rule, $password_field);
            $this->flag = false;
        }
    }

    /**
     * @param $rule
     * @param $field
     * @throws ValidateException
     * Не зарегестрирован?
     * зарегестрирован - false
     * не зарегестрирован - true
     */
    private function not_auth($rule, $field)
    {
        $login_field = Base::get('user_login');
        $class = Base::get('user');

        if (!isset($this->data[$login_field])) {
            throw new ValidateException('Не понятно какие поля проверять "' . $field . '". Проверьте настройки', 404);
        }

        $user = $class::where($login_field, '=', $this->data[$login_field])->first([$login_field]);

        if (is_null($user) or $user === false or $user == '') {
            $this->errors[] = $this->lang($rule, $login_field);
            $this->flag = false;
        }
    }

    /**
     * @param $rule
     * @param $field
     * @throws ValidateException
     * Уникальное значение поля в талице
     * по умолчаню проверяется таблица $user в базовых настройках
     * или
     * можно передать имя таблицы по шаблону field_at_form:table_name
     */
    private function unique($rule, $field)
    {
        $sp = explode(':', $field);
        if (!isset($this->data[$sp[0]]) or in_array('', $sp)) {
            throw new ValidateException('Не понятно какое поле из какой таблицы', 404);
        }
        $f = Base::get('user_login');
        if (count($sp) == 1) {
            /**
             * @var $class User
             */
            $class = Base::get('user');
            $user = $class::where($f, '=', $this->data[$sp[0]])->first();
        } elseif (count($sp) == 2) {
            $user = Capsule::table($sp[1])->where($f, '=', $this->data[$sp[0]])->first();
        }
        if ($user !== null and (is_object($user->count()) or $user->count() > 0)) {
            $this->errors[] = $this->lang($rule, $f);
            $this->flag = false;
        }
    }


    /**
     * значения этх полей одинаково
     */
    private function equals($rule, $field)
    {
        $sp = explode(':', $field);
        if (!isset($this->data[$sp[0]]) or count($sp) < 2 or in_array('', $sp)) {
            throw new ValidateException('Не понятно какие поля сравнивать', 404);
        }
        $el1 = $this->data[$sp[0]];

        foreach ($sp as $item) {
            if (!isset($this->data[$item]) or $this->data[$item] != $el1) {
                $this->errors[] = $this->lang($rule, $item, $el1);
                $this->flag = false;
            }
        }
    }

    /**
     * Убедитесь, что поле отличается от другого поля
     */
    private function different($rule, $field)
    {
        $sp = explode(':', $field);
        if (!isset($this->data[$sp[0]]) or count($sp) < 2 or in_array('', $sp)) {
            throw new ValidateException('Не понятно какие поля сравнивать', 404);
        }
        $el1 = $this->data[$sp[0]];
        array_shift($sp);

        foreach ($sp as $item) {
            if (!isset($this->data[$item]) or $this->data[$item] == $el1) {
                $this->errors[] = $this->lang($rule, $item, $el1);
                $this->flag = false;
            }
        }
    }

    /**
     * Проверить, что поле было «принято»
     */
    private function confirmed($rule, $field)
    {
        if (!isset($this->data[$field]) or $this->data[$field] != 'on') {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Длинна строки
     */
    private function length($rule, $field)
    {
        $sp = explode(':', $field);
        if (!isset($this->data[$sp[0]]) or count($sp) < 2 or in_array('', $sp)) {
            throw new ValidateException('Не указано значение для - ' . $field, 404);
        }

        if (function_exists('mb_strlen')) {
            if (mb_strlen($this->data[$sp[0]]) != $sp[1] * 1) {
                $this->errors[] = $this->lang($rule, $sp[0], $sp[1] * 1);
                $this->flag = false;
                return;
            }
        }
        if (strlen($this->data[$sp[0]]) != $sp[1] * 1) {
            $this->errors[] = $this->lang($rule, $sp[0], $sp[1] * 1);
            $this->flag = false;
        }
    }

    /**
     * Минимальная длина строки
     */
    private function min_length($rule, $field)
    {
        $sp = explode(':', $field);
        if (!isset($this->data[$sp[0]]) or count($sp) < 2 or in_array('', $sp)) {
            throw new ValidateException('Не указано значение для - ' . $field, 404);
        }
        if (function_exists('mb_strlen')) {
            if (mb_strlen($this->data[$sp[0]]) < $sp[1] * 1) {
                $this->errors[] = $this->lang($rule, $sp[0], $sp[1] * 1);
                $this->flag = false;
                return;
            }
        }
        if (strlen($this->data[$sp[0]]) < $sp[1] * 1) {
            $this->errors[] = $this->lang($rule, $sp[0], $sp[1] * 1);
            $this->flag = false;
        }
    }

    /**
     * Максимальная длина строки
     */
    private function max_length($rule, $field)
    {
        $sp = explode(':', $field);
        if (!isset($this->data[$sp[0]]) or count($sp) < 2 or in_array('', $sp)) {
            throw new ValidateException('Не указано значение для - ' . $field, 404);
        }
        if (function_exists('mb_strlen')) {
            if (mb_strlen($this->data[$sp[0]]) > $sp[1] * 1) {
                $this->errors[] = $this->lang($rule, $sp[0], $sp[1] * 1);
                $this->flag = false;
                return;
            }
        }
        if (strlen($this->data[$sp[0]]) > $sp[1] * 1) {
            $this->errors[] = $this->lang($rule, $sp[0], $sp[1] * 1);
            $this->flag = false;
        }
    }

    /**
     * Максимальное число
     */
    private function max_int($rule, $field)
    {
        $sp = explode(':', $field);
        if (!isset($this->data[$sp[0]]) or count($sp) < 2 or in_array('', $sp) or !is_numeric($this->data[$sp[0]])) {
            throw new ValidateException('Что то не так - ' . $field, 404);
        }
        if ((abs($this->data[$sp[0]]) * 1) > ($sp[1] * 1)) {
            $this->errors[] = $this->lang($rule, $sp[0], $sp[1] * 1);
            $this->flag = false;
        }
    }

    /**
     * Минимальное число
     */
    private function min_int($rule, $field)
    {
        $sp = explode(':', $field);
        if (!isset($this->data[$sp[0]]) or count($sp) < 2 or in_array('', $sp) or !is_numeric($this->data[$sp[0]])) {
            throw new ValidateException('Что то не так - ' . $field, 404);
        }
        if ((abs($this->data[$sp[0]]) * 1) < ($sp[1] * 1)) {
            $this->errors[] = $this->lang($rule, $sp[0], $sp[1] * 1);
            $this->flag = false;
        }
    }

    /**
     * Убедитесь, что поле является действительным IP-адресом
     *
     */
    private function ip($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!filter_var($this->data[$field], \FILTER_VALIDATE_IP)) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Убедитесь, что поле является действительным IPv4-адресом
     *
     */
    private function ipv4($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!filter_var($this->data[$field], \FILTER_FLAG_IPV4)) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Убедитесь, что поле является действительным IPv6-адресом
     *
     */
    private function ipv6($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!filter_var($this->data[$field], \FILTER_FLAG_IPV6)) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Убедитесь, что поле содержит только символы ASCII
     */
    private function ascii($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        // multibyte extension needed
        if (function_exists('mb_detect_encoding')) {
            if (!mb_detect_encoding($this->data[$field], 'ASCII', true)) {
                $this->errors[] = $this->lang($rule, $field);
                $this->flag = false;
            }
        } else {
            if (0 !== preg_match('/[^\x00-\x7F]/', $field)) {
                $this->errors[] = $this->lang($rule, $field);
                $this->flag = false;
            }
        }
    }


    /**
     * Убедитесь, что поле содержит только символы UTF-8
     */
    private function utf8($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        // multibyte extension needed
        if (function_exists('mb_detect_encoding')) {
            if (!mb_detect_encoding($this->data[$field], 'UTF-8', true)) {
                $this->errors[] = $this->lang($rule, $field);
                $this->flag = false;
            }
        } else {
            throw new ValidateException('Не установлено расширение mbstring, требующееся для поля - ' . $field, 404);
        }
    }

    /**
     * Убедитесь, что поле является валидным адресом электронной почты
     */
    private function email($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (1 !== preg_match('#^[a-zA-Z0-9._\-]+@[a-z\-]{1,10}\.[a-z]{1,10}$#', $this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Убедитесь, что поле является валидным адресом электронной почты и имя домена активно
     */
    private function emailDNS($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }

        $this->email($rule, $field);

        $domain = ltrim(stristr($this->data[$field], '@'), '@') . '.';
        if (function_exists('idn_to_ascii') && defined('INTL_IDNA_VARIANT_UTS46')) {
            $domain = idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46);
        }
        if (!checkdnsrr($domain, 'ANY')) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Проверить, что поле является допустимым URL-адресом по синтаксису
     */
    private function url($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        $flag = false;
        foreach ($this->validUrlPrefixes as $prefix) {
            if ((strpos($this->data[$field], $prefix) === 0)) {
                $flag = true;
            }
        }
        if (1 === preg_match('#^(?:(ht|f)tp(s?):\/\/)?[\w.-]+(?:\.[\w\.-]+)+#', $this->data[$field])) {
            if ($flag) {
                $flag = true;
            }
        } else {
            $flag = false;
        }
        if (!$flag) {
            $this->errors[] = $this->lang($rule, $field);
        }
        $this->flag = $flag;
    }

    /**
     * Проверьте, что поле является активным URL, проверив запись DNS
     */
    private function url_active($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        $flag = false;
        foreach ($this->validUrlPrefixes as $prefix) {
            if ((strpos($this->data[$field], $prefix) === 0)) {
                $flag = true;
            }
        }
        $host = parse_url(strtolower($this->data[$field]), PHP_URL_HOST);
        if (checkdnsrr($host, 'A') or checkdnsrr($host, 'AAAA') or checkdnsrr($host, 'CNAME')) {
            if ($flag) {
                $flag = true;
            }
        } else {
            $flag = false;
        }
        if (!$flag) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Убедитесь, что поле содержит только буквенно-цифровые символы, тире и подчеркивания
     */
    private function slug($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (1 !== preg_match('/^([-a-z0-9_-])+$/i', $this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Проверьте, что поле проходит проверку регулярным выражением
     * $v->rule('regex', ['field_name:#[a-z]+#']);
     * $v->rules(['regex' => ['field_name:#[a-z]+#']]);
     */
    private function regex($rule, $field)
    {
        $sp = explode(':', $field);
        if (!isset($this->data[$sp[0]]) or count($sp) < 2 or in_array('', $sp)) {
            throw new ValidateException('Что то не так - ' . $field, 404);
        }
        if (1 !== preg_match($sp[1], $this->data[$sp[0]])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Проверить, что поле содержит число, похожее на номер кредитной карты
     */
    private function credit_card($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        $digit = str_replace(' ', '', $this->data[$field]);
        $number = strrev(preg_replace('/[^\d]/', '', $digit));
        $sum = 0;
        for ($i = 0, $j = strlen($number); $i < $j; $i++) {
            if (($i % 2) == 0) {
                $val = $number[$i];
            } else {
                $val = $number[$i] * 2;
                if ($val > 9) {
                    $val -= 9;
                }
            }
            $sum += $val;
        }
        if ((($sum % 10) === 0) === false) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }


    private function float($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (1 !== preg_match('/^\d*\.\d*$/', $this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    private function array($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!is_array($this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    private function bool($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!is_bool($this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Поле обязательно к заполнению
     */
    private function required($rule, $field)
    {
        if (!isset($this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
            return;
        }
        $str = str_replace(' ', '', $this->data[$field]);
        if ($str == '' or $str == [] or $str === null or $str === false) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Строка содержащая цифры и/или латинские буквы
     */
    private function alnum($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!ctype_alnum($this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * должно содержать только латинские символы
     */
    private function alpha($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!ctype_alpha($this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * должно быть цыфрами или строкой содержащей цыфры
     */
    private function numeric($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!is_numeric($this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Проверяет, все ли символы в переданной строке $field являются управляющими символами.
     * Управляющими символами, к примеру, являются перевод строки, символ табуляции, escape.
     */
    private function cntrl($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!ctype_cntrl($this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * должно быть строкой содержащей цыфры
     */
    private function digit($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!ctype_digit($this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Проверяет, все ли символы в переданной строке $field являются печатными.
     */
    private function print($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!ctype_print($this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }

    /**
     * Проверяет, являются ли все символы в строке $field шестнадцатеричными 'цифрами'.
     */
    private function xdigit($rule, $field)
    {
        if (!isset($this->data[$field])) {
            throw new ValidateException('Не нашёл поля - ' . $field, 404);
        }
        if (!ctype_xdigit($this->data[$field])) {
            $this->errors[] = $this->lang($rule, $field);
            $this->flag = false;
        }
    }


    public function getData(): array
    {
        return $this->data;
    }
}




















