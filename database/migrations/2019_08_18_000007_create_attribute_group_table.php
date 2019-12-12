<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class AttributeGroup implements \Core\Migrations\Migration {

    public function up()
    {
        Capsule::schema()->create('attribute_group', function (Blueprint $table){
            $table->increments('id');
            $table->string('title');
        });
        Capsule::select("ALTER TABLE attribute_group COMMENT = 'Группа фильтров'");
    }


    public function down()
    {
        Capsule::schema()->dropIfExists('attribute_group');
    }
}