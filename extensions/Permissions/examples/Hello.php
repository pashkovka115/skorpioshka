<?php
namespace Modules\Home;


use Extensions\Permissions\Permission;
use Extensions\Permissions\Role;
use Modules\Module;

class Hello extends Module
{
    public function index()
    {
        echo __METHOD__;


//        dump(Permission::All());
//        dump(Role::getInstance()->permissions());

        /*dump(Role::getInstance()->create([
            'name' =>'role-admin',
            'description'=>'Admin'
        ]));*/

        /*dump(Role::firstOrCreate([
            'name' =>'role-admin2',
            'description'=>'Admin'
        ]));*/


//        dump(Role::getInstance()->getPermissionsByIdRole(3));
//        dump(Role::getInstance()->createRole(['name'=>'role-admin3']));
//        dump(Permission::getInstance()->allPermissions());

        // 'edit articles'
        // ['publish articles', 'unpublish articles']
        // Permission::all()
//        dump(Role::getInstance()->find(2)->givePermissionTo(['editor', 'moderator']));
//        dump(Role::getInstance()->find(2)->givePermissionTo(['publish articles', 'unpublish articles']));
//        dump(Role::getInstance()->find(2)->givePermissionTo(['super-admin', 'admin', 'editor']));
//        dump(Role::getInstance()->find(2)->givePermissionTo(Permission::getInstance()->allPermissions()));
//        dump(Role::getInstance()->find(2)->givePermissionTo(Permission::all()));
//        dump(Role::getInstance()->find(2)->hasPermissionTo('super-admin'));
//        dump(Role::getInstance()->getPermissionsByIdRole(2));
//        dump(Role::getInstance()->getPermissionsByNameRole('role-admin'));
//        dump(Role::getInstance()->find(2)->revokePermissionTo('super-admin'));
//        dump(Permission::getInstance()->drop('admin'));
//        dump(Role::getInstance()->dropRole('role-admin'));
//        dump(Permission::find(1)->assignRole('role-admin2'));
//        dump(Permission::getInstance()->getPermissionsById(10));
//        dump(Permission::find(4)->syncRoles(['role-admin3']));
//        dump(Permission::find(4)->revokeRoleTo('role-admin3'));
//        dump(Permission::getInstance()->allPermissions());






    }
    public function first()
    {
        echo __METHOD__;
    }
}