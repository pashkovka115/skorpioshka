<?php
/*
* Необходимо зарегестрировать модуль в \Core\Settings\Base->modules
* В этих настройках можно переопределить базовые настройки,
* а так же дописать свои
*/
return [
    'routes' => [
        'GET'  => [
            'admin/login' => '\Modules\Admin\Login\Controllers\Login@createAdmin',
        ],
        'POST' => [
            'admin/login/check' => '\Modules\Admin\Login\Controllers\Login@checkAdmin@admin.auth',
        ]
    ]
];