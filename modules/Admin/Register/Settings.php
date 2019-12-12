<?php
//namespace Modules\Register;
/*
* Необходимо зарегестрировать модуль в \Core\Settings\Base->modules
* В этих настройках можно переопределить любые базовые настройки,
* а так же дописать свои
*/


//class Config
//{
//
//    use \Core\Helpers\ToArray;

    return [
        'routes' => [
            'GET'  => [
                'admin/register' => 'Modules\Register\Controllers\Register@createAdmin@admin.register',
            ],
            'POST' => [
                'admin/register/store' => 'Modules\Register\Controllers\Register@storeAdmin@admin.register.store',
            ]
        ]
    ];
//}

//return (new \Modules\Register\Config())->toArray();