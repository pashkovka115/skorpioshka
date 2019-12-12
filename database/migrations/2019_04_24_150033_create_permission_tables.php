<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


class CreatePermissionTables implements \Core\Migrations\Migration
{

    public function up()
    {
        if (!Capsule::schema()->hasTable('permissions')){
            Capsule::schema()->create('permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Capsule::schema()->hasTable('roles')) {
            Capsule::schema()->create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Capsule::schema()->hasTable('user_has_roles')) {
            Capsule::schema()->create('user_has_roles', function (Blueprint $table) {
                $table->unsignedInteger('role_id');

//            $table->string('model_type');
                $table->unsignedBigInteger('user_id');
                $table->index(['user_id']);

                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

                $table->primary(['role_id', 'user_id'],
                    'user_has_roles_role_primary');
            });
        }

        if (!Capsule::schema()->hasTable('role_has_permissions')) {
            Capsule::schema()->create('role_has_permissions', function (Blueprint $table) {
                $table->unsignedInteger('permission_id');
                $table->unsignedInteger('role_id');

                $table->foreign('permission_id')
                    ->references('id')
                    ->on('permissions')
                    ->onDelete('cascade');

                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

                $table->primary(['permission_id', 'role_id']);
            });
        }

        if (Capsule::schema()->hasTable('users')){
            Capsule::schema()->table('users', function (Blueprint $table){
                $table->tinyInteger('super_admin', false, true)->default(0);
            });
        }else{
            exit('Отсутствует таблица USERS');
        }
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('role_has_permissions');
        Capsule::schema()->dropIfExists('user_has_roles');
        Capsule::schema()->dropIfExists('roles');
        Capsule::schema()->dropIfExists('permissions');
    }
}
