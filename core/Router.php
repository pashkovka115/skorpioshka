<?php


namespace Core;

//https://elisdn.ru/blog/47/routing-in-frameworks-url-managing

use Core\Exceptions\RouteException;
use Core\Helpers\Arrays;
use Core\Settings\Base;
use Core\System\Traits\Singleton;
use Extensions\Sessions\Session;
use Modules\Module;
use Modules\Moduler;

class Router
{
    use Singleton;

    /**
     * @var $query
     * Строка запроса без параметров
     */
    private $query;
    /**
     * @var $params
     * Параметры в строке запроса
     */
    private $params = false;
//    private $module;
    /**
     * @var $admin_dir bool
     * Админская часть или пользовательская
     */
    private $admin_dir;
    /**
     * @var $current_module
     * Имя модуля который будет выполнен
     */
    private $current_module;

    /**
     * @var $merge_settings array
     * Склеенные настройки согласно которых будет
     * работать сайт
     */
    private $merge_settings;


    public function request()
    {
        \Extensions\Sessions\Session::checkSession();

        $split = explode('?', trim($_SERVER['REQUEST_URI'], '/'));
        $this->query = $split[0] == '' ? '/' : trim($split[0]);
        $this->params = isset($split[1]) ? $split[1] : false;
        $modules = Base::get('modules');
        $admin_alias = Base::get('admin_alias');

        // Если admin в QUERY грузим модули ADMIN иначе FRONT
        if (strpos($this->query, $admin_alias) === 0){
            $modules = $modules['ADMIN'];
        }else{
            $modules = $modules['FRONT'];
        }



        $query_split = explode('/', $this->query);

        // если это не админка
        if ($query_split[0] != $admin_alias) {
            $this->admin_dir = false;
            $this->current_module = ($query_split[0] == '') ? '/' : $query_split[0];
        } // если это админка
        elseif ($query_split[0] == $admin_alias) {
            $this->admin_dir = true;
            $this->current_module = '/';
            if (isset($query_split[1])) {
                $this->current_module = $query_split[1];
            }
        }

        // Записываем данные запроса в базовые настройки (виртуально)
        Base::set('request', Request::getInstance());


        // Если модуль существует
        // у него должны быть настройки Settings.php !!!
        if (isset($modules[$this->current_module])) {
            if ($this->admin_dir){
                $module_path = MODULES . '/Admin/' . $modules[$this->current_module];
                $module_path = str_replace('//', '/', $module_path);
            }else{
                $module_path = MODULES . '/Front/' . $modules[$this->current_module];
                $module_path = str_replace('//', '/', $module_path);
            }

            define('CURRENT_MODULE_PATH', $module_path);
            $module_settings = require $module_path . '/Settings.php';
            $this->merge_settings = Arrays::array_merge_recursive(Base::getInstance()->toArray(), $module_settings);

        } else {
//            Session::referrer_clear();
            throw new RouteException('Неизвестный модуль - ' . $this->current_module, 404);
        }

        // Выбираем маршруты согласно методу запроса
        if (isset($this->merge_settings['routes'][$_SERVER['REQUEST_METHOD']])) {
            $routes = $this->merge_settings['routes'][$_SERVER['REQUEST_METHOD']];
        } else {
            throw new RouteException('Нет секции ' . $_SERVER['REQUEST_METHOD'] . ' в описании маршрутов', 404);
        }

        // Запускаем метод модуля согласно маршрутов в настройках
        foreach ($routes as $pattern => $class_str) {

            // Заменяем именнованный плейсхолдер на подмаску регулярного выражения
            $sp_pat = explode('/', $pattern);
            $sp_q = explode('/', $this->query);
            $vars = [];
            for ($i = 0; $i < count($sp_pat); $i++) {
                if (1 === preg_match('#\{(\w+:.*)?}#', $sp_pat[$i], $matches11)){

                    $sp_pat[$i] = preg_replace('#\{(\w+:\[)(.*)(\])}#', '$2', $sp_pat[$i]);

                    if (isset($matches11[1])){
                        // $sp_mat[0] - имя переменной
                        // $sp_mat[1] - регулярка для значения
                        $sp_mat = explode(':[', $matches11[1]);
                        $sp_mat[1] = rtrim($sp_mat[1], ']');

                        if (isset($sp_q[$i]) and 1 === preg_match("#$sp_mat[1]#", $sp_q[$i])){
                            $vars[$sp_mat[0]] = $sp_q[$i];
                        }

                    }
                }
            }
            // конец подмаски

            $pattern = implode('\/', $sp_pat);

            if (1 === preg_match("#^$pattern$#i", $this->query, $matches)) {

                $sp = explode('@', $class_str);
                if (count($sp) < 2 or $sp[1] == '') {
                    throw new RouteException('Не указано имя метода', 404);
                }
                $class = $sp[0];
                $method = $sp[1];

                $rc = new \ReflectionClass($class);
                if ($rc->implementsInterface(Moduler::class)) {
                    $obj = $rc->newInstance();
                    $meth = $rc->getMethod($method);

                    $response = $this->getResponse();
                    $response->setBody($meth->invokeArgs($obj, $vars));
                    $response->printPage();

                    return;
                } else {
                    throw new RouteException('Модуль: ' . $class . ' не унаследован от "Module"', 404);
                }

                break;
            }
        }


        // todo: Класс request принять запрос, предоставить валидатор, функционал по вызову в контроллере
        throw new RouteException('Не описан маршрут для секции ' . $_SERVER['REQUEST_METHOD'], 404);
    }


    public function redirect($http = false, $code = 301)
    {
        if ($http) { // todo: тестить
            $redirect = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/' . $http;
        } else { // todo: тестить
            $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
            header('Location: ' . $redirect);
        }
        http_response_code($code);
        header('Location: ' . $redirect);
    }


    /**
     * @return bool
     * Это админ панель?
     */
    public function isAdminDir(): bool
    {
        return $this->admin_dir;
    }


    public function getQuery()
    {
        return $this->query;
    }

    public function getAllQuery()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function getRequest()
    {
        return Base::get('request');
    }

    public function getResponse()
    {
        return Response::getInstance();
    }


    public function getParams()
    {
        return $this->params;
    }


    public function getMergeSettings($key = false)
    {
        $i = self::getInstance()->merge_settings;
        if ($key and isset($i[$key])) {
            return $i[$key];
        }
        return $i;
    }
}
