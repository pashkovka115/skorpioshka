<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class Order implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('order', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->enum('status', ['0', '1'])->default('0')->comment('Активный, в стадии выполнения или нет');
            $table->timestamp('date')->default(Capsule::table('order')->raw('CURRENT_TIMESTAMP'))->comment('Когда сделан заказ');
            $table->timestamp('update_at')->nullable()->default(null)->comment('Когда заказ выполнен');
            $table->string('currency', 10)->comment('В какой валюте зделан заказ');
            $table->text('note')->nullable()->default(null)->comment('Примечания к заказу');
        });

        Capsule::select("ALTER TABLE `order` COMMENT = 'Информация о заказе'");
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('order');
    }
}