<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class Currency implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('currency', function (Blueprint $table){
            $table->increments('id');
            $table->string('title', 50)->comment('Человеческое название');
            $table->string('code', 3)->comment('Код');
            $table->string('symbol_left', 10)->comment('Отображать слева');
            $table->string('symbol_right', 10)->comment('Отображать справа');
            $table->float('value')->comment('Индекс к базовой валюте (курс)');
            $table->enum('base', ['0', '1'])->comment('Базовая или нет');
        });
        Capsule::select("ALTER TABLE currency COMMENT = 'Валюта'");
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('currency');
    }
}