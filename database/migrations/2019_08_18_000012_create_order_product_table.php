<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class OrderProduct implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('order_product', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('order_id')->comment('Номер заказа');
            $table->unsignedInteger('product_id');
            $table->integer('qty')->comment('Количество');
            $table->string('title')->comment('Имя товара на случай если изменится');
            $table->float('price')->comment('Цена товара на случай если изменится');

            $table->index(["order_id"], 'order_id');

            $table->foreign('order_id', 'order_id')
                ->references('id')->on('order')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Capsule::select("ALTER TABLE order_product COMMENT = 'Заказанные товары'");
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('order_product');
    }
}