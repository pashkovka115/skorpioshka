<?php

namespace Core\Settings;

use Core\Middleware\Admin;
use Core\Request;
use Core\System\Traits\Singleton;
use Modules\CommonModels\User;

class Base
{
    use Singleton;

    /**
     * @var array $routes
     * Сюда модули записывают свои маршруты (программно/виртуально)
     * через свои настройки
     */
    private $routes = [];


    /**
     * @var string $admin_alias
     * Префикс урла админ панели
     * используется как контрольное слово при парсинге урла.
     * Также с этого слова должен начинаться маршрут
     * администраторской части сайта.
     * Если это слово необходимо изменить, то это надо сделать
     * до начала разработки сайта.
     */
    private $admin_alias = 'admin';

    /**
     * @var array $modules
     * Список модулей по шаблону
     * start-url => ModuleName
     * Для урла http://site.ru/catalog/var1/var2/var3
     * FRONT => [catalog => ModuleName]
     *
     * Для урла http://site.ru/admin/catalog/var1/var2/var3
     * ADMIN => [catalog => ModuleName]
     *
     * Модуль может быть сделан, как для одной страницы,
     * так и для логически связанных страниц.
     * Эта настройка является приоритетной
     * при диспетчеризации маршрутов
     *
     * Для главной страницы должен быть модуль '/'
     * Необходимо зарегестрировать вновь созданный модуль
     *
     * FRONT - Пользовательская часть сайта
     * ADMIN - Администраторская часть сайта
     */
    private $modules = [
        'FRONT' => [
            '/' => 'Home',
        ],
        'ADMIN' => [
            '/' => 'Home',
            'register' => 'Register',
            'login'    => 'Login',
            'logout'   => 'Logout',
        ]
    ];

    /**
     * @var array
     * Настройки расширений
     */
    /*private $extensions = [
        'GenerateView' => [
            'teachers'=>[

            ]
        ]
    ];*/

    /**
     * @var array $middleware
     * Список классов посредников выполняемых
     * между запросом и приложением
     */
    private $middleware = [
        \Core\Middleware\Trim::class,
        \Core\Middleware\Csrf::class,
    ];

    /**
     * @var array $route_middleware
     * Список классов посредников выполняемых
     * между запросом и приложением
     * Для определённых маршрутов
     */
    private $route_middleware = [
        '^admin(?!/login).*$' => Admin::class
    ];

    /**
     * @var $request Request
     * Используется системой
     * Не рекомендуется переопределять!!! Но нет не чего невозможного))
     */
    private $request;

    /**
     * @var string $users
     * Таблица пользователей в БД
     */
//    private $users = 'users';

    /**
     * @var int $cookieLifetime
     * Время жизни сессии авторизованного пользователя
     * в секундах
     */
    private $cookieLifetime = 14400;

    /**
     * @var int $remember
     * На какое время запомнить пользователя
     * если отмечен чекбокс "запомнить меня"
     * в секундах
     */
    private $remember = 8640000;

    /**
     * @var \Core\Models\Model::class
     * Модель пользователей
     */
    private $user = User::class;

    /**
     * @var string
     * Поле в таблице $user по которому
     * проверяется уникальность пользователя
     * Возможно переопределение в модуле.
     */
    private $user_login    = 'email';
    /**
     * @var string
     * Поле в таблице $user по которому
     * проверяется пароль пользователя
     * Возможно переопределение в модуле.
     */
    private $user_password = 'password';

    /**
     * @var array
     * Настройки пагинации.
     * Возможно для каждого модуля своя пагинация.
     * Возможно переопределение в модуле.
     */
    private $pagConfig = [
        'perPage'            => 10,
        'numLinks'           => 4,
        'firstLink'          => 'Первая',
        'nextLink'           => 'Следующая &raquo;',
        'prevLink'           => '&laquo; Предыдущая',
        'lastLink'           => 'Последняя',
        'fullTagOpen'        => '<div class="pagination">',
        'fullTagClose'       => '</div>',
        'firstTagOpen'       => '',
        'firstTagClose'      => '&nbsp;',
        'lastTagOpen'        => '&nbsp;',
        'lastTagClose'       => '',
        'curTagOpen'         => '&nbsp;<b>',
        'curTagClose'        => '</b>',
        'nextTagOpen'        => '&nbsp;',
        'nextTagClose'       => '&nbsp;',
        'prevTagOpen'        => '&nbsp;',
        'prevTagClose'       => '',
        'numTagOpen'         => '&nbsp;',
        'numTagClose'        => '',
        'showCount'          => true,
        'queryStringSegment' => 'page',
    ];


    /**
     * @param $property
     * Возвращает настройку|false
     */
    static public function get($property)
    {
        $i = self::getInstance();
        if (isset($i->$property))
            return $i->$property;
        return false;
    }

    /**
     * @param $property
     * @param $value
     * Присвоить настройке новое значение. Внимательно!!!
     */
    static public function set($property, $value)
    {
        self::getInstance()->$property = $value;
    }


    public function toArray()
    {
        $arr = [];
        $obj = self::getInstance();
        foreach ($obj as $property => $value) {
            $arr[$property] = $value;
        }
        return $arr;
    }
}





























