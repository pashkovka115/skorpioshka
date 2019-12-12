<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class RelatedProduct implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('related_product', function (Blueprint $table){
            $table->increments('product_id');
            $table->unsignedInteger('related_id')->comment('Связанный продукт');
        });
        Capsule::select("ALTER TABLE related_product COMMENT = 'С товарами также покупают эти'");
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('related_product');
    }
}