<?php
/*
* Необходимо зарегестрировать модуль в \Core\Settings\Base->modules
* В этих настройках можно переопределить базовые настройки,
* а так же дописать свои
*/
return [
    'routes' => [
        'GET' => [
            'admin/logout' => '\Modules\Admin\Logout\Controllers\Logout@logout@admin.logout',
        ]
    ]
];