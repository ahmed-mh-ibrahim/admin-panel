<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('mobile_number')->unique();
            $table->longText('avater_image')->nullable();
            $table->integer("property_post_qty")->unsigned()->default(0);
            $table->string('password', 60);
            $table->string('company_name')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_website')->nullable();
            $table->integer("user_type")->unsigned()->default(1);
            $table->boolean("is_admin")->default(false);
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
        Schema::drop('users');
    }
}
