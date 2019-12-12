<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class Permissions
{
    public function run()
    {
        \Extensions\Permissions\Permission::create(['name' => 'all','description' => 'можно всё',]);
        \Extensions\Permissions\Permission::getInstance()->createPermission(['name' => 'edit','description' => 'редактировать',]);
        \Extensions\Permissions\Permission::getInstance()->createPermission(['name' => 'read','description' => 'читать',]);
        \Extensions\Permissions\Permission::getInstance()->createPermission(['name' => 'write','description' => 'писать',]);
        \Extensions\Permissions\Permission::getInstance()->createPermission(['name' => 'create','description' => 'создавать',]);

        \Extensions\Permissions\Role::getInstance()->createRole(['name'=>'super-admin', 'description'=>'Супер админ']);
        \Extensions\Permissions\Role::getInstance()->createRole(['name'=>'moderator', 'description'=>'Модератор, проверяющий']);
        \Extensions\Permissions\Role::getInstance()->createRole(['name'=>'writer', 'description'=>'Писатель, создаёт']);

        \Extensions\Permissions\Role::find(1)->givePermissionTo(\Extensions\Permissions\Permission::all());
        \Extensions\Permissions\Role::find(2)->givePermissionTo(['edit', 'read']);

        \Modules\CommonModels\User::find(1)->giveRoles(\Extensions\Permissions\Role::all());
        \Modules\CommonModels\User::find(2)->giveRoles(['moderator']);
        \Modules\CommonModels\User::find(3)->giveRoles(['writer']);
    }
}