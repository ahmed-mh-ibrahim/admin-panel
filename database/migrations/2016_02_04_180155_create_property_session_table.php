<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertySessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Property_session', function (Blueprint $table) {
            /*
            relation property_images,property_tags
            tables: tags,country,city,province
*/
            $table->increments('id');
            $table->string('name');
            $table->string('property_type');
            $table->string('property_condition');
            $table->text('property_description');
            $table->string('address')->nullable();
            $table->string('city');
            $table->string('province');
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->float('latitude');
            $table->float('longitude');
            $table->float('selling_price');
            $table->float('land_size');
            $table->float('building_size')->nullable();
            $table->integer("bedrooms")->unsigned();
            $table->integer("bathrooms")->unsigned();
            $table->integer("parkings")->unsigned();
            $table->string("certificate");
            $table->string("orientation");
            $table->string("furnish_condition");
            $table->string("status")->default("pending");
            $table->integer("users_id")->unsigned()->default(1);
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("Property_session");
    }
}
