<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class Category implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('category', function (Blueprint $table){
            $table->increments('id');
            $table->string('title');
            $table->string('alias');
            $table->unsignedTinyInteger('parent_id')->default('0');
            $table->string('keywords')->nullable()->default(null)->comment('Мета данные');
            $table->string('description')->nullable()->default(null)->comment('Мета данные');

            $table->unique(["alias"], 'alias');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('category');
    }
}