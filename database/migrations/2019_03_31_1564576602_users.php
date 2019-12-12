<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class Users implements \Core\Migrations\Migration {
    public function up()
    {
        Capsule::schema()->create('users', function (Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
});
    }
    
    
    public function down()
    {
        Capsule::schema()->dropIfExists('users');
    }
}