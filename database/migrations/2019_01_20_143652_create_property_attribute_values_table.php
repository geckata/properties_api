<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyAttributeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_attribute_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id')->unsigned();
            $table->mediumInteger('property_attribute_name_id')->unsigned();
            $table->text('value');
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('property_attribute_name_id')->references('id')->on('property_attribute_names');
            $table->unique(['property_id', 'property_attribute_name_id'], 'property_attribute_values_unique_composite_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_attribute_values');
    }
}
