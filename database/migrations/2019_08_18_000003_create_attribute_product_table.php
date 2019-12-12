<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class AttributeProduct implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('attribute_product', function (Blueprint $table){
            $table->increments('attr_id');
            $table->unsignedInteger('product_id');
        });
        Capsule::select("ALTER TABLE attribute_product COMMENT = 'Связь атрибуты группы атрибутов'");
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('attribute_product');
    }
}