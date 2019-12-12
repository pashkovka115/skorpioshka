<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class Brand implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('brand', function (Blueprint $table){
            $table->increments('id');
            $table->string('title');
            $table->string('alias');
            $table->string('img')->default('brand_no_image.jpg');
            $table->string('keywords')->nullable()->default(null)->comment('Мета данные');
            $table->string('description')->nullable()->default(null)->comment('Мета данные');

            $table->unique(["alias"], 'alias');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('brand');
    }
}