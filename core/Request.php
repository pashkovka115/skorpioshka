<?php


namespace Core;


use Core\Exceptions\RouteException;
use Core\Helpers\Validator;
use Core\Settings\Base;
use Core\System\Traits\Singleton;
use Extensions\Sessions\Session;


class Request
{
    use Singleton;

    private $transform_request = [];
    private $headers           = [];
    private $query;
    private $params            = false;
    private $ip;
    private $cookie            = [];


    private function __construct()
    {
        $this->headers = apache_request_headers();
        $this->query = Router::getInstance()->getQuery();
        $this->params = Router::getInstance()->getParams();
        $this->ip = isset($_SERVER['HTTP_CLIENT_IP']) ?
            $_SERVER['HTTP_CLIENT_IP'] :
            isset($_SERVER['HTTP_X_FORWARDED_FOR']) ?
                $_SERVER['HTTP_X_FORWARDED_FOR'] :
                $_SERVER['REMOTE_ADDR'];
//        $this->cookie = $_COOKIE;
        foreach ($_COOKIE as $k_c => $cook) {
            $dec = decryptthis($cook);
            if (is_Base64Encoded($cook) and $dec) {
                $this->cookie[$k_c] = $dec;
            } else {
                $this->cookie[$k_c] = $cook;
            }
        }

        if (count($_POST) > 0) {
            $this->transform_request = $_POST;
//            var_dump($_POST);
            Session::setInputDate($_POST); // todo тестить
        } elseif (count($_GET) > 0) {
            $this->transform_request = $_GET;
        }

        $middlewares = Base::get('middleware');

        foreach ($middlewares as $middleware) {
            $obj = new $middleware();
            $this->transform_request = $obj->transform($this->transform_request);
        }

        $route_middlewares = Base::get('route_middleware');
        $query = Router::getInstance()->getQuery();

        foreach ($route_middlewares as $pattern => $route_middleware) {
            if (1 === preg_match("#$pattern#i", $query, $matches)) {
                $obj = new $route_middleware();
                $this->transform_request = $obj->transform($this->transform_request);
            }
        }

    }


    public function input($name, $default = '')
    {
        if (isset($this->transform_request[$name]))
            return $this->transform_request[$name];
        return $default;
    }


    /**
     * @return array
     * Входные POST / GET данные
     */
    public function getAllInputs()
    {
        return $this->transform_request;
    }


    /**
     * @return string
     * Строка параметров
     */
    public function getParams()
    {
        return Router::getInstance()->getParams();
    }

    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($name)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }
        return null;
    }

    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    public function ip()
    {
        return $this->ip;
    }

    public function getCookie($key)
    {
        if (isset($this->cookie[$key]))
            return $this->cookie[$key];
        return false;
    }

    public function getCookies()
    {
        return $this->cookie;
    }

    public function hasCookie($key)
    {
        return isset($this->cookie[$key]);
    }
}


















