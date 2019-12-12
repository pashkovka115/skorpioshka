<?php


namespace Core;


use Core\System\Traits\Singleton;

class Response
{
    use Singleton;

    private $headers = [];
    private $cookies = [];
    private $body;
    /**
     * @var array
     * Куки которые НЕ надо шифровать
     */
    private $cookie_excluded = [];


    public function __construct()
    {
        $this->cookie_excluded = [
            session_name()
        ];
    }




    public function setCookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = true)
    {
        $this->cookies[] = [
            'name'     => $name,
            'value'    => $this->parseCookie($name, $value),
            'expire'   => $expire,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httponly
        ];
    }

    private function parseCookie($name, $value)
    {
        if (!in_array($name, $this->cookie_excluded))
            return encryptthis($value);
        else
            return $value;
    }


    public function addHeader(string $header, int $http_response_code = 200, bool $replace = true): void
    {
        $this->headers[] = [
            'header' => $header,
            'replace'=>$replace,
            'code'=>$http_response_code
        ];
    }


    public function setBody($body): void
    {
        $this->body = $body;
    }

    public static function sessionDestroy(){
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }


    public function printPage()
    {
        foreach ($this->cookies as $cookie) {
            setcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
        }
        foreach ($this->headers as $header) {
            header($header['header'], $header['replace'], $header['code']);
            if (strpos($header['header'], 'Location') !== false){
                exit();
            }
        }
        echo $this->body;
    }
}

























