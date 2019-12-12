<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class AttributeValue implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('attribute_value', function (Blueprint $table){
            $table->increments('id');
            $table->string('value');
            $table->unsignedInteger('attr_group_id');

            $table->index(["attr_group_id"], 'attr_group_id');
            $table->unique(["value"], 'value');
        });
        Capsule::select("ALTER TABLE attribute_value COMMENT = 'Фильтры. Фильтры относятся к группе фильтров'");
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('attribute_value');
    }
}