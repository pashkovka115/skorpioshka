<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class Modification implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('modification', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->string('title')->comment('Название модификации');
            $table->float('price')->default('0')->comment('Цена модификации');
        });
        Capsule::select("ALTER TABLE modification COMMENT = 'Варианты товара'");
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('modification');
    }
}