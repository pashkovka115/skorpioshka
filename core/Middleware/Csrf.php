<?php


namespace Core\Middleware;

use Core\Exceptions\CsrfException;

class Csrf extends Middleware
{
    public function transform($data)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' and (!isset($data['_csrf']) or decryptthis($data['_csrf']) != APP_KEY)){
            throw new CsrfException('Не идентифицированный запрос CSRF', 404);
        }
        return $data;
    }
}