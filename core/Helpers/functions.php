<?php

use Core\Exceptions\RouteException;


/**
 * @return \Extensions\Authorization\Auth
 * @throws \Core\Exceptions\AuthException
 * Возвращает информацию об авторизованном пользователе
 */
function auth(){
    return \Extensions\Authorization\Auth::getInstance();
}

/**
 * Вызывает сборщика мусора для сессий
 */
function session_clear()
{
    $gc_time = SITE_PATH . '/storage/sessions/php_session_last_gc';
    $gc_period = 1800;

    if (file_exists($gc_time)) {
        if (filemtime($gc_time) < time() - $gc_period) {
            session_gc();
            touch($gc_time);
        }
    } else {
        touch($gc_time);
    }
}

/**
 * @return string
 * Возвращает адресс сайта
 * Рекомендуется использовать эту функцию
 * вместо константы SITE_URL
 */
function get_url(string $path = '')
{
    $url = SITE_URL;
    $url = rtrim($url, '/');
    if (strlen($path) > 0) {
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
    }
    return $url . $path;
}

/**
 * @param string $route Имя маршрута Settings.php->routes->...Controller@method@route.name
 * @param string $module Имя модуля (папки с модулем)
 * @param array $params параметры (индексированный масив) которые необходимы для маршрута согласно карты маршрутиризации Settings.php[routes]
 * @throws RouteException
 * Генерирует адрес ссылки по имени маршрута
 * В пределах одного модуля имя маршрута должно быть уникальным
 */
function route(string $route, string $module, array $params = [])
{
    $path_admin = MODULES . '/Admin/' . $module . '/Settings.php';
    $path_front = MODULES . '/Front/' . $module . '/Settings.php';
    $rout_front = [];
    $rout_admin = [];
    if (file_exists($path_admin)){
        $rout_admin = (require $path_admin)['routes'];
    }
    if (file_exists($path_front)){
        $rout_front = (require $path_front)['routes'];
    }

    $routes = $rout_admin + $rout_front;

    foreach ($routes as $methods) {
        foreach ($methods as $r => $action) {
            $sp = explode('@', $action);
            if (isset($sp[2]) and $sp[2] == $route) {

                $sp_r = explode('/', $r);

                if (preg_match_all('#([^/]?)\{(\w+:\[)(.*?)(\])}#', $r, $matches)) {
                    if (isset($matches[0])) {
                        $i = 0;
                        foreach ($sp_r as $item) {
                            $j = 0;
                            foreach ($matches[0] as $match) {
                                if ($match == $item) {
                                    if (isset($params[$j])) {

                                        $match = rtrim(ltrim($match, '{'), '}');
                                        $match = rtrim(explode(':[', $match)[1], ']');

                                        if (isset($params[$j]) and 1 === preg_match("#^" . $match . '$#', $params[$j])) {
                                            $sp_r[$i] = $params[$j];
                                        } else {
                                            throw new RouteException('ERROR Параметр не соответствует регулярке', 404);
                                        }
                                    } else {
                                        $sp_r[$i] = '';
                                    }
                                }
                                $j++;
                            }
                            $i++;
                        }

                    }
                }

                $link = rtrim(implode('/', $sp_r), '/');
                echo '/' . $link;
                return;
            }
        }
    }
}

/**
 * @param bool|string [$key]
 * @return array|string
 * Возвращает ранее введённые данные пользователем в форму
 */
function old($key = false)
{
    return \Extensions\Sessions\Session::getInputDate($key);
}

/**
 * @param $data
 * @param $key
 * @return string
 * Шифрует строку с помощью ключа
 */
function encryptthis($data, $key = APP_KEY)
{
    $encryption_key = base64_decode($key);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

/**
 * @param $data
 * @param $key
 * @return string
 * Расшифровывает строку с помощью ключа
 */
function decryptthis($data, $key = APP_KEY)
{
    $encryption_key = base64_decode($key);
    if (!$encryption_key) {
        return $data;
    }
    list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

/**
 * @param $encodedString
 * @return bool
 * Является ли строка закодированной base64
 */
function is_Base64Encoded($encodedString)
{
    $length = strlen($encodedString);

// Check every character.
    for ($i = 0; $i < $length; ++$i) {
        $c = $encodedString[$i];
        if (
            ($c < '0' || $c > '9')
            && ($c < 'a' || $c > 'z')
            && ($c < 'A' || $c > 'Z')
            && ($c != '+')
            && ($c != '/')
            && ($c != '=')
        ) {
// Bad character found.
            return false;
        }
    }
// Only good characters found.
    return true;
}

function csrf_field()
{
    echo '<input type="hidden" name="_csrf" value="' . encryptthis(APP_KEY) . '">';
}

if (!function_exists('get_rus_date')) {
    function get_rus_date($dateTime, $format = '%DAYWEEK%, d %MONTH% Y H:i', $offset = 0)
    {
        $monthArray = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
        $daysArray = array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье');

        $timestamp = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dateTime)->timestamp; // todo: переделать на time()
        $timestamp += 3600 * $offset;

        $findArray = array('/%MONTH%/i', '/%DAYWEEK%/i');
        $replaceArray = array($monthArray[date("m", $timestamp) - 1], $daysArray[date("N", $timestamp) - 1]);
        $format = preg_replace($findArray, $replaceArray, $format);

        return date($format, $timestamp);
    }
}
/**
 * Такое время назад
 */
if (!function_exists('someTimeAgo') and !function_exists('dimension')) {
    function someTimeAgo($time)
    { // Определяем количество и тип единицы измерения
        $time = time() - strtotime($time);
        if ($time < 60) {
            return 'меньше минуты назад';
        } elseif ($time < 3600) {
            return dimension((int)($time / 60), 'i');
        } elseif ($time < 86400) {
            return dimension((int)($time / 3600), 'G');
        } elseif ($time < 2592000) {
            return dimension((int)($time / 86400), 'j');
        } elseif ($time < 31104000) {
            return dimension((int)($time / 2592000), 'n');
        } elseif ($time >= 31104000) {
            return dimension((int)($time / 31104000), 'Y');
        }
    }

    function dimension($time, $type)
    { // Определяем склонение единицы измерения
        $dimension = array(
            'n' => ['месяцев', 'месяц', 'месяца', 'месяц'],
            'j' => ['дней', 'день', 'дня'],
            'G' => ['часов', 'час', 'часа'],
            'i' => ['минут', 'минуту', 'минуты'],
            'Y' => ['лет', 'год', 'года']
        );
        if ($time >= 5 && $time <= 20)
            $n = 0;
        else if ($time == 1 || $time % 10 == 1)
            $n = 1;
        else if (($time <= 4 && $time >= 1) || ($time % 10 <= 4 && $time % 10 >= 1))
            $n = 2;
        else
            $n = 0;
        return $time . ' ' . $dimension[$type][$n] . ' назад';

    }
}
/**
 * @param $number число вещей
 * @param $after варианты склонения
 *
 * Склонение русских слов
 *
 * варианты написания для количества 1, 2 и 5
 * plural_form($number, ['ответ', 'ответа', 'ответов'])
 */
function plural_form($number, $after)
{
    $cases = [2, 0, 1, 1, 1, 2];
    return $number . ' ' . $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
}

/**
 * Перенаправление назад
 * или на указанный адрес todo: тестить
 */
function back($forcibly = false)
{
    if (isset($_SERVER['HTTP_REFERER']) and $_SERVER['HTTP_REFERER'] !== null){
        $url = $_SERVER['HTTP_REFERER'];
    }else{
        $url = get_url(\Extensions\Sessions\Session::getReferrer());
    }
    if ($forcibly){
        header('Location: ' . $url);
    }else{
        \Core\Response::getInstance()->addHeader('Location: ' . $url);
    }
}

/**
 * @param bool $http
 * @param int $code
 * Редирект на указанный адрес (без протокола и домена)
 * или на главную по умолчанию
 */
function redirect($url = '', $code = 301, $forcibly = false)
{
    if ($forcibly){
        header('Location: ' . get_url($url), $code);
    }else{
        \Core\Response::getInstance()->addHeader('Location: ' . get_url($url), $code);
    }
}

/**
 * @param $status string
 * Возвращает ошибки для вида
 */
function errors()
{
    $i = \Extensions\Sessions\Session::getError();
    \Extensions\Sessions\Session::destroyError();
    return $i;
}

function has_errors()
{
    return \Extensions\Sessions\Session::hasError();
}

function set_error(string $error){
    \Extensions\Sessions\Session::setError($error);
}

function info()
{
    $i = \Extensions\Sessions\Session::getInfo();
    \Extensions\Sessions\Session::destroyInfo();
    return $i;
}

function set_info($message)
{
    \Extensions\Sessions\Session::setInfo($message);
}

function has_info()
{
    return \Extensions\Sessions\Session::hasInfo();
}

/**
 * @param string [$key]
 * @return array|string
 * Настройки модуля которые будут применены к системе
 */
function settings($key = false)
{
    return \Core\Router::getInstance()->getMergeSettings($key);
}

/**
 * @return false|string
 * Время сейчас
 */
function now()
{
    return date('Y-m-d H:i:s');
}

// file.key
/**
 * @param $string
 * @return string
 * Статический перевод
 */
function __($string)
{
    $sp = explode('.', $string);
    $f1 = LANG . config('app')['lang'] . $sp[0] . '.php';
    $f2 = LANG . config('app')['default_lang'] . $sp[0] . '.php';
    if (file_exists($f1)) {
        $words = require $f1;
        if (count($sp) == 2) {
            if (isset($words[$sp[1]])) {
                return $words[$sp[1]];
            } elseif (file_exists($f2)) {
                $words = require $f2;
                if (isset($words[$sp[1]])) {
                    return $words[$sp[1]];
                }
            }
        }
    }
    return '-----';
}

/**
 * @param $file
 * @return array
 * Конфигурация системы
 */
function config($file)
{
    return require CONFIG_PATH . '/' . $file . '.php';
}

/**
 * @param $view
 * @param array $params
 * @return string
 * Возвращает результирующее представление
 * Представление ищется от site.com/views
 */
function view($view, array $params = [])
{
    $view = SITE_PATH . '/views/' . $view;
    return \Core\View::getInstance()->view($view, $params);
}

/**
 * @param $view
 * Возвращает представление в стандартный поток
 * Удобно использовать внутри представлений.
 * Например для подключения одного шаблона внутри другого.
 */
function template($view, array $params = [])
{
    $view = SITE_PATH . '/views/' . $view;
    echo \Core\View::getInstance()->view($view, $params);
}

/**
 * @param $view
 * @param array $params
 * Система склеивает шаблон с переменными
 * и запоминает его
 */
function render($view, $params = [])
{
    $view = SITE_PATH . '/views/' . $view;
    $v = \Core\View::getInstance();
    $v->render($view, $params);
}

/**
 * @param $view
 * @return string
 * Возвращает шаблон если он был добавлен ранее
 */
function getTemplate($view)
{
    return \Core\View::getInstance()->getTemplate($view);
}

/**
 * @param string $path
 * @return string /public
 */
function asset($path = '')
{
    if (strlen($path) > 0) {
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
    }
    echo get_url('/public' . $path);
}


