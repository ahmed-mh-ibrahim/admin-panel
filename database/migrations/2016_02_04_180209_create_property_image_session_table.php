<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyImageSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("Property_images_session", function(BluePrint $table){
            $table->increments('id');
            $table->longText('src');
            $table->integer('property_id')->unsigned();
            $table->boolean("cover")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("Property_images_session");
    }
}
