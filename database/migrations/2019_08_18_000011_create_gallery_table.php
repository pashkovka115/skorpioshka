<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class Gallery implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('gallery', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->string('img');
        });
        Capsule::select("ALTER TABLE gallery COMMENT = 'Изображения товара'");
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('gallery');
    }
}