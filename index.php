<?php
define('ACCESS', true);

require __DIR__ . '/vendor/autoload.php';

header('Content-Type: text/html;charset=utf-8');
session_save_path(__DIR__ . '/storage/sessions');
session_start(['cookie_httponly' => true]);
session_clear();


new \Core\Db\Db();
\Extensions\Sessions\Session::referrer();
\Core\Router::getInstance()->request();


//dump();
//echo '<pre>';
//print_r($rc->getMethods());
//echo '</pre>';