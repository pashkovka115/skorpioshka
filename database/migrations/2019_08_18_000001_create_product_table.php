<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class Product implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('product', function (Blueprint $table){
            $table->increments('id');
            $table->unsignedTinyInteger('category_id');
            $table->unsignedTinyInteger('brand_id');
            $table->string('title');
            $table->string('alias');
            $table->text('content')->nullable()->default(null);
            $table->float('price')->default('0');
            $table->float('old_price')->default('0');
            $table->enum('status', ['0', '1'])->default('1')->comment('Показывать или нет');
            $table->string('keywords')->nullable()->default(null)->comment('Мета данные');
            $table->string('description')->nullable()->default(null)->comment('Мета данные');
            $table->string('img')->default('no_image.jpg');
            $table->enum('hit', ['0', '1'])->default('0')->comment('Хит, популярный, самый продоваемый');

            $table->index(["category_id", "brand_id"], 'category_id');
            $table->index(["hit"], 'hit');
            $table->unique(["alias"], 'alias');
        });
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('product');
    }
}