<?php


namespace Core\Exceptions;


use Core\Helpers\Log;
use Throwable;

class BaseException extends \Exception
{
    use Log;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (!DEBUG) {
            error_reporting(0);
            $mess = include 'messages.php';
            if (isset($mess[$code])) {
                $message = $mess[$code];
                $this->message = $mess[$code];
            }
            parent::__construct($message, $code, $previous);

            $error = $message;
            $error .= "\n" . ' = File: ' . $this->getFile() . ' = Line: ' . $this->getLine() . "\n Query: " . $_SERVER['REQUEST_URI'] . "\n";

            $this->writeLog($error);
            require 'views/default.php';
            if ($code > 99 and $code < 600) {
                http_response_code($code);
            } else {
                http_response_code(404);
            }
        } else {
            error_reporting(E_ALL);
            $mess = include 'messages.php';
            parent::__construct($message, $code, $previous);
            echo '<h4>' . $this->getMessage() . ' - ' . $mess[$code] . ' - ' . $code . '</h4>';
        }

    }
}